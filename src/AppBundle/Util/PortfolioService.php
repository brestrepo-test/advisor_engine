<?php
namespace AppBundle\Util;

use AppBundle\Entity\Portfolio;
use AppBundle\Entity\Stock;
use AppBundle\Entity\Transaction;
use AppBundle\Util\Interfaces\StockPriceServiceInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class PortfolioService
 */
class PortfolioService implements Interfaces\PortfolioInterface
{
    /**
     * @var StockPriceServiceInterface
     */
    private $stockPriceService;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * PortfolioService constructor.
     *
     * @param EntityManagerInterface     $entityManager
     * @param StockPriceServiceInterface $stockPriceService
     */
    public function __construct(EntityManagerInterface $entityManager, StockPriceServiceInterface $stockPriceService)
    {

        $this->stockPriceService = $stockPriceService;
        $this->entityManager = $entityManager;
    }

    /**
     * @param array $portfolio
     */
    public function updatePortfolioValue($portfolio)
    {
        foreach ($portfolio as &$item) {
            $today = new \DateTime();
            if ($item->getLastUpdate() !== $today) {
                $this->updatePortfolio($item->getSymbol());
            }
        }
    }

    /**
     * Updates the portfolio PL for a specific symbol
     *
     * @param Stock $stock
     */
    public function updatePortfolio(Stock $stock)
    {
        $transactions = $this->entityManager->getRepository('AppBundle:Transaction')->findBy(['symbol' => $stock->getSymbol()]);
        $price = $this->stockPriceService->getSymbolLastStoredClosingPrice($stock->getSymbol());
        list($pl, $amountOwned, $totalInvestment) = $this->calculatePLAndAmount($transactions, $price);

        $portfolio = $this->entityManager->getRepository('AppBundle:Portfolio')->findOneBy(['symbol' => $stock->getSymbol()]);

        if (empty($portfolio)) {
            $portfolio = new Portfolio();
        }

        $portfolio->setSymbol($stock);
        $portfolio->setAmount($amountOwned);
        $portfolio->setPl($pl);
        $portfolio->setLastUpdate(new \DateTime());
        $portfolio->setTotalInvestment($totalInvestment);

        $this->entityManager->persist($portfolio);
        $this->entityManager->flush();
    }

    /**
     * @param array $transactions
     * @param float $price
     *
     * @return array
     */
    private function calculatePLAndAmount($transactions, $price)
    {
        $amountOwned = 0;
        $transactionPL = [];

        foreach ($transactions as $transaction) {
            $operation = ($transaction->getOperation() == Transaction::OPERATION_BUY) ? 1 : -1;
            $amountTransaction = $transaction->getAmount() * $operation;

            if ($amountOwned+$amountTransaction > 0) {
                $amountOwned += $amountTransaction;
                $transactionPL[] =  ($price * $transaction->getAmount()) - ($transaction->getUnitPrice() * $transaction->getAmount());
            } else {
                //If there is a point when we sell all from a stock we start the count again
                //Obviously a full version would have controls that prevent you from selling stock you don't own
                //But in this case we want to keep it simple and allow you to do that.
                $transactionPL = [];
                $amountOwned = 0;
            }
        }

        $totalInvestment = $price * $amountOwned;

        return [ array_sum($transactionPL), $amountOwned, $totalInvestment ];
    }
}
