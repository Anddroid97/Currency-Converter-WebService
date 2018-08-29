<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CurrencyExchangeRateRepository")
 */
class CurrencyExchangeRate
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $CurrencyForConvert;

    /**
     * @ORM\Column(type="float")
     */
    private $ExchangeRate;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $ConvertedCurrency;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCurrencyForConvert(): ?string
    {
        return $this->CurrencyForConvert;
    }

    public function setCurrencyForConvert(string $CurrencyForConvert): self
    {
        $this->CurrencyForConvert = $CurrencyForConvert;

        return $this;
    }

    public function getExchangeRate(): ?float
    {
        return $this->ExchangeRate;
    }

    public function setExchangeRate(float $ExchangeRate): self
    {
        $this->ExchangeRate = $ExchangeRate;

        return $this;
    }

    public function getConvertedCurrency(): ?string
    {
        return $this->ConvertedCurrency;
    }

    public function setConvertedCurrency(string $ConvertedCurrency): self
    {
        $this->ConvertedCurrency = $ConvertedCurrency;

        return $this;
    }
}
