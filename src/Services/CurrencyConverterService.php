<?php

namespace App\Services;

use Anddroid97\CurrencyConverter\CurrencyConverter;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\CurrencyExchangeRate;

class CurrencyConverterService
{
    private $entityManager;
    private $currencyConverter;

    public function __construct(CurrencyConverter $currencyConverter, EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->currencyConverter = $currencyConverter;
    }

    public function getConvertResult(string $currencyFrom, string $currencyTo, int $amount): array
    {
        $model = $this->entityManager
            ->getRepository(CurrencyExchangeRate::class)
            ->findOneBy(['CurrencyForConvert' => $currencyFrom, 'ConvertedCurrency' => $currencyTo]);

        if(empty($model)) {
            $badResponse = ['error' => ['message' => \sprintf('Resource with currency %s or currency %s not found! You must write currency in form e.g. USD', $currencyFrom, $currencyTo )],
                'links' => [ 'see all possible converting variants ' => '/api/all_currencies',
                             'add new conversion' => '/api/add_conversion',
                ]
            ];
            return $badResponse;
        }

        $this->currencyConverter->convertFrom($model->getCurrencyForConvert());
        $this->currencyConverter->convertTo($model->getConvertedCurrency());
        $this->currencyConverter->setExchangeRate($model->getExchangeRate());
        $result = $this->currencyConverter->getConvertResult($amount);
        $readableResult = $this->currencyConverter->getBeautyConvertResult($amount);

        $response = [
            'result' => [
                'converterResult' => $result,
                'readable result' => $readableResult,
                'attributes' => [
                    'convertFrom' => $this->currencyConverter->getFromCurrency(),
                    'convertTo' => $this->currencyConverter->getToCurrency(),
                    'exchangeRates' => $this->currencyConverter->checkExchangeRate()
                ],
                'links'=> [
                    'see all possible converting variants ' => '/api/all_currencies',
                    'see exchange rate for given Currencies ' => \sprintf('/api/possible_conversions/%s', $currencyFrom),
                    'add new conversion' => '/api/add_conversion',
                    'update exchange rates' => \sprintf('/api/update_exchange_rate/currencyFrom=%s&currencyTo=%s', $currencyFrom, $currencyTo)
                ]
            ]
        ];

        return $response;
    }

    public function getAllPossibleCurrencies(): array
    {
        $currencies = $this->entityManager
            ->getRepository(CurrencyExchangeRate::class)
            ->findAll();

        if (empty($currencies)) {
            $badResponse = $response = ['error' => ['message' => 'Have not existed any currencies yet'],
                                        'links' => ['add new conversion' => '/api/add_conversion']
            ];
            return $badResponse;
        }

        $response = [];
        foreach ($currencies as $currency) {
            $response[] = [
                'result'=> [
                    'currencyToConvert' => $currency->getCurrencyForConvert(),
                    'convertedCurrency' => $currency->getConvertedCurrency(),
                    'exchangeRates' =>  $currency->getExchangeRate(),
                ],
                'links' => [
                    'see exchange rate for given Currency ' => \sprintf('/api/possible_conversions/%s',$currency->getCurrencyForConvert())
                ]
            ];
        }

        return $response;
    }

    public function getExchangeRatesByGivenCurrency(string $currency): array
    {
        $currencies = $this->entityManager
            ->getRepository(CurrencyExchangeRate::class)
            ->findBy(['CurrencyForConvert' => $currency]);

        if (empty($currency)) {
            $badResponse = ['error' => ['message' => \sprintf('Resource with currency %s not found! You must write currency in form e.g. USD', $currency)],
                            'links' => ['see all possible converting variants ' => '/api/all_currencies',
                                        'add new conversion' => '/api/add_conversion',
                ]
            ];
            return $badResponse;
        }

        $response = [];
        foreach ($currencies as $currency) {
            $response[] = [
                'result' => [
                    'currencyToConvert' => $currency->getCurrencyForConvert(),
                    'convertedCurrency' => $currency->getConvertedCurrency(),
                    'exchangeRates' => $currency->getExchangeRate(),
                ],
                'links' => [
                    'see all possible converting variants ' => '/api/all_currencies'
                ]
            ];
        }
        return $response;
    }

