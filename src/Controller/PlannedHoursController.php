<?php

namespace App\Controller;

use App\Entity\PlannedHours;
use App\Exception\NonMondayException;
use App\Exception\UnauthorizedException;
use App\Repository\AuthorRepository;
use App\Repository\PlannedHoursRepository;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class PlannedHoursController extends AbstractController
{
    /**
     * @var ProjectRepository
     */
    private $projectRepository;
    /**
     * @var AuthorRepository
     */
    private $authorRepository;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var PlannedHoursRepository
     */
    private $plannedHoursRepository;

    /**
     * PlannedHoursController constructor.
     *
     * @param ProjectRepository      $projectRepository
     * @param AuthorRepository       $authorRepository
     * @param EntityManagerInterface $entityManager
     * @param PlannedHoursRepository $plannedHoursRepository
     */
    public function __construct(
        ProjectRepository $projectRepository,
        AuthorRepository $authorRepository,
        EntityManagerInterface $entityManager,
        PlannedHoursRepository $plannedHoursRepository
    )
    {
        $this->projectRepository = $projectRepository;
        $this->authorRepository = $authorRepository;
        $this->entityManager = $entityManager;
        $this->plannedHoursRepository = $plannedHoursRepository;
    }

    public function createOrUpdate(Request $request)
    {
        //Raw request data
        $projectId = $request->get('projectId');
        $weekCommencing = $request->get('weekCommencing');
        $plannedHours = $request->get('plannedHours');

        $user = $this->getUser();
        $project = $this->projectRepository->findOneBy(['user' => $user, 'id' => $projectId]);
        if (null === $project) {
            throw new UnauthorizedException('Project not found');
        }

        $monday = \DateTimeImmutable::createFromFormat('d-m-Y', $weekCommencing)->setTime(0,0,0,0);
        if ($monday->format('l') !== 'Monday') {
            throw new NonMondayException(sprintf('Expected Monday but got %s', $monday->format('l')));
        }

        foreach ($plannedHours as $plannedHour) {
            $authorId = $plannedHour['authorId'];
            $author = $this->authorRepository->findOneBy(['id' => $authorId, 'user' => $user]);
            if (null === $author) {
                throw new UnauthorizedException('Author not found');
            }
            $hours = $plannedHour['hours'];
            if (!is_numeric($hours)) {
                throw new UnauthorizedException('Project not found');
            }

            /** @var PlannedHours $existingPlannedHours */
            $existingPlannedHours = $this->plannedHoursRepository->findOneBy([
                'project' => $project,
                'author' => $author,
                'weekCommencing' => $monday
            ]);

            if (null !== $existingPlannedHours) {
                $existingPlannedHours->setHours($hours);
                $this->entityManager->persist($existingPlannedHours);
            } else {
                $this->entityManager->persist((new PlannedHours())
                    ->setAuthor($author)
                    ->setWeekCommencing($monday)
                    ->setProject($project)
                    ->setHours($hours))
                ;
            }
        }

        $this->entityManager->flush();

        return new JsonResponse('', 200);
    }
}
