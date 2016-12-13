<?php
/**
 * Created by PhpStorm.
 * User: sgueye
 * Date: 12/12/2016
 * Time: 1:44 PM
 */

namespace Mms\Organizations\Tree\NestedSet;

use Tree\Node\NodeInterface as TreeNodeInterface;

interface NodeInterface extends TreeNodeInterface
{
    /**
     * gets a unique identifier for this node
     *
     * @return mixed
     */
    public function getIdentifier();

    /**
     * sets a unique identifier for this node
     *
     * @param mixed $id
     * @return $this
     */
    public function setIdentifier($id);
    /**
     * gets a string representation of the Node
     *
     * @return string
     */
    public function __toString();
    /**
     * gets Node's left value
     *
     * @return int
     */
    public function getLeftValue();
    /**
     * sets Node's left value
     *
     * @param int $lft
     * @return $this
     */
    public function setLeftValue($lft);
    /**
     * gets Node's right value
     *
     * @return int
     */
    public function getRightValue();
    /**
     * sets Node's right value
     *
     * @param int $rgt
     * @return $this
     */
    public function setRightValue($rgt);

    /**
     * @return int
     */
    public function getLevel();

    /**
     * @param mixed $level
     * @return $this
     */
    public function setLevel($level);
}