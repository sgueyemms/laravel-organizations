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
    /**
     * @var int
     */
    private $counter = 1;

    /**
     * @param NodeInterface $node
     * @param $level
     * @return NodeInterface
     */
    private function doRun(NodeInterface $node, $level)
    {
        $node->setLevel($level)->setLeftValue($this->counter++);

        foreach ($node->getChildren() as $child) {
            /**
             * @var NodeInterface $child
             */
            $this->doRun($child, $level+1);
        }
        $node->setRightValue($this->counter++);
        return $node;
    }

    /**
     * @param NodeInterface $node
     * @return NodeInterface
     */
    public function run(NodeInterface $node)
    {
        $this->counter = 1;
        return $this->doRun($node, 0);
    }
}