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
            $response = ['error' => ['message' => \sprintf('Resourse with currency %s or currency %s not found! You must write currency in form e.g. USD', $currencyFrom, $currencyTo )],
                        'links' => [ 'see all possible converting variants ' => '/all_currencies',
                                     'add new conversion' => '/add_conversion',
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
                    'see all possible converting variants ' => '/all_currencies',
                    'see exchange rate for given Currencies ' => \sprintf('/possible_conversions/%s', $currencyFrom),
                    'add new conversion' => '/add_conversion',
                    'update exchange rates' => \sprintf('/update_exchange_rate/from=%s&currencyTo=%s', $currencyFrom, $currencyTo)
              ]
          ]
        ];

        return $this->view($response, Response::HTTP_OK);
    }
}