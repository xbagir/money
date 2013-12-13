<?php
/**
 * Created by JetBrains PhpStorm.
 * User: user
 * Date: 22.09.13
 * Time: 12:35
 * To change this template use File | Settings | File Templates.
 */

namespace Xbagir\Money;

class Converter
{
    private $currencyCode  = 'EUR';
    private $exchangeRates = array();
    
    public function __construct($currencyCode, array $exchangeRates)
    {
        $this->currencyCode  = $currencyCode;
        $this->exchangeRates = $exchangeRates;
    }    
    
    public function convert($currentCurrencyCode, $targetCurrencyCode, $amount)
    {       
        $amount = $amount / $this->exchangeRates[$currentCurrencyCode];

        if ($targetCurrencyCode != $this->currencyCode)
        {
            $amount *= $this->exchangeRates[$targetCurrencyCode];    
        }

        return round($amount, 4);
    }
}