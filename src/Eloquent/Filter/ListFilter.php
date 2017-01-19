<?php
/**
 * Created by PhpStorm.
 * User: sgueye
 * Date: 12/5/2016
 * Time: 3:38 PM
 */

namespace Mms\Organizations\Eloquent\Filter;


use App\Models\OrganizationRelationship;
use Baum\Node as BaumNestedSetNode;
use Mms\Admin\Datagrid\ProxyQuery;

class ListFilter
{
    /**
     * @var BaumNestedSetNode[]
     */
    private $organizationNodes;

    /**
     * @var OrganizationRelationship
     */
    private $nodeInstance;

    /**
     * @var string
     */
    private $alias;
    public function __construct(array $organizationNodes, $alias)
    {
        $this->organizationNodes = $organizationNodes;
        $this->nodeInstance      = new OrganizationRelationship();
        $this->alias             = $alias;
    }

    /**
     * @param $proxyQuery
     */
    public function __invoke($proxyQuery)
    {
        $this->apply($proxyQuery);
    }

    /**
     * @param ProxyQuery $proxyQuery
     */
    public function apply($proxyQuery)
    {
        $alias = 'child_relationship';
        $proxyQuery->join(
            $this->nodeInstance->getTable()." AS $alias",
            "$alias.organization_id",
            '=',
            $this->alias.'.id'
        );
        $conditions = [];
        $bindings = [];
        $baseParameterName = $alias.'__';
        $counter = 0;
        foreach ($this->organizationNodes as $node) {
            $counter++;
            $rootParameterName = $baseParameterName."root_".$counter;
            $leftParameterName = $baseParameterName."left_".$counter;
            $rightParameterName = $baseParameterName."right_".$counter;
            $conditions[] = sprintf(
                "($alias.root_id = :%s AND $alias.%s >= :%s AND $alias.%s < :%s)",
                $rootParameterName,
                $this->nodeInstance->getLeftColumnName(),
                $leftParameterName,
                $this->nodeInstance->getRightColumnName(),
                $rightParameterName
            );
            $bindings[":$rootParameterName"]  = $node->root_id;
            $bindings[":$leftParameterName"]  = $node->getLeft();
            $bindings[":$rightParameterName"] = $node->getRight();
        }
        $sql = implode(' OR ', $conditions);
        $proxyQuery->whereRaw("($sql)", $bindings);
    }
}