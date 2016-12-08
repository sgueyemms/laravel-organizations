<?php
/**
 * Created by PhpStorm.
 * User: sgueye
 * Date: 12/3/2016
 * Time: 6:58 PM
 */

namespace Mms\Organizations;


use App\Models\Organization;
use App\Models\OrganizationRelationship;
use App\Models\OrganizationType;
use Illuminate\Database\Eloquent\Model;
use Mms\Laravel\Eloquent\BaseModel;
use Mms\Laravel\Eloquent\ModelManager;
use Mms\Organizations\Eloquent\OrganizationInterface;
use Mms\Organizations\Eloquent\YearInterface;
use Mms\Organizations\Tree\Node as TreeNode;


class OrganizationManager
{
    private $manager;
    private $modelClass;
    private $typeClass;
    private $config;
    private $hierarchies;
    private $classIndexedConfig;
    private $organizationTypes = [];

    /**
     * @var \SplObjectStorage
     */
    private $referenceStorage;



    public function __construct(ModelManager $manager, $modelClass, $typeClass, array $config, array $hierarchies)
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
        $this->hierarchies = $hierarchies;
        $this->referenceStorage = [];
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
    public function create(YearInterface $year, BaseModel $instance)
    {
        $configEntry = $this->getInstanceConfig($instance);
        $organizationType = $this->getOrganizationType($configEntry['code']);
        /**
         * @var OrganizationInterface $organization
         */
        $organization = $this->manager->create($this->modelClass);
        $organization->processYear($year);
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

    /**
     * @param OrganizationInterface|Model $organization
     * @return BaseModel
     */
    private function findReference(OrganizationInterface $organization)
    {
        $key = $organization->id .'_'.$organization->organization_type_id;
        if(empty($this->referenceStorage[$key])) {
            if(!$organization->relationLoaded('organization_type')) {
                $organization->load('organization_type');
            }
            $organizationType = $organization->organization_type;
            if(!$organizationType) {
                throw new \RuntimeException("No organization type found");
            }
            $referenceClass = $this->getModelClass($organizationType->code);
            $reference = $this->manager->getModelRepository($referenceClass)
                ->find($organization->getReferenceIdentifier());
            if(!$reference) {
                throw new \RuntimeException(sprintf(
                    "No reference found in model %s with ID=%s",
                    $referenceClass,
                    $organization->getReferenceIdentifier())
                );
            }
            $this->referenceStorage[$key] = $reference;
        }
        return $this->referenceStorage[$key];
    }
    public function loadReference(OrganizationInterface $organization)
    {
        if(!$organization->getReference()) {
            $reference = $this->findReference($organization);
            if($reference) {
                $organization->setReference($reference);
            }
        }
        return $this;
    }

    /**
     * @param BaseModel $instance
     * @return OrganizationType
     */
    private function getInstanceReferenceType(BaseModel $instance)
    {
        $configEntry = $this->getInstanceConfig($instance);
        return $this->getOrganizationType($configEntry['code']);
    }

    /**
     * @param YearInterface $year
     * @param BaseModel $instance
     * @return OrganizationInterface
     */
    public function findOrganization(YearInterface $year, BaseModel $instance)
    {
        $criteria = [
            'year_id' => $year->id,
            'reference_id' => $instance->id,
            'organization_type_id' => $this->getInstanceReferenceType($instance)->id
        ];
        $organization = $this->manager->getModelRepository($this->modelClass)->findOneBy($criteria);
        if($organization) {
            $this->loadReference($organization);
        }
        return $organization;
    }

    /**
     * Finds an organization by its code and a year
     *
     * It uses a left join with all tables that implement the organization (using organization types).
     * @param YearInterface $year
     * @param string $code
     * @return OrganizationInterface
     */
    public function findOrganizationByCode(YearInterface $year, $code)
    {
        $criteria = [
            'year_id' => $year->id,
            'reference_id' => $instance->id,
            'organization_type_id' => $this->getInstanceReferenceType($instance)->id
        ];
        $queryBuilder = $this->manager->createQueryBuilder(Organization::class);
        foreach ($this->config['models'] as $organizationTypeCode => $configEntry) {
            $metadata = $this->manager->getModelMetadata($configEntry['model']);
            //$queryBuilder->leftJoin()
        }
        throw new \Exception("Not implemented yet");
    }
    public function has(BaseModel $instance)
    {
        $configEntry = $this->getInstanceConfig($instance);
        $organizationType = $this->getOrganizationType($configEntry['code']);
        $finderQuery = $this->manager->getModelRepository($this->modelClass)->get;
    }

    /**
     * @param OrganizationRelationship $node
     * @param OrganizationInterface|Model $organization
     */
    private function processHierarchy(OrganizationRelationship $node, OrganizationInterface $organization, $nodeBuilder)
    {
        $this->loadReference($organization);
        $config = $this->getInstanceConfig($organization->getReference());
        $configKey = $config['code'];
        if(!empty($this->hierarchies[$configKey])) {
            foreach ($this->hierarchies[$configKey] as $relation) {
                foreach ($organization->getReference()->getRelationValue($relation) as $childReference) {
                    $childOrganization = $this->findOrganization($organization->year, $childReference);
                    $childNode = $nodeBuilder($childOrganization);
                    //$childNode->makeChildOf($node);
                    $node->addChild($childNode);
                    $childNode->save();
                    $this->processHierarchy($childNode, $childOrganization, $nodeBuilder);
                }
            }
        }
    }

    public function buildHierarchy(OrganizationInterface $organization)
    {
        $nodeBuilder = function ($organization) {
            $node = new OrganizationRelationship();
            $node->organization_id = $organization->id;
            $node->save();
            return $node;
        };
        $root = $nodeBuilder($organization);
        $this->processHierarchy($root, $organization, $nodeBuilder);
    }

    public function findRoot(OrganizationInterface $organization)
    {
        $queryBuilder = OrganizationRelationship::roots();
        $queryBuilder->where('organization_id', '=', $organization->getIdentifier());
        return $queryBuilder->get()->first();
    }

    public function getOrganizationBranches(OrganizationInterface $organization)
    {
        $queryBuilder = $this->manager->createQueryBuilder(OrganizationRelationship::class);
        $queryBuilder->where('organization_id', '=', $organization->getIdentifier());
        $branches = [];
        foreach ($queryBuilder->get() as $setNode) {
            /**
             * @var OrganizationRelationship $setNode
             */
            $branches[] = [
                'node' => $this->buildRelationshipTree($setNode),
                'path' => array_map(
                    function($node) { return $node->organization->getExtendedLabel(); },
                    $setNode->getAncestors()->all()
                )
            ];
        };
        return $branches;
    }

    public function processPrettyPrint(
        TreeNode $tree,
        $data,
        $level,
        $indent
    ){
        $data[] = str_repeat($indent, $level). $tree->getValue()->organization."";
        foreach ($tree->getChildren() as $child) {
            $data = $this->processPrettyPrint($child, $data, $level+1, $indent);
        }
        return $data;
    }

    public function prettyPrint(TreeNode $tree, $indent = null)
    {
        return $this->processPrettyPrint($tree, [], 0, $indent ?: '  ');
    }

    public function buildRelationshipTree(OrganizationRelationship $setNode)
    {
        $tree = new TreeNode($setNode);
        foreach ($setNode->children as $setChildNode) {
            $tree->addChild($this->buildRelationshipTree($setChildNode));
        }
        return $tree;
    }
}