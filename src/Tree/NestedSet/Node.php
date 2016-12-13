<?php
/**
 * Created by PhpStorm.
 * User: sgueye
 * Date: 12/7/2016
 * Time: 3:05 PM
 */

namespace Mms\Organizations\Tree\NestedSet;


use Tree\Node\Node as TreeNode;

class Node extends TreeNode implements NodeInterface
{
    /**
     * @var
     */
    private $identifier;

    private $leftValue;

    private $rightValue;

    private $level;


    /**
     * gets a unique identifier for this node
     *
     * @return mixed
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @param mixed $id
     * @return $this
     */
    public function setIdentifier($id)
    {
        $this->identifier = $id;
        return $this;
    }


    /**
     * gets a string representation of the Node
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getValue()."";
    }

    /**
     * gets Node's left value
     *
     * @return int
     */
    public function getLeftValue()
    {
        return $this->leftValue;
    }

    /**
     * sets Node's left value
     *
     * @param int $lft
     * @return $this
     */
    public function setLeftValue($lft)
    {
        $this->leftValue = $lft;
        return $this;
    }

    /**
     * gets Node's right value
     *
     * @return int
     */
    public function getRightValue()
    {
        return $this->rightValue;
    }

    /**
     * sets Node's right value
     *
     * @param int $rgt
     * @return $this
     */
    public function setRightValue($rgt)
    {
        $this->rightValue = $rgt;
        return $this;
    }

    /**
     * @return int
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @param mixed $level
     * @return $thi
     */
    public function setLevel($level)
    {
        $this->level = $level;
        return $this;
    }

}