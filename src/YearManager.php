<?php
/**
 * Created by PhpStorm.
 * User: sgueye
 * Date: 1/18/2017
 * Time: 2:56 PM
 */

namespace Mms\Organizations;

use Illuminate\Http\Request;
use Mms\Laravel\Eloquent\RepositoryInterface;
use Mms\Organizations\Eloquent\YearInterface;

class YearManager
{
    /**
     *
     * @var RepositoryInterface
     */
    private $repository;

    /**
     *
     * @var string
     */
    private $currentYearCode;

    /**
     *
     * @var string
     */
    private $yearsIdIndex = [];

    /**
     *
     * @var string
     */
    private $yearsCodeIndex = [];

    /**
     *
     * @param RepositoryInterface $repository
     * @param string $currentYearCode
     */
    public function __construct(
        RepositoryInterface $repository,
        $currentYearCode
    )
    {
        $this->repository      = $repository;
        $this->currentYearCode = $currentYearCode;
    }

    /**
     *
     * @param string $id
     * @return YearInterface
     */
    public function findYear($id)
    {
        if(empty($this->yearsIdIndex[$id])) {
            $year = $this->repository->find($id);
            if(!$year) {
                throw new \RuntimeException("No year with id=$id found in this context");
            }
            $this->yearsIdIndex[$id] = $year;
            $this->yearsCodeIndex[$year->code] = $year;
        }
        return $this->yearsIdIndex[$id];
    }

    /**
     *
     * @param string $code
     * @return YearInterface
     */
    public function findYearByCode($code)
    {
        if(empty($this->yearsCodeIndex[$code])) {
            $year = $this->repository->findByCode($code);
            if(!$year) {
                throw new \RuntimeException("No year with code=$code found in this context");
            }
            $this->yearsIdIndex[$year->id] = $year;
            $this->yearsCodeIndex[$code] = $year;
        }
        return $this->yearsCodeIndex[$code];
    }

    /**
     *
     * @return YearInterface
     */
    public function getCurrentYear()
    {
        return $this->findYearByCode($this->currentYearCode);
    }

    /**
     * @param YearInterface $year
     * @return YearInterface
     */
    public function getPreviousYear(YearInterface $year)
    {
        $years = explode('-', $year->code);
        $code = implode('-', [(int)$years[0]-1, (int)$years[1]-1]);
        return $this->findYearByCode($code);
    }

    public function getRequestYear(Request $request)
    {

    }
}