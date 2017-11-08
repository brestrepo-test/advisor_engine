<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="portfolio",uniqueConstraints={@ORM\UniqueConstraint(name="symbol", columns={"symbol"})})
 */
class Portfolio
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Stock")
     * @ORM\JoinColumn(name="symbol", referencedColumnName="symbol")
     */
    private $symbol;

    /**
     * @ORM\Column(type="integer")
     */
    private $amount;

    /**
     * Profit/loss
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $pl;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     */
    private $totalInvestment;

    /**
     * @ORM\Column(type="date")
     */
    private $lastUpdate;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getSymbol()
    {
        return $this->symbol;
    }

    /**
     * @param mixed $symbol
     */
    public function setSymbol($symbol)
    {
        $this->symbol = $symbol;
    }

    /**
     * @return mixed
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param mixed $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * @return mixed
     */
    public function getPl()
    {
        return $this->pl;
    }

    /**
     * @param mixed $pl
     */
    public function setPl($pl)
    {
        $this->pl = $pl;
    }

    /**
     * @return mixed
     */
    public function getLastUpdate()
    {
        return $this->lastUpdate;
    }

    /**
     * @param mixed $lastUpdate
     */
    public function setLastUpdate($lastUpdate)
    {
        $this->lastUpdate = $lastUpdate;
    }

    /**
     * @return mixed
     */
    public function getTotalInvestment()
    {
        return $this->totalInvestment;
    }

    /**
     * @param mixed $totalInvestment
     */
    public function setTotalInvestment($totalInvestment)
    {
        $this->totalInvestment = $totalInvestment;
    }
}
