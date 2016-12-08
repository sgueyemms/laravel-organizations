<?php
/**
 * Created by PhpStorm.
 * User: sgueye
 * Date: 12/7/2016
 * Time: 3:05 PM
 */

namespace Mms\Organizations\Tree;


use Tree\Node\NodeInterface;


class ArrayGenerator
{
    public function generate(NodeInterface $node)
    {
        /**
         * @var Node $node
         */
        return [
            'name' => $node->getValue()->organization."",
            'id' => $node->getValue()->id,
            'is_leaf' => $node->isLeaf(),
            'organization_id' => $node->getValue()->organization->id,
            'organization_type_id' => $node->getValue()->organization->organization_type_id,
            'children' => []
        ];
    }

    public function __invoke(NodeInterface $node)
    {
        return $this->generate($node);
    }

}