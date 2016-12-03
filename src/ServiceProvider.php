<?php

namespace Mms\Organizations;

use Illuminate\Container\Container;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

/**
 * Description of ServiceProvider
 *
 * @author sgueye
 */
class ServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/config.php' => config_path('mms_organizations.php')
        ]);

    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/config.php', 'organizations'
        );



        $this->registerCommands();

    }
    protected function registerCommands()
    {
        $this->app->singleton('mms.multi-tenancy.event-registration', function(Container $app) {
            return $app->make(Command\GenerateModelEventRegistration::class);
        });
        $this->commands('mms.multi-tenancy.event-registration');
    }
}
