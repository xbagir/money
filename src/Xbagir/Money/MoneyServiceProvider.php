<?php namespace Xbagir\Money;

use Illuminate\Support\ServiceProvider;
use Whoops\Example\Exception;
use Xbagir\Money\Console\MakeExchangeRatesCommand;

class MoneyServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = true;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{        
        $this->package('xbagir/money');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
    public function register()
    { 
        $this->app['money'] = $this->app->share(function ($app)
        {            
            $config    = $app['config']->get('money::money.currency');
            $converter = $app['money.converter'];

            return new Manager($config, $converter);
        });

        $this->app['money.converter'] = $this->app->share(function($app)
        {
            list($currencyCode, $exchangeRates) = $app['cache']->get($app['config']->get('money::money.cacheKey'));
                 
            return new Converter($currencyCode, $exchangeRates);
        });
                
        $this->app['command.money.exchange-rates'] = $this->app->share(function($app)
        {
            return new MakeExchangeRatesCommand($app['cache'], $app['config']->get('money::money.cacheKey'));
        });

        $this->commands('command.money.exchange-rates');
    }
    
	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
        return array('money', 'money.converter', 'command.money.exchange-rates');
	}

}