    public function addConversion(string $currencyFrom, string $currencyTo, float $exchangeRate): array
    {
        $model = $this->entityManager
            ->getRepository(CurrencyExchangeRate::class)
            ->findOneBy(['CurrencyForConvert'=> $currencyFrom, 'ConvertedCurrency' => $currencyTo]);

        if (empty($model)) {

            $conversion = new CurrencyExchangeRate();

            $conversion->setCurrencyForConvert($currencyFrom);
            $conversion->setConvertedCurrency($currencyTo);
            $conversion->setExchangeRate($exchangeRate);

            $this->entityManager->persist($conversion);
            $this->entityManager->flush();

            $response = [
                'result' => [
                    'message' => 'Success'
                ],
                'links' => [
                    'see exchange rate for given Currency' => \sprintf('/api/possible_conversions/%s', $conversion->getCurrencyForConvert()),
                    'convert currency' => \sprintf('/api/converter/convertFrom=%s&convertTo=%s&amount=setAmount', $conversion->getCurrencyForConvert(), $conversion->getConvertedCurrency()),
                    'delete this conversion' => \sprintf('/api/delete/currencyFrom=%s&currencyTo=%s',$conversion->getCurrencyForConvert(), $conversion->getConvertedCurrency()),
                    'delete all possible conversions of this currency' => \sprintf('/api/delete_all/currencyFrom=$s', $conversion->getCurrencyForConvert())
                ]
            ];

            return $response;
        }

        $badResponse = [
            'error' => [
                'message' => 'this exchange rate is already exist'
            ],
            'links' => [
                'see exchange rate for given Currency' => \sprintf('/api/possible_conversions/%s', $currencyFrom)
            ]
        ];

        return $badResponse;
    }

    public function updateExchangeRate(string $currencyFrom, string $currencyTo, float $exchangeRate): array
    {
        $model = $this->entityManager
            ->getRepository(CurrencyExchangeRate::class)
            ->findOneBy(['CurrencyForConvert'=> $currencyFrom, 'ConvertedCurrency' => $currencyTo]);

        if(empty($model)) {
            $badResponse = ['error' => ['message' => \sprintf('Resource with currency %s or currency %s not found! You must write currency in form e.g. USD', $currencyFrom, $currencyTo )],
                'links' => [ 'see all possible converting variants ' => '/api/all_currencies',
                             'add new conversion' => '/api/add_conversion',
                ]
            ];
            return $badResponse;
        }

        $model->setExchangeRate($exchangeRate);
        $this->entityManager->persist($model);
        $this->entityManager->flush();

        $response = [
            'message' => 'Success',
            'result'  => [
                'currencyToConvert' => $model->getCurrencyForConvert(),
                'convertedCurrency' => $model->getConvertedCurrency(),
                'exchangeRates' => $model->getExchangeRate(),
            ],
            'links' => [
                'convert currency' => \sprintf('api/converter/convertFrom=%s&convertTo=%s&amount=setAmount', $model->getCurrencyForConvert(), $model->getConvertedCurrency()),
                'see all possible converting variants ' => '/api/all_currencies'
            ]
        ];

        return $response;
    }

    public function deleteConversion(string $currencyFrom, string $currencyTo): array
    {

        $model = $this->entityManager
            ->getRepository(CurrencyExchangeRate::class)
            ->findOneBy(['CurrencyForConvert'=> $currencyFrom, 'ConvertedCurrency' => $currencyTo]);

        if(empty($model)) {
            $badResponse = ['error' => ['message' => \sprintf('Resource with currency %s or currency %s not found! You must write currency in form e.g. USD', $currencyFrom, $currencyTo )],
                'links' => [ 'see all possible converting variants ' => '/api/all_currencies',
                    'add new conversion' => '/api/add_conversion',
                ]
            ];
            return $badResponse;
        }

        $this->entityManager->remove($model);
        $this->entityManager->flush();

        return [];
    }

    public function deleteAllConversionByCurrency(string $currencyFrom): array
    {
        $currencies = $this->entityManager
            ->getRepository(CurrencyExchangeRate::class)
            ->findBy(['CurrencyForConvert' => $currencyFrom ]);

        if(empty($currencies)) {
            $badResponse = ['error' => ['message' => \sprintf('Resource with currency %s not found! You must write currency in form e.g. USD', $currencyFrom )],
                'links' => [ 'see all possible converting variants ' => '/api/all_currencies',
                             'add new conversion' => '/api/add_conversion',
                ]
            ];
            return $badResponse;
        }

        foreach ($currencies as $currency) {
            $this->entityManager->remove($currency);
            $this->entityManager->flush();
        }

        return [];
    }
}