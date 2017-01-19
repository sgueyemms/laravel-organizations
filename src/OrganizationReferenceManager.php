<?php
/**
 * Created by PhpStorm.
 * User: sgueye
 * Date: 1/18/2017
 * Time: 2:56 PM
 */

namespace Mms\Organizations;

use Illuminate\Database\Eloquent\Model;
use Mms\Organizations\Eloquent\RelationshipRepositoryInterface;
use Mms\Organizations\Eloquent\YearInterface;

class OrganizationReferenceManager
{
    /**
     *
     * @var RelationshipRepositoryInterface
     */
    private $repository;

    /**
     *
     * @var array
     */
    private $nodeStorage;

    /**
     *
     * @param RelationshipRepositoryInterface $repository
     */
    public function __construct(
        RelationshipRepositoryInterface $repository
    ){
        $this->repository  = $repository;
        $this->nodeStorage = [];
    }

    public function getUserReferences(Model $user)
    {
        $references = $user->organization_references;
        return $references ? $references->all() : [];
    }

    public function getUserReferencesForYear(Model $user, YearInterface $year)
    {
        return array_filter(
            $this->getUserReferences($user),
            function ($reference) use($year) {
                return $reference->organization->year_id == $year->id;
            }
        );
    }

    /**
     * @param array $references
     * @return array
     */
    private function loadReferenceNodes(array $references)
    {
        $data = [];
        foreach ($references as $reference) {
            $organization = $reference->organization;
            $data = array_merge($data, $this->repository->loadOrganizationNodes($organization)->all());
        }

        return $data;
    }

    private function generateKey(Model $user, array $references)
    {
        return $user->id.'_'.implode('_', array_map(function($value) {return $value->id;}, $references));
    }

    private function findNodes(Model $user, $references)
    {
        $key = $this->generateKey($user, $references);
        if(!isset($this->nodeStorage[$key])) {
            $this->nodeStorage[$key] = $this->loadReferenceNodes($references);
        }
        return $this->nodeStorage[$key];
    }

    /**
     * @param Model $user
     * @return mixed
     */
    public function getUserReferenceNodes(Model $user)
    {
        return $this->findNodes($user, $this->getUserReferences($user));
    }

    /**
     * @param Model $user
     * @param YearInterface $year
     * @return mixed
     */
    public function getUserReferenceNodesForYear(Model $user, YearInterface $year)
    {
        return $this->findNodes($user, $this->getUserReferencesForYear($user, $year));
    }
}