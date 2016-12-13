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

    protected function generateExtra(NodeInterface $node)
    {
        return [];
    }
    public function generate(NodeInterface $node)
    {
        /**
         * @var Node $node
         */
        return array_merge(
            [
                'name' => $node->getValue()."",
                'id' => $node->getValue()->id,
                'is_leaf' => $node->isLeaf(),
                'children' => []
            ],
            $this->generateExtra($node)
        );
    }

    public function __invoke(NodeInterface $node)
    {
        return $this->generate($node);
    }

}