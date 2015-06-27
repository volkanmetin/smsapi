<?php namespace Volkanmetin\Smsapi;

use Illuminate\Support\ServiceProvider;

class SmsapiServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../../config/smsapi.php' => config_path('smsapi.php'),
        ], 'config');

        $this->loadTranslationsFrom(__DIR__.'/../../lang', 'smsapi');
    }

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
        $this->app['smsapi'] = $this->app->share(function($app)
        {
            return new Smsapi($app);
        });
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return ['smsapi'];
	}

}
