<?php

namespace App\Infrastructure\Doctrine\Repository;

use App\Shared\Domain\Criteria\Criteria;
use App\Infrastructure\Doctrine\Criteria\CriteriaConverter;

trait DoctrineRepository
{
    protected static $criteriaToDoctrineFields = [
        'id'        => 'id',
        'file_id'        => 'file_id'
    ];

    public function transformCriteria(Criteria $criteria)
    {
        return CriteriaConverter::convert($criteria, self::$criteriaToDoctrineFields);
    }
}
