<?php
namespace AppBundle\Util\Interfaces;

/**
 * Interface FinancialServiceClientInterface
 */
interface FinancialServiceClientInterface
{
    /**
     * @param $symbol
     * @param $fromDate
     * @return array
     */
    public function fetchDailyStockTimeSeries($symbol, $fromDate);
}
