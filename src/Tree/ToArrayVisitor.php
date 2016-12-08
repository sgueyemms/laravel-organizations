<?php
/**
 * Created by PhpStorm.
 * User: sgueye
 * Date: 12/7/2016
 * Time: 3:05 PM
 */

namespace Mms\Organizations\Tree;


use Tree\Node\NodeInterface;
use Tree\Visitor\Visitor;


class ToArrayVisitor implements Visitor
{
    private $generator;
    public function __construct(ArrayGenerator $generator)
    {
        $this->generator = $generator;
    }

    public function visit(NodeInterface $node)
    {
        /**
         * @var Node $node
         */
        $data = $this->generator->generate($node);
        $children = [];
        foreach ($node->getChildren() as $child) {
            $children[] = $child->accept($this);
        }
        $data['children'] = $children;
        return $data;
    }

}