<?php
namespace AppBundle\Util\Interfaces;

use AppBundle\Entity\Stock;

/**
 * Interface StockPriceServiceInterface
 */
interface StockPriceServiceInterface
{
    /**
     * @param array $symbols
     */
    public function updateStockClosingPriceForSymbols(array $symbols);

    /**
     * @param Stock $symbol
     * @param \DateTime $date
     *
     * @return mixed
     */
    public function getStockInformation($symbol, $date);

    /**
     * @param Stock $symbol
     *
     * @return mixed
     */
    public function getSymbolLastStoredClosingPrice($symbol);
}
