<?php
/**
 * Created by PhpStorm.
 * User: sgueye
 * Date: 12/3/2016
 * Time: 6:58 PM
 */

namespace Mms\Organizations;


use App\Models\Organization;
use Mms\Laravel\Eloquent\BaseModel;
use Mms\Laravel\Eloquent\ModelManager;

class OrganizationManager
{
    private $manager;
    private $modelClass;
    private $typeClass;
    private $config;
    private $classIndexedConfig;
    private $organizationTypes = [];


    public function __construct(ModelManager $manager, $modelClass, $typeClass, array $config)
    {
        $this->manager            = $manager;
        $this->modelClass         = $modelClass;
        $this->typeClass          = $typeClass;
        $this->config             = [];
        $this->classIndexedConfig = [];
        foreach ($config as $code => $item) {
            $item['code'] = $code;
            $this->classIndexedConfig[$item['model']] = $item;
            $this->config[$code] = $item;
        }
    }

    /**
     * @param $code
     * @return mixed
     */
    private function getOrganizationType($code)
    {
        if(empty($this->organizationTypes[$code])) {
            $type = $this->manager->getModelRepository($this->typeClass)->findByCode($code);
            if(!$type) {
                throw new \RuntimeException("No organization type with code '$code' found'");
            }
            $this->organizationTypes[$code] = $type;
        }
        return $this->organizationTypes[$code];
    }

    private function getInstanceConfig(BaseModel $instance)
    {
        $class = $this->manager->getInstanceModelClass($instance);
        if(empty($this->classIndexedConfig[$class])) {
            throw new \RuntimeException("No configuration entry for class '$class");
        }
        return $this->classIndexedConfig[$class];
    }

    /**
     * @return array
     */
    public function getConfiguration()
    {
        return $this->config;
    }

    public function getModelClass($code)
    {
        if(!isset($this->config[$code])) {
            throw new \RuntimeException("No configuration entry for code '$code");
        }
        return $this->config[$code]['model'];
    }

    /**
     * Creates an organization, no check for existence is run before attempting the creation
     *
     * @param BaseModel $instance
     * @return Organization|\Illuminate\Database\Eloquent\Model
     */
    public function create(BaseModel $instance)
    {
        $configEntry = $this->getInstanceConfig($instance);
        $organizationType = $this->getOrganizationType($configEntry['code']);
        $organization = $this->manager->create($this->modelClass);
        /**
         * @var Organization $organization
         */
        $organization->organization_type()->associate($organizationType);
        $organization->reference_id = $instance->id;
        $organization->name = $instance->name;
        $organization->description = $instance->description;
        $this->manager->save($organization);
        return $organization;
    }
    public function has(BaseModel $instance)
    {
        $configEntry = $this->getInstanceConfig($instance);
        $organizationType = $this->getOrganizationType($configEntry['code']);
        $finderQuery = $this->manager->getModelRepository($this->modelClass)->get;
    }
}