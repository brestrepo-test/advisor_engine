<?php
namespace AppBundle\Util\Interfaces;

use AppBundle\Entity\Stock;

interface PortfolioInterface
{
    /**
     * @param $portfolio
     * @return mixed
     */
    public function updatePortfolioValue($portfolio);

    /**
     * @param $stock
     */
    public function updatePortfolio(Stock $stock);
}
