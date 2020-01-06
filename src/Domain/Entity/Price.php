<?php

namespace App\Domain\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass="App\Infrastructure\Doctrine\Repository\PriceRepository")
 */
class Price
{
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Domain\Entity\Station", inversedBy="prices")
     * @ORM\JoinColumn(nullable=false)
     */
    private $station;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $price_gasoline_95;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $price_diesel_a;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $price_diesel_b;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $price_new_diesel_a;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $price_gasoline_98;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $price_liquefied_petroleum_gas;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStation(): ?Station
    {
        return $this->station;
    }

    public function setStation(?Station $station): self
    {
        $this->station = $station;

        return $this;
    }

    public function getPriceGasoline95(): ?float
    {
        return $this->price_gasoline_95;
    }

    public function setPriceGasoline95(?float $price_gasoline_95): self
    {
        $this->price_gasoline_95 = $price_gasoline_95;

        return $this;
    }

    public function getPriceDieselA(): ?float
    {
        return $this->price_diesel_a;
    }

    public function setPriceDieselA(?float $price_diesel_a): self
    {
        $this->price_diesel_a = $price_diesel_a;

        return $this;
    }

    public function getPriceDieselB(): ?float
    {
        return $this->price_diesel_b;
    }

    public function setPriceDieselB(?float $price_diesel_b): self
    {
        $this->price_diesel_b = $price_diesel_b;

        return $this;
    }

    public function getPriceNewDieselA(): ?float
    {
        return $this->price_new_diesel_a;
    }

    public function setPriceNewDieselA(?float $price_new_diesel_a): self
    {
        $this->price_new_diesel_a = $price_new_diesel_a;

        return $this;
    }

    public function getPriceGasoline98(): ?float
    {
        return $this->price_gasoline_98;
    }

    public function setPriceGasoline98(?float $price_gasoline_98): self
    {
        $this->price_gasoline_98 = $price_gasoline_98;

        return $this;
    }

    public function getPriceLiquefiedPetroleumGas(): ?float
    {
        return $this->price_liquefied_petroleum_gas;
    }

    public function setPriceLiquefiedPetroleumGas(?float $price_liquefied_petroleum_gas): self
    {
        $this->price_liquefied_petroleum_gas = $price_liquefied_petroleum_gas;

        return $this;
    }
}
