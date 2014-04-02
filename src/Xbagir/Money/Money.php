<?php namespace Xbagir\Money;

class Money
{
    protected $amount;
    protected $currency;
    protected $converter;
    protected $manager;
        
    public function __construct($amount, Currency $currency, Converter $converter, Manager $manager)
    {
        $this->amount    = $amount;
        $this->currency  = $currency;
        $this->converter = $converter;
        $this->manager   = $manager;
    }

    public function amount()
    {
        return $this->amount;
    }

    public function amountFormat($decimals = 0, $decPoint = '.', $thousandsSep = ' ') 
    {
        return number_format($this->amount, $decimals,  $decPoint, $thousandsSep );
    }
    
    public function currency()
    {
        return $this->currency;
    }
    
    public function __call($name, $arguments) 
    {
        if (substr($name, 0, 2) == 'to')
        {                                   
            $targetCurrencyCode = substr($name, 2);
            $amount             = $this->converter->convert($this->currency()->code(), $targetCurrencyCode, $this->amount());
                  
            return call_user_func_array(array($this->manager, $targetCurrencyCode), array($amount));     
        }

        throw new \InvalidArgumentException(strtr('Tried to call unknown method [:method]', array(
            ':method' => get_class($this).'::'.$name,
        )));
    }
    
}
 