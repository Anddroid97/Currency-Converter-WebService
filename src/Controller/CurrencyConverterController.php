<?php

namespace App\Controller;

use App\Entity\CurrencyExchangeRate;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Anddroid97\CurrencyConverter\CurrencyConverter;
use Symfony\Component\HttpFoundation\Response;

class CurrencyConverterController extends FOSRestController
{
    /**
     * Convert currency.
     *
     * @Rest\Get("/converter/convertFrom={currencyFrom}&convertTo={currencyTo}&amount={amount}")
     *
     * @param string $currencyFrom
     * @param string $currencyTo
     * @param int $amount
     * @return \FOS\RestBundle\View\View
     */
    public function getConvertResult(string $currencyFrom, string $currencyTo,int $amount)
    {
        $model = $this->getDoctrine()
            ->getRepository(CurrencyExchangeRate::class)
            ->findOneBy(['CurrencyForConvert'=> $currencyFrom, 'ConvertedCurrency' => $currencyTo]);

        if(empty($model)) {
            $response = ['error' => ['message' => \sprintf('Resource with currency %s or currency %s not found! You must write currency in form e.g. USD', $currencyFrom, $currencyTo )],
                        'links' => [ 'see all possible converting variants ' => '/api/all_currencies',
                                     'add new conversion' => '/api/add_conversion',
                                   ]
                        ];
            return $this->view($response, Response::HTTP_NOT_FOUND);
        }

        $currencyConverter = new CurrencyConverter();

        $currencyConverter->convertFrom($model->getCurrencyForConvert());
        $currencyConverter->convertTo($model->getConvertedCurrency());
        $currencyConverter->setExchangeRate($model->getExchangeRate());
        $result = $currencyConverter->getConvertResult($amount);
        $readableResult = $currencyConverter->getBeautyConvertResult($amount);

        $response = [
          'result' => [
              'converterResult' => $result,
              'readable result' => $readableResult,
              'attributes' => [
                  'convertFrom' => $currencyConverter->getFromCurrency(),
                  'convertTo' => $currencyConverter->getToCurrency(),
                  'exchangeRates' => $currencyConverter->checkExchangeRate()
              ],
              'links'=> [
                    'see all possible converting variants ' => '/api/all_currencies',
                    'see exchange rate for given Currencies ' => \sprintf('/api/possible_conversions/%s', $currencyFrom),
                    'add new conversion' => '/add_conversion',
                    'update exchange rates' => \sprintf('/api/update_exchange_rate/from=%s&currencyTo=%s', $currencyFrom, $currencyTo)
              ]
          ]
        ];

        return $this->view($response, Response::HTTP_OK);
    }

    /**
     * Get All possible currencies.
     *
     * @Rest\Get("/all_currencies")
     *
     */
    public function getAllPossibleCurrencies()
    {
        $currencies = $this->getDoctrine()->getRepository(CurrencyExchangeRate::class)->findAll();

        if (empty($currencies)) {
            $response = $response = ['error' => ['message' => 'Have not existed any currencies yet'],
                'links' => ['add new conversion' => '/api/add_conversion']
            ];
            return $this->view($response, Response::HTTP_NOT_FOUND);
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

        return $this->view($response, Response::HTTP_OK);
    }

    /**
     * Return possible conversions of given currency
     *
     * @Rest\Get("/possible_conversions/{currency}")
     *
     * @param string $currency
     * @return \FOS\RestBundle\View\View
     */
    public function getExchangeRatesByGivenCurrency(string $currency)
    {
        $currencies = $this->getDoctrine()
            ->getRepository(CurrencyExchangeRate::class)
            ->findBy(['CurrencyForConvert' => $currency]);

        if (empty($currency)) {
            $response = ['error' => ['message' => \sprintf('Resource with currency %s not found! You must write currency in form e.g. USD', $currency)],
                'links' => ['see all possible converting variants ' => '/api/all_currencies',
                    'add new conversion' => '/api/add_conversion',
                ]
            ];
            return $this->view($response, Response::HTTP_NOT_FOUND);
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

        return $this->view($response, Response::HTTP_OK);
    }

    public function addConversion()
    {

    }
}