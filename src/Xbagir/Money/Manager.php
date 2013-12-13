<?php
/**
 * Created by JetBrains PhpStorm.
 * User: user
 * Date: 22.09.13
 * Time: 12:35
 * To change this template use File | Settings | File Templates.
 */

namespace Xbagir\Money;

class Manager
{
    protected $config;
    protected $converter;
    
    public function __construct(array $config, Converter $converter)
    {
        $this->config    = $config;
        $this->converter = $converter;
    }

    public function __call($code, $arguments)
    {
        if ( ! array_key_exists($code, $this->config) )
        {
            throw new \InvalidArgumentException(strtr('The currency code [:code] is not defined in the configuration file', array(
                ':code' => $code,
            )));  
        }    
        
        return new Money($arguments[0], new Currency($code, $this->config[$code]), $this->converter, $this);
    }
}