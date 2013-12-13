<?php
/**
 * Created by JetBrains PhpStorm.
 * User: user
 * Date: 22.09.13
 * Time: 11:43
 * To change this template use File | Settings | File Templates.
 */

namespace Xbagir\Money;

class Currency
{
    protected $name;
    protected $code;
    protected $sign;
    protected $numberToBasic;

    public function __construct($code, array $config)
    {
        $this->code = $code;
        
        foreach ($config as $option => $value)
        {
            $this->$option = $value;   
        }    
    }

    public function code()
    {
        return $this->code;
    }

    public function name()
    {
        return $this->name;
    }

    public function sign()
    {
        return $this->name;
    }
}