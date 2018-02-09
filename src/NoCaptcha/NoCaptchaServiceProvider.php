<?php namespace GuestIsp\NoCaptcha;

use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class NoCaptchaServiceProvider extends ServiceProvider {

    /**
    * Indicates if loading of the provider is deferred.
    *
    * @var bool
    */
    protected $defer = false;

    /**
    * Register the service provider.
    *
    * @return void
    */
    public function register()
    {
        $this->app['nocaptcha'] = $this->app->share(function($app)
        {
            return new NoCaptcha;
        });
    }

    /**
    * Bootstrap the application events.
    *
    * @return void
    */
    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__ . '/../lang', 'nocaptcha');

        $this->app->booted(function($app) {
            // Get validator and translator
            $validator = $app['validator'];
            $translator = $app['translator'];

            // Aggiunge due regole di validazione custom
            $validator->extend('nocaptcha',      'nocaptcha@validateNoCaptcha',     $translator->get('nocaptcha::validation.nocaptcha'));
            $validator->extend('nocaptchanonce', 'nocaptcha@validateNonce',         $translator->get('nocaptcha::validation.nocaptcha'));
            $validator->extend('nocaptchatime',  'nocaptcha@validateNoCaptchaTime', $translator->get('nocaptcha::validation.nocaptchatime'));
        });
    }

    /**
    * Get the services provided by the provider.
    *
    * @return array
    */
    public function provides()
    {
        return array('nocaptcha');
    }
}
