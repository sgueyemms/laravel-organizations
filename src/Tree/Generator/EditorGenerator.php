<?php
/**
 * Created by PhpStorm.
 * User: sgueye
 * Date: 12/7/2016
 * Time: 3:05 PM
 */

namespace Mms\Organizations\Tree\Generator;


use Tree\Node\NodeInterface;
use Mms\Organizations\Tree\ArrayGenerator;

class EditorGenerator extends ArrayGenerator
{

    protected function generateExtra(NodeInterface $node)
    {
        return [
            'organization_id' => $node->getValue()->organization->id,
            'organization_type_id' => $node->getValue()->organization->organization_type_id,
        ];
    }
}