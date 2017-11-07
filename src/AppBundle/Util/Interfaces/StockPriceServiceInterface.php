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
}
