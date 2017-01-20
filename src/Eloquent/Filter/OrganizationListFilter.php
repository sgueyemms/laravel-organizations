<?php
/**
 * Created by PhpStorm.
 * User: sgueye
 * Date: 12/5/2016
 * Time: 3:38 PM
 */

namespace Mms\Organizations\Eloquent\Filter;


use Baum\Node as BaumNestedSetNode;

class OrganizationListFilter
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
        $this->organizationFilter->apply($proxyQuery, $this->organizationNodes, $this->alias);
    }
}