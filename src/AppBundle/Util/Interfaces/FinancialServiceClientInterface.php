<?php
namespace AppBundle\Util\Interfaces;

/**
 * Interface FinancialServiceClientInterface
 */
interface FinancialServiceClientInterface
{
    /**
     * @param string $symbol
     * @param null|\DateTime $fromDate
     * 
     * @return array
     */
    public function fetchDailyStockTimeSeries($symbol, $fromDate);
}
