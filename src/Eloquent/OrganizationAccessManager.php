<?php
/**
 * Created by PhpStorm.
 * User: sgueye
 * Date: 1/20/2017
 * Time: 10:46 AM
 */

namespace Mms\Organizations\Eloquent;


use Mms\Laravel\Eloquent\ModelManager;
use Mms\Organizations\Eloquent\Filter\OrganizationRelationshipFilter;

class OrganizationAccessManager
{
    /**
     * @var OrganizationRelationshipFilter
     */
    private $organizationFilter;

    /**
     * @var
     */
    private $manager;

    private $modelClass;
    public function __construct(OrganizationRelationshipFilter $organizationFilter, ModelManager $manager, $modelClass)
    {
        $this->organizationFilter = $organizationFilter;
        $this->manager            = $manager;
        $this->modelClass         = $modelClass;
    }
    public function isAccessible(OrganizationInterface $organization, array $organizationNodes)
    {
        $queryBuilder = $this->manager->createQueryBuilder($this->modelClass);
        $alias = $this->manager->getModelQueryAlias($this->modelClass);
        $queryBuilder->whereRaw("$alias.id = :id", [':id' => $organization->getIdentifier()], 'and');
        $this->organizationFilter->apply($queryBuilder, $organizationNodes, $alias);
        return $queryBuilder->count() > 0;
    }
}