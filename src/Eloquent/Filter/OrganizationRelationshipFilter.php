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

/**
 * Class OrganizationRelationshipFilter This class is a helper for other filter classes
 * @package Mms\Organizations\Eloquent\Filter
 */
class OrganizationRelationshipFilter
{
    /**
     * @var BaumNestedSetNode[]
     */
    private $organizationNodes;

    /**
     * @var BaumNestedSetNode
     */
    private $nodeInstance;

    /**
     * OrganizationFilter constructor.
     * @param BaumNestedSetNode $nodeInstance
     */
    public function __construct(BaumNestedSetNode $nodeInstance)
    {
        $this->nodeInstance = $nodeInstance;
    }

    /**
     * @param $proxyQuery
     */
    public function __invoke($proxyQuery, array $organizationNodes, $alias)
    {
        $this->apply($proxyQuery, $organizationNodes, $alias);
    }

    /**
     * @param ProxyQuery $proxyQuery
     */
    public function apply($proxyQuery, array $organizationNodes, $baseAlias)
    {
        $alias = 'child_relationship';
        $proxyQuery->join(
            $this->nodeInstance->getTable()." AS $alias",
            "$alias.organization_id",
            '=',
            $baseAlias.'.id'
        );
        $conditions = [];
        $bindings = [];
        $baseParameterName = $alias.'__';
        $counter = 0;
        foreach ($organizationNodes as $node) {
            $counter++;
            $rootParameterName = $baseParameterName."root_".$counter;
            $leftParameterName = $baseParameterName."left_".$counter;
            $rightParameterName = $baseParameterName."right_".$counter;
            $conditions[] = sprintf(
                "($alias.root_id = :%s AND $alias.%s >= :%s AND $alias.%s <= :%s)",
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