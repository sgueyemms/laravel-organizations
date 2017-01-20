<?php

namespace Mms\Organizations;

use Illuminate\Container\Container;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Mms\Laravel\Eloquent\ModelManager;
use Mms\Organizations\Eloquent\Filter\OrganizationListFilter;
use Mms\Organizations\Eloquent\Filter\OrganizationRelationshipFilter;
use Mms\Organizations\Eloquent\OrganizationAccessManager;
use Mms\Organizations\PrezentGrid\Type\OrganizationRelationshipType;
use Mms\Organizations\Tree\ToArrayVisitor;

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

        $this->app->bind(YearManager::class, $this->app->share(function (Container $app) {
            return new YearManager(
                $app->make(ModelManager::class)->getModelRepository('App\Models\Year'),
                '2016-2017'
            );
        }));

        $this->app->bind(OrganizationReferenceManager::class, $this->app->share(function (Container $app) {
            return new OrganizationReferenceManager(
                $app->make(ModelManager::class)->getModelRepository('App\Models\OrganizationRelationship')
            );
        }));

        $this->app->bind(OrganizationManager::class, $this->app->share(function (Container $app) {
            return new OrganizationManager(
                $app->make(ModelManager::class),
                $app['config']->get('mms_organizations.model_class'),
                $app['config']->get('mms_organizations.model_type_class'),
                $app['config']->get('mms_organizations.models'),
                $app['config']->get('mms_organizations.relationships')
            );
        }));

        $this->app->bind(OrganizationRelationshipType::class, function(Container $app) {
            return new OrganizationRelationshipType(
                $app->make(OrganizationManager::class),
                $app->make(ToArrayVisitor::class)
            );
        });
        //This is not its place as the admin package depends on that one
        //Make the admin grid extension take resolvers and create the resolvers in the application code
        $this->app->tag(OrganizationRelationshipType::class, ['admin.grid_type']);

        $this->app->bind(OrganizationRelationshipFilter::class, $this->app->share(function (Container $app) {
            return new OrganizationRelationshipFilter(
                $app->make(ModelManager::class)->getModelInstance('App\Models\OrganizationRelationship')
            );
        }));

        $this->app->bind(OrganizationAccessManager::class, $this->app->share(function (Container $app) {
            return new OrganizationAccessManager(
                $app->make(OrganizationRelationshipFilter::class),
                $app->make(ModelManager::class),
                'App\Models\Organization'
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

        $this->app->singleton('mms.organizations.relationship', function(Container $app) {
            return $app->make(Command\BuildOrganizationsRelationshipCommand::class);
        });
        $this->commands('mms.organizations.relationship');
    }
}
