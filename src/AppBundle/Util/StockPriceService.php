<?php
namespace AppBundle\Util;

use AppBundle\Entity\Stock;
use AppBundle\Entity\StockClosingPrice;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * Class StockPriceService
 */
class StockPriceService implements Interfaces\StockPriceServiceInterface
{
    const BATCH_INSERT_CHUNK = 100;

    /**
     * @var Interfaces\FinancialServiceClientInterface
     */
    private $client;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * StockPriceService constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param Interfaces\FinancialServiceClientInterface $client
     */
    public function __construct(EntityManagerInterface $entityManager, Interfaces\FinancialServiceClientInterface $client)
    {
        $this->entityManager = $entityManager;
        $this->client = $client;
    }

    /**
     * Updates stock closing prices for a list of symbols
     *
     * @param array $symbols
     *
     * @throws \Exception
     */
    public function updateStockClosingPriceForSymbols(array $symbols)
    {
        foreach ($symbols as $symbol) {
            $lastClosingPrice = $this->getSymbolLastStoredClosingPriceDate($symbol);
            $yesterday = (new \DateTime())->sub(new \DateInterval('P1D'));
            
            //We only want to update if the last closing price we have is not from yesterday
            if ((is_null($lastClosingPrice)) || ($lastClosingPrice->format('Y-m-d') !== $yesterday->format('Y-m-d'))) {
                try {
                    $historicPrices = $this->client->fetchDailyStockTimeSeries($symbol, $lastClosingPrice);
                } catch (\Exception $e) {
                    throw new \Exception("{$symbol} cant be fetched", 0, $e);
                }

                $stock = $this->findOrCreateStock($symbol);
                $this->batchInsertStockClosingPrices($stock, $historicPrices);
            }
        }
    }

    /**
     * @param string $symbol
     *
     * @return mixed
     */
    public function getSymbolLastStoredClosingPrice($symbol)
    {
        $lastClosingPrice = $this->entityManager
            ->getRepository('AppBundle\Entity\StockClosingPrice')
            ->findOneBy(['symbol' => $symbol], ['date' => 'DESC']);

        return (!empty($lastClosingPrice)) ? $lastClosingPrice->getPrice() : null;
    }

    /**
     * Gets the information for a stock
     *
     * @param string $symbol
     * @param \DateTime $date
     *
     * @return array
     *
     * @throws \Exception
     */
    public function getStockInformation($symbol, $date)
    {
        $this->updateStockClosingPriceForSymbols([ $symbol ]);

        $stock = $this->findOrCreateStock($symbol);
        $price = $this->getSymbolPriceForDate($symbol, $date);

        return [ $stock, $price ];
    }

    /**
     * Get a symbol price for a date
     *
     * @param string $symbol
     * @param \DateTime $date
     *
     * @return null
     */
    public function getSymbolPriceForDate($symbol, $date)
    {
        $price = $this->entityManager
            ->getRepository('AppBundle\Entity\StockClosingPrice')
            ->findOneBy(['symbol' => $symbol, 'date' => $date]);

        return (!empty($price)) ? $price->getPrice() : null;
    }

    /**
     * @param string $symbol
     *
     * @return mixed
     */
    private function getSymbolLastStoredClosingPriceDate($symbol)
    {
        $lastClosingPrice = $this->entityManager
            ->getRepository('AppBundle\Entity\StockClosingPrice')
            ->findOneBy(['symbol' => $symbol], ['date' => 'DESC']);

        return (!empty($lastClosingPrice)) ? $lastClosingPrice->getDate() : null;
    }

    /**
     * Retrieves and creates if needed a stock
     *
     * @param string $symbol
     *
     * @return Stock|array
     */
    private function findOrCreateStock($symbol)
    {
        $stock = $this->entityManager
            ->getRepository('AppBundle\Entity\Stock')
            ->findOneBy(array('symbol' => $symbol));

        if (empty($stock)) {
            $stock = new Stock();
            $stock->setSymbol($symbol);
            $this->entityManager->persist($stock);
            $this->entityManager->flush();
        }

        return $stock;
    }

    /**
     * Batch inserts the historic of closing prices for a stock
     *
     * @param Stock $stock
     *
     * @param array $historicPrices
     */
    private function batchInsertStockClosingPrices($stock, $historicPrices)
    {
        foreach ($historicPrices as $index => $historicPrice) {

            $stockClosingPrice = new StockClosingPrice();
            $stockClosingPrice->setDate($historicPrice->date);
            $stockClosingPrice->setSymbol($stock);
            $stockClosingPrice->setPrice($historicPrice->adjustedClose);

            $this->entityManager->merge($stockClosingPrice);

            if ($index% self::BATCH_INSERT_CHUNK === 0) {
                $this->entityManager->flush();
                $this->entityManager->clear();
            }
        }
        $this->entityManager->flush();
        $this->entityManager->clear();
    }
}
