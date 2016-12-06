<?php
/**
 * Created by PhpStorm.
 * User: sgueye
 * Date: 12/5/2016
 * Time: 3:38 PM
 */

namespace Mms\Organizations\Eloquent;


interface OrganizationInterface
{
    public function processYear(YearInterface $year);

    /**
     * @return int
     */
    public function getReferenceIdentifier();

    /**
     * @return mixed
     */
    public function getReference();

    /**
     * @param mixed $reference
     */
    public function setReference($reference);

}