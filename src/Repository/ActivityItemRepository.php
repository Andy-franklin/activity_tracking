<?php


namespace App\Repository;


use App\Entity\ActivityItem;
use App\Entity\Project;
use App\Entity\Tag;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class ActivityItemRepository extends ServiceEntityRepository
{
    /**
     * TagRepository constructor.
     *
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ActivityItem::class);
    }

    public function findActivitiesForProjectInDateRange(Project $project, $startingDate, $endingDate)
    {
        $projectTags = $project->getTags();

        /** @var Tag $tag */
        $projectTagsIds = [];
        foreach ($projectTags as $tag) {
            $projectTagsIds[] = $tag->getId();
        }

        return $this->createQueryBuilder('ai')
            ->innerJoin('ai.tags', 't')
            ->andWhere('t.id IN (:tagIds)')
            ->setParameter(':tagIds', implode(',', $projectTagsIds))
            ->andWhere('ai.startTime > :startingDate')
            ->setParameter(':startingDate', $startingDate)
            ->andWhere('ai.startTime < :endingDate')
            ->setParameter(':endingDate', $endingDate)
            ->getQuery()
            ->getResult()
        ;
    }
}
