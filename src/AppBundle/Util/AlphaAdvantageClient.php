<?php
namespace AppBundle\Util;

use AppBundle\Model\HistoricClose;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

/**
 * Class AlphaAdvantageClient
s */
class AlphaAdvantageClient implements Interfaces\FinancialServiceClientInterface
{
    //TODO: In a larger scope project, these two constants belong in the settings and not on this class
    //For portability, we'll keep them here
    const API_KEY = 'L4UX7XH5DK4NFX7A';
    const TIME_SERIES_DAILY_ADJUSTED_URL = 'https://www.alphavantage.co/query?function=TIME_SERIES_DAILY_ADJUSTED';

    /**
     * @var Client
     */
    private $client;

    /**
     * AlphaAdvantageClient constructor.
     */
    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * Fetches the daily time series from alpha advantage
     *
     * @param string $symbol
     * @param null|\DateTime $fromDate
     *
     * @return array
     *
     * @throws \Exception
     */
    public function fetchDailyStockTimeSeries($symbol, $fromDate)
    {
        $uri = $this->getUri($symbol);

        if ((!is_null($fromDate)) && (!$fromDate instanceof \DateTime)) {
            throw new \Exception('From date must be an instance of DateTime or null');
        }

        try {
            $response = $this->client->request("GET", $uri);
        } catch (ClientException $e) {
            throw new \Exception("We were unable to retrieve the daily series for that symbol");
        }

        return $this->sortResults($symbol, json_decode($response->getBody()->getContents(), true), $fromDate);
    }

    /**
     * Returns the uri to fetch the daily time series for a specified symbol
     *
     * @param string $symbol
     *
     * @return string
     */
    private function getUri($symbol)
    {
        return implode(
            "&",
            [
                self::TIME_SERIES_DAILY_ADJUSTED_URL,
                "symbol={$symbol}",
                "apikey=".self::API_KEY,
                "datatype=json",
            ]
        );
    }

    /**
     * Returns the transformed results
     *
     * @param string $symbol
     * @param array $results
     * @param null|\DateTime $fromDate
     *
     * @return array
     *
     * @throws \Exception
     */
    private function sortResults($symbol, array $results, $fromDate)
    {
        if (!empty($results)) {
            if (strpos(strtolower(array_keys($results)[0]), 'error') !== false) {
                //You would hope the api would throw a 404 in this case. But we get an error message
                //with a 200 status code.
                //With more time, we would like to separate this exceptions into different types of exception
                //So we can catch easily what was the reason for the failure
                throw new \Exception("The symbol you requested is not valid");
            } else {
                return $this->transformResults($symbol, $results, $fromDate);
            }
        } else {
            throw new \Exception("The results obtained are empty or corrupted");
        }
    }

    /**
     * Transform the results from the response to Model\HistoricClose
     *
     * @param string $symbol
     * @param array $results
     * @param null|\DateTime $fromDate
     *
     * @return array
     *
     * @throws \Exception
     */
    private function transformResults($symbol, $results, $fromDate)
    {
        $historicCloses = [];
        if (empty($results['Time Series (Daily)'])) {
            throw new \Exception("The results are not in the format we expected");
        } else {
            foreach ($results['Time Series (Daily)'] as $date => $information) {
                $date = new \DateTime($date);
                if (empty($fromDate) || ($date > $fromDate)) {
                    $historicClose = new HistoricClose();
                    $historicClose->date = $date;
                    $historicClose->symbol = $symbol;
                    $historicClose->adjustedClose = floatval($information['5. adjusted close']);

                    $historicCloses[] = $historicClose;
                }
            }
        }

        return $historicCloses;
    }
}
