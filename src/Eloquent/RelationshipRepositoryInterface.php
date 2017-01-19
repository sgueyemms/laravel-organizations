<?php
/**
 * Created by PhpStorm.
 * User: sgueye
 * Date: 1/19/2017
 * Time: 10:51 AM
 */

namespace Mms\Organizations\Eloquent;


use Baum\Node as BaumNestedSetNode;
use Mms\Laravel\Eloquent\RepositoryInterface;

interface RelationshipRepositoryInterface extends RepositoryInterface
{
    /**
     * @param OrganizationInterface $organization
     * @return BaumNestedSetNode[]
     */
    public function loadOrganizationNodes(OrganizationInterface $organization);
}