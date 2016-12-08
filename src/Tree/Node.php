<?php
/**
 * Created by PhpStorm.
 * User: sgueye
 * Date: 12/7/2016
 * Time: 3:05 PM
 */

namespace Mms\Organizations\Tree;


use App\Models\OrganizationHierarchy;
use Tree\Node\Node as TreeNode;


class Node extends TreeNode
{
    /**
     * Node constructor.
     * @param OrganizationHierarchy $organizationRelationship
     */
    public function __construct(OrganizationHierarchy $organizationRelationship)
    {
        parent::__construct($organizationRelationship);
    }

    /**
     * @return OrganizationHierarchy
     */
    public function getValue()
    {
        return parent::getValue();
    }

}