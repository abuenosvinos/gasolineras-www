<?php

namespace App\Domain\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Infrastructure\Doctrine\Repository\StationRepository")
 */
class Station
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $province;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $municipality;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $location;

    /**
     * @ORM\Column(type="string", length=5)
     */
    private $postal_code;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $address;

    /**
     * @ORM\Column(type="float")
     */
    private $lng;

    /**
     * @ORM\Column(type="float")
     */
    private $lat;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $label;

    /**
     * @ORM\Column(type="string", length=5)
     */
    private $rem;

    /**
     * @ORM\Column(type="string", length=5)
     */
    private $sale_type;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $schedule;

    /**
     * @ORM\ManyToOne(targetEntity="App\Domain\Entity\File", inversedBy="stations")
     * @ORM\JoinColumn(nullable=false)
     */
    private $file;

    /**
     * @ORM\OneToMany(targetEntity="App\Domain\Entity\Price", mappedBy="station", orphanRemoval=true, cascade={"remove"})
     */
    private $prices;

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

    public function __construct()
    {
        $this->prices = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProvince(): ?string
    {
        return $this->province;
    }

    public function setProvince(string $province): self
    {
        $this->province = $province;

        return $this;
    }

    public function getMunicipality(): ?string
    {
        return $this->municipality;
    }

    public function setMunicipality(string $municipality): self
    {
        $this->municipality = $municipality;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(string $location): self
    {
        $this->location = $location;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postal_code;
    }

    public function setPostalCode(string $postal_code): self
    {
        $this->postal_code = $postal_code;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getLng(): ?float
    {
        return $this->lng;
    }

    public function setLng(float $lng): self
    {
        $this->lng = $lng;

        return $this;
    }

    public function getLat(): ?float
    {
        return $this->lat;
    }

    public function setLat(float $lat): self
    {
        $this->lat = $lat;

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getRem(): ?string
    {
        return $this->rem;
    }

    public function setRem(string $rem): self
    {
        $this->rem = $rem;

        return $this;
    }

    public function getSaleType(): ?string
    {
        return $this->sale_type;
    }

    public function setSaleType(string $sale_type): self
    {
        $this->sale_type = $sale_type;

        return $this;
    }

    public function getSchedule(): ?string
    {
        return $this->schedule;
    }

    public function setSchedule(string $schedule): self
    {
        $this->schedule = $schedule;

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

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function setFile(?File $file): self
    {
        $this->file = $file;

        return $this;
    }

    /**
     * @return Collection|Price[]
     */
    public function getPrices(): Collection
    {
        return $this->prices;
    }

    public function addPrice(Price $price): self
    {
        if (!$this->prices->contains($price)) {
            $this->prices[] = $price;
            $price->setStation($this);
        }

        return $this;
    }

    public function removePrice(Price $price): self
    {
        if ($this->prices->contains($price)) {
            $this->prices->removeElement($price);
            // set the owning side to null (unless already changed)
            if ($price->getStation() === $this) {
                $price->setStation(null);
            }
        }

        return $this;
    }
}
