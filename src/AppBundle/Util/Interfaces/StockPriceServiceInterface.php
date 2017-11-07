<?php
namespace AppBundle\Util\Interfaces;

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
     * @param $symbol
     * @param $date
     * @return mixed
     */
    public function getStockInformation($symbol, $date);

    /**
     * @param $symbol
     * @return mixed
     */
    public function getSymbolLastStoredClosingPrice($symbol);
}
