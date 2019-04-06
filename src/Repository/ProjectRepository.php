<?php

namespace App\Repository;

use App\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class ProjectRepository extends ServiceEntityRepository
{
    /**
     * TagRepository constructor.
     *
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Project::class);
    }

    public function findActivitiesForProjectInDateRange($project, $startingDate, $endingDate)
    {
        return $this->createQueryBuilder('p')
            ->addSelect('p')
            ->addSelect('t')
            ->addSelect('ai')
            ->innerJoin('p.tags', 't')
            ->innerJoin('t.activityItems', 'ai')
            ->innerJoin('ai.activityLog', 'al')
            ->andWhere('p.user = al.user')
            ->getQuery()
            ->getResult()
        ;
    }
}
