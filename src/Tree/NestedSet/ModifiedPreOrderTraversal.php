<?php
/**
 * Created by PhpStorm.
 * User: sgueye
 * Date: 12/12/2016
 * Time: 2:42 PM
 */

namespace Mms\Organizations\Tree\NestedSet;


class ModifiedPreOrderTraversal
{
    public function run(NodeInterface $node, $level = 0, $counter = 1)
    {
        $node->setLevel($level)->setLeftValue($counter++);

        foreach ($node->getChildren() as $child) {
            /**
             * @var NodeInterface $child
             */
            $this->run($child, $level+1, $counter++);
        }
        $node->setRightValue($counter+1);
        return $node;
    }
}