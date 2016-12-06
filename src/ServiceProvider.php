<?php

namespace Mms\Organizations;

use Illuminate\Container\Container;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Mms\Laravel\Eloquent\ModelManager;

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
            __DIR__.'/../config/config.php', 'mms_organizations'
        );

        $this->app->bind(OrganizationManager::class, $this->app->share(function (Container $app) {
            return new OrganizationManager(
                $app->make(ModelManager::class),
                $app['config']->get('mms_organizations.model_class'),
                $app['config']->get('mms_organizations.model_type_class'),
                $app['config']->get('mms_organizations.models'),
                $app['config']->get('mms_organizations.hierarchies')
            );
        }));

        $this->registerCommands();

    }
    protected function registerCommands()
    {
        $this->app->singleton('mms.organizations.init', function(Container $app) {
            return $app->make(Command\BuildOrganizationsCommand::class);
        });
        $this->commands('mms.organizations.init');

        $this->app->singleton('mms.organizations.hierarchy', function(Container $app) {
            return $app->make(Command\BuildOrganizationsHierarchyCommand::class);
        });
        $this->commands('mms.organizations.hierarchy');
    }
}
