<?php
/**
 * Created by PhpStorm.
 * User: sgueye
 * Date: 1/20/2017
 * Time: 10:46 AM
 */

namespace Mms\Organizations\Eloquent;


use Illuminate\Database\Eloquent\Model;
use Mms\Organizations\OrganizationReferenceManager;

class OrganizationAccessChecker
{
    /**
     * @var OrganizationAccessManager
     */
    private $accessManager;

    /**
     * @var OrganizationReferenceManager
     */
    private $referenceManager;

    /**
     * OrganizationAccessChecker constructor.
     * @param OrganizationAccessManager $accessManager
     * @param OrganizationReferenceManager $referenceManager
     */
    public function __construct(
        OrganizationAccessManager $accessManager,
        OrganizationReferenceManager $referenceManager
    ){
        $this->accessManager    = $accessManager;
        $this->referenceManager = $referenceManager;
    }
    public function hasAccess(Model $user, OrganizationInterface $organization)
    {
        $year = $organization->year;
        $organizationNodes = $this->referenceManager->getUserReferenceNodesForYear($user, $year);
        return $this->accessManager->isAccessible($organization, $organizationNodes);
    }
}