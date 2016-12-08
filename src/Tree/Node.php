<?php
/**
 * Created by PhpStorm.
 * User: sgueye
 * Date: 12/7/2016
 * Time: 3:05 PM
 */

namespace Mms\Organizations\Tree;


use App\Models\OrganizationRelationship;
use Tree\Node\Node as TreeNode;


class Node extends TreeNode
{
    /**
     * Node constructor.
     * @param OrganizationRelationship $organizationRelationship
     */
    public function __construct(OrganizationRelationship $organizationRelationship)
    {
        parent::__construct($organizationRelationship);
    }

    /**
     * @return OrganizationRelationship
     */
    public function getValue()
    {
        return parent::getValue();
    }

}