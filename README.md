# RESTful Currency-Converter-WebService 
A simple Currency-Convertor Web-Service with REST API on Symfony Skeleton using FOSRestBundle

Usage
--------------
GET => For convert currency:
http://127.0.0.1:8000/api/converter/convertFrom={currencyFrom}&convertTo={currencyTo}&amount={amount}

http://127.0.0.1:8000/api/converter/convertFrom=USD&convertTo=UAH&amount=2
```php
{
    "result": {
        "converterResult": 56.58,
        "readable result": "Given 2 USD = 56.58 UAH",
        "attributes": {
            "convertFrom": "USD",
            "convertTo": "UAH",
            "exchangeRates": "1 USD equals 28.29 UAH"
        },
        "links": {
            "see all possible converting variants ": "/api/all_currencies",
            "see exchange rate for given Currencies ": "/api/possible_conversions/USD",
            "add new conversion": "/api/add_conversion",
            "update exchange rates": "/api/update_exchange_rate/currencyFrom=USD&currencyTo=UAH"
        }
    }
}
```
GET => Get all currencies
http://127.0.0.1:8000/api/all_currencies

```php
 "result": {
            "currencyToConvert": "USD",
            "convertedCurrency": "RUB",
            "exchangeRates": 68.01
        },
        "links": {
            "see exchange rate for given Currency ": "/api/possible_conversions/USD"
        }
    },
    {
        "result": {
            "currencyToConvert": "UAH",
            "convertedCurrency": "USD",
            "exchangeRates": 0.036
        },
        "links": {
            "see exchange rate for given Currency ": "/api/possible_conversions/UAH"
        }
    },
    {
        "result": {
            "currencyToConvert": "UAH",
            "convertedCurrency": "RUB",
            "exchangeRates": 68.01
        },
        "links": {
            "see exchange rate for given Currency ": "/api/possible_conversions/UAH"
        }
......        
```
GET => Get possible conversions of given currency:
http://127.0.0.1:8000/api/possible_conversions/{currency}

http://127.0.0.1:8000/api/possible_conversions/USD
```php
{
        "result": {
            "currencyToConvert": "USD",
            "convertedCurrency": "UAH",
            "exchangeRates": 28.29
        },
        "links": {
            "see all possible converting variants ": "/api/all_currencies"
        }
    },
    {
        "result": {
            "currencyToConvert": "USD",
            "convertedCurrency": "EUR",
            "exchangeRates": 0.86
        },
        "links": {
            "see all possible converting variants ": "/api/all_currencies"
        }
```

POST => Add new conversion:
http://127.0.0.1:8000/api/add_conversion
Example of data(json format): ```{"currencyFrom": "CAD", "currencyTo": "USD", "exchangeRate": 0.77 }```
```php
{
    "result": {
        "message": "Success"
    },
    "links": {
        "see exchange rate for given Currency": "/api/possible_conversions/CAD",
        "convert currency": "/api/converter/convertFrom=CAD&convertTo=USD&amount=setAmount",
        "delete this conversion": "/api/delete/currencyFrom=CAD&currencyTo=USD",
        "delete all possible conversions of this currency": "/api/delete_all/currencyFrom=$s"
    }
}
```

PATCH => Update ExchangeRates:
http://127.0.0.1:8000/api/update_exchange_rate/currencyFrom=CAD&currencyTo=USD
Example of data(json format): ```{"exchangeRate": 0.80 }```
```php
{
    "message": "Success",
    "result": {
        "currencyToConvert": "CAD",
        "convertedCurrency": "USD",
        "exchangeRates": 0.8
    },
    "links": {
        "convert currency": "api/converter/convertFrom=CAD&convertTo=USD&amount=setAmount",
        "see all possible converting variants ": "/api/all_currencies"
    }
}
```
DELETE => Delete Conversion:
http://127.0.0.1:8000/api/delete/currencyFrom=CAD&currencyTo=USD

DELETE => Delete all conversions of currency:
http://127.0.0.1:8000/api/delete_all/currencyFrom=CAD









