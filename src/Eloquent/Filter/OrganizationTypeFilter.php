<?php
/**
 * Created by PhpStorm.
 * User: sgueye
 * Date: 12/5/2016
 * Time: 3:38 PM
 */

namespace Mms\Organizations\Eloquent\Filter;


use Baum\Node as BaumNestedSetNode;

class OrganizationTypeFilter
{
    /**
     * @var OrganizationRelationshipFilter
     */
    private $organizationFilter;

    /**
     * @var BaumNestedSetNode[]
     */
    private $organizationNodes;

    /**
     * @var string
     */
    private $alias;
    public function __construct(OrganizationRelationshipFilter $organizationFilter, array $organizationNodes, $alias)
    {
        $this->organizationFilter = $organizationFilter;
        $this->organizationNodes  = $organizationNodes;
        $this->alias              = $alias;
    }

    /**
     * @param $proxyQuery
     */
    public function __invoke($proxyQuery)
    {
        /*
         * Join the organization table, then let the organization filter apply the relationship filters
         */
        $alias = 'organization';
        $proxyQuery->join(
            'organization'." AS $alias",
            "$alias.organization_type_id",
            '=',
            $this->alias.'.id'
        );
        $this->organizationFilter->apply($proxyQuery, $this->organizationNodes, $alias);
    }
}