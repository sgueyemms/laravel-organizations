<?php
/**
 * Created by PhpStorm.
 * User: sgueye
 * Date: 12/1/2016
 * Time: 10:17 PM
 */

namespace Mms\Organizations\Command;

use App\Admin\ProjectAdmin;
use App\Admin\TaskAdmin;
use App\Models\Document;
use App\Models\FileUpload;
use App\Models\Project;
use App\Models\Role;
use App\Models\Tag;
use App\Models\User;
use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;
use ICanBoogie\Inflector;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Mms\Acl\AclLoader;
use Mms\Acl\EloquentDomainObject;
use Mms\Acl\ObjectIdentityRetrievalStrategy;
use Mms\Acl\UserAccountFactory;
use Mms\Admin\Admin;
use Mms\Admin\Pool;
use Faker\Factory as FakerFactory;
use Mms\Laravel\Eloquent\BlameableUser;
use Mms\MultiTenancy\Tenant\RequestTenantProvider;
use Symfony\Component\Security\Acl\Permission\MaskBuilder;
use Symfony\Component\Security\Acl\Domain\ObjectIdentity;
use Symfony\Component\Security\Acl\Domain\UserSecurityIdentity;
use Symfony\Component\Security\Acl\Model\MutableAclProviderInterface;

class BuildOrganizationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:build-organizations {type} {--opt1=} {--opt2=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Builds organizations out of administrative entities';

    /**
     *
     * @return \Mms\Laravel\Eloquent\ModelManager
     */
    private function getModelManager()
    {
        return app('mms.eloquent.model_manager');
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

    }
}