<?php namespace Xbagir\Money\Console;

use Illuminate\Cache\CacheManager;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class MakeExchangeRatesCommand extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'money:exchange-rates';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Creating cache exchange rate';
    
    protected $cache;
    protected $cacheKey;
    
    protected $exchangeRates = array('EUR',array(
            'USD' => 1.375,
            'JPY' => 141.35,
            'BGN' => 1.9558,
            'CZK' => 27.453,
            'DKK' => 7.4604,
            'GBP' => 0.83645,
            'HUF' => 300.79,
            'LTL' => 3.4528,
            'LVL' => 0.7031,
            'PLN' => 4.1825,
            'RON' => 4.4525,
            'SEK' => 8.9897,
            'CHF' => 1.2214,
            'NOK' => 8.4015,
            'HRK' => 7.6425,
            'RUB' => 44.9962,
            'TRY' => 2.7902,
            'AUD' => 1.5039,
            'BRL' => 3.1759,
            'CAD' => 1.4604,
            'CNY' => 8.3486,
            'HKD' => 10.6605,
            'IDR' => 16371.02,
            'ILS' => 4.8061,
            'INR' => 83.9149,
            'KRW' => 1444.26,
            'MXN' => 17.6749,
            'MYR' => 4.4094,
            'NZD' => 1.6529,
            'PHP' => 60.837,
            'SGD' => 1.7181,
            'THB' => 44.099,
            'ZAR' => 14.1808,
    ));

    /**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct(CacheManager $cache, $cacheKey)
	{
        $this->cache    = $cache;
        $this->cacheKey = $cacheKey;
        
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return void
	 */
	public function fire()
	{
        $this->cache->forget($this->cacheKey);
        
        // 3 попытки получить ответ от сервера банка. 
        // Ибо он иногда отвечает пустотой
        for ( $index = 1; $index <= 3 ; $index++ )
        {
            if ( ! $exchangeRates = $this->getExchangeRates() )
            {
                continue;
            }
            
            $this->cache->forever($this->cacheKey, $exchangeRates);

            break;
        }

        if ($this->cache->has($this->cacheKey))
        {    
            $this->info('Cache exchange rate created!'); 
        }
        else
        {
            $this->info('Cache exchange rate is not created!');

            $this->cache->forever($this->cacheKey, $this->exchangeRates);
        }    
	}

    protected function getExchangeRates()
    {
        $exchangeRates = array();
        $curl          = curl_init();

        curl_setopt($curl, CURLOPT_URL, "http://www.ecb.europa.eu/stats/eurofxref/eurofxref-daily.xml");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1; rv:24.0) Gecko/20100101 Firefox/24.0");
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT_MS, 1000);
        curl_setopt($curl, CURLOPT_FORBID_REUSE, true);

        $xml = curl_exec($curl);

        curl_close($curl);

        if ( ! $xml)
        {
            return $exchangeRates;
        }

        $xml = simplexml_load_string($xml);

        foreach ($xml->Cube->Cube->Cube as $tag)
        {
            $exchangeRates[strval($tag->attributes()->currency)] = floatval($tag->attributes()->rate);
        }
        
        return array('EUR', $exchangeRates);
    }

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return array(
			//array('example', InputArgument::REQUIRED, 'An example argument.'),
		);
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return array(
			//array('example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null),
		);
	}

}