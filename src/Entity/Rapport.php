<?php

namespace App\Entity;

use App\Repository\RapportRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RapportRepository::class)
 */
class Rapport
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $file;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nameColumnX;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nameColumnY;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $typeChart;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $chartDesc;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $price;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $descPrice;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFile(): ?string
    {
        return $this->file;
    }

    public function setFile(string $file): self
    {
        $this->file = $file;

        return $this;
    }

    public function getNameColumnX(): ?string
    {
        return $this->nameColumnX;
    }

    public function setNameColumnX(?string $nameColumnX): self
    {
        $this->nameColumnX = $nameColumnX;

        return $this;
    }

    public function getNameColumnY(): ?string
    {
        return $this->nameColumnY;
    }

    public function setNameColumnY(?string $nameColumnY): self
    {
        $this->nameColumnY = $nameColumnY;

        return $this;
    }

    public function getTypeChart(): ?string
    {
        return $this->typeChart;
    }

    public function setTypeChart(?string $typeChart): self
    {
        $this->typeChart = $typeChart;

        return $this;
    }

    public function getChartDesc(): ?string
    {
        return $this->chartDesc;
    }

    public function setChartDesc(?string $chartDesc): self
    {
        $this->chartDesc = $chartDesc;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getDescPrice(): ?string
    {
        return $this->descPrice;
    }

    public function setDescPrice(?string $descPrice): self
    {
        $this->descPrice = $descPrice;

        return $this;
    }
}
