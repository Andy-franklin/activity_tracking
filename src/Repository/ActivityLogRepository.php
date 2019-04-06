<?php

namespace App\Repository;

use App\Entity\ActivityLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class ActivityLogRepository extends ServiceEntityRepository
{
    /**
     * TagRepository constructor.
     *
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ActivityLog::class);
    }
}
