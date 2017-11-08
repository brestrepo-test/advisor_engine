<?php
namespace AppBundle\Util\Interfaces;

use AppBundle\Entity\Stock;

/**
 * Interface PortfolioInterface
 */
interface PortfolioInterface
{
    /**
     * @param array $portfolio
     *
     * @return mixed
     */
    public function updatePortfolioValue($portfolio);

    /**
     * @param Stock $stock
     */
    public function updatePortfolio(Stock $stock);
}
