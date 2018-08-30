<?php

namespace App\Controller;

use App\Services\CurrencyConverterService;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\HttpFoundation\Request;
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
     * @param CurrencyConverterService $currencyConverterService
     * @return \FOS\RestBundle\View\View
     */
    public function getConvertResult(string $currencyFrom, string $currencyTo,int $amount, CurrencyConverterService $currencyConverterService)
    {
       $response = $currencyConverterService->getConvertResult($currencyFrom,$currencyTo,$amount);

       if (\array_key_exists('error',$response))
           return $this->view($response, Response::HTTP_NOT_FOUND);

       return $this->view($response, Response::HTTP_OK);
    }

    /**
     * Get All possible currencies.
     *
     * @Rest\Get("/all_currencies")
     *
     * @param CurrencyConverterService $currencyConverterService
     * @return \FOS\RestBundle\View\View
     */
    public function getAllPossibleCurrencies(CurrencyConverterService $currencyConverterService)
    {
       $response = $currencyConverterService->getAllPossibleCurrencies();

       if (\array_key_exists('error',$response))
            return $this->view($response, Response::HTTP_NOT_FOUND);

       return $this->view($response, Response::HTTP_OK);
    }

    /**
     * Return possible conversions of given currency
     *
     * @Rest\Get("/possible_conversions/{currency}")
     *
     * @param string $currency
     * @param CurrencyConverterService $currencyConverterService
     *
     * @return \FOS\RestBundle\View\View
     */
    public function getExchangeRatesByGivenCurrency(string $currency, CurrencyConverterService $currencyConverterService)
    {
        $response = $currencyConverterService->getExchangeRatesByGivenCurrency($currency);

        if (\array_key_exists('error',$response))
            return $this->view($response, Response::HTTP_NOT_FOUND);

        return $this->view($response, Response::HTTP_OK);
    }

    /**
     * Add new Conversion.
     *
     * @Rest\Post("/add_conversion")
     *
     * @param Request $request
     * @return \FOS\RestBundle\View\View
     */
    public function addConversion(Request $request, CurrencyConverterService $currencyConverterService)
    {
        $currencyFrom = $request->request->get('currencyFrom');
        $currencyTo = $request->request->get('currencyTo');
        $exchangeRate = $request->request->get('exchangeRate');

        $response = $currencyConverterService->addConversion($currencyFrom, $currencyTo, $exchangeRate);

        if (\array_key_exists('error',$response))
            return $this->view($response, Response::HTTP_BAD_REQUEST);

        return $this->view($response, Response::HTTP_CREATED);

    }

    /**
     * Update exchange rates.
     *
     * @Rest\Patch("/update_exchange_rate/currencyFrom={currencyFrom}&currencyTo={currencyTo}")
     * @param string $currencyFrom
     * @param string $currencyTo
     * @param Request $request
     * @param CurrencyConverterService $currencyConverterService
     * @return \FOS\RestBundle\View\View
     */
    public function updateExchangeRate(string $currencyFrom, string $currencyTo, Request $request, CurrencyConverterService $currencyConverterService)
    {
        $exchangeRate = $request->request->get('exchangeRate');
        $response = $currencyConverterService->updateExchangeRate($currencyFrom, $currencyTo, $exchangeRate);

        if (\array_key_exists('error',$response))
            return $this->view($response, Response::HTTP_NOT_FOUND);

        return $this->view($response, Response::HTTP_OK);
    }

    /**
     * Delete Conversion.
     *
     * @Rest\Delete("/delete/currencyFrom={currencyFrom}&currencyTo={currencyTo}")
     *
     * @param string $currencyFrom
     * @param string $currencyTo
     * @param CurrencyConverterService $currencyConverterService
     * @return \FOS\RestBundle\View\View
     */
    public function deleteConversion(string $currencyFrom, string $currencyTo, CurrencyConverterService $currencyConverterService)
    {
        $response = $currencyConverterService->deleteConversion($currencyFrom, $currencyTo);

        if (\array_key_exists('error',$response))
            return $this->view($response, Response::HTTP_NOT_FOUND);

       return $this->view([],Response::HTTP_NO_CONTENT);
    }

    /**
     * Delete all conversions of currency.
     *
     * @Rest\Delete("/delete_all/currencyFrom={currencyFrom}")
     *
     * @param string $currencyFrom
     * @param CurrencyConverterService $currencyConverterService
     * @return \FOS\RestBundle\View\View
     */
    public function deleteAllConversionByCurrency(string $currencyFrom, CurrencyConverterService $currencyConverterService)
    {
        $response = $currencyConverterService->deleteAllConversionByCurrency($currencyFrom);

        if (\array_key_exists('error',$response))
            return $this->view($response, Response::HTTP_NOT_FOUND);

        return $this->view([],Response::HTTP_NO_CONTENT);

    }

}