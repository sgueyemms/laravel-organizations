<?php
/**
 * Created by PhpStorm.
 * User: sgueye
 * Date: 12/7/2016
 * Time: 3:05 PM
 */

namespace Mms\Organizations\Tree\NestedSet;


use Tree\Node\NodeInterface;
use Mms\Organizations\Tree\ArrayGenerator;

class Generator extends ArrayGenerator
{

    protected function generateExtra(NodeInterface $node)
    {
        /**
         * @var Node $node
         */
        return [
            'left' => $node->getLeftValue(),
            'right' => $node->getRightValue(),
            'level' => $node->getLevel(),
        ];
    }
}