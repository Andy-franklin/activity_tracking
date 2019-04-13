<?php

namespace App\Controller;

use App\Entity\ActivityItem;
use App\Entity\Author;
use App\Entity\PlannedHours;
use App\Entity\Project;
use App\Exception\NonMondayException;
use App\Form\ProjectType;
use App\Repository\ActivityItemRepository;
use App\Repository\AuthorRepository;
use App\Repository\PlannedHoursRepository;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class ProjectController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var ActivityItemRepository
     */
    private $activityItemRepository;
    /**
     * @var AuthorRepository
     */
    private $authorRepository;
    /**
     * @var PlannedHoursRepository
     */
    private $plannedHoursRepository;

    /**
     * ProjectController constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param ProjectRepository      $projectRepository
     * @param ActivityItemRepository $activityItemRepository
     * @param AuthorRepository       $authorRepository
     * @param PlannedHoursRepository $plannedHoursRepository
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        ProjectRepository $projectRepository,
        ActivityItemRepository $activityItemRepository,
        AuthorRepository $authorRepository,
        PlannedHoursRepository $plannedHoursRepository
    )
    {
        $this->entityManager = $entityManager;
        $this->activityItemRepository = $activityItemRepository;
        $this->authorRepository = $authorRepository;
        $this->plannedHoursRepository = $plannedHoursRepository;
    }

    public function new(Request $request)
    {
        $user = $this->getUser();
        $project = new Project();

        $form = $this->createForm(ProjectType::class, $project, ['user' => $user]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $project->setUser($user);

            $this->entityManager->persist($project);
            $this->entityManager->flush();

            $this->addFlash('success', 'Your project has been created.');

            return $this->redirectToRoute('index');
        }

        return $this->render('project/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function single(Request $request, Project $project)
    {
        $project->denyUnlessOwner($this->getUser());

        $weekCommencing = $request->get('weekCommencing');
        if (null !== $weekCommencing) {
            try {
                $monday = \DateTimeImmutable::createFromFormat('d-m-Y', $weekCommencing)->setTime(0,0,0,0);
                $sunday = $monday->add(\DateInterval::createFromDateString('+ 7 days'));
            } catch (\Exception $exception) {
                throw new \RuntimeException('The date format is incorrect');
            }

            if ($monday->format('l') !== 'Monday') {
                //If we aren't on a monday throw an error
                throw new NonMondayException(sprintf('Expected Monday but got %s', $monday->format('l')));
            }
        } else {
            $monday = new \DateTime('Monday this week');
            $sunday = new \DateTime('Sunday this week');
        }

        $projectActivities = $this->activityItemRepository->findActivitiesForProjectInDateRange($project, $monday, $sunday);

        /** @var ActivityItem $activity */
        $authorDurations = [];
        foreach ($projectActivities as $activity) {
            $author = $activity->getAuthor();
            $authorId = $author->getId();
            if (isset($authorDurations[$authorId])) {
                $interval = $authorDurations[$authorId]['duration'];
                $interval = $this->addDateIntervals($interval, $activity->getDuration());
            } else {
                $interval = $activity->getDuration();
            }

            $authorDurations[$authorId] = [
                'duration' => $interval,
                'authorName' => $author->getName()
            ];
        }

        $authors = $this->authorRepository->findBy(['user' => $this->getUser()]);
        /** @var Author $author */
        foreach ($authors as $author) {
            $authorId = $author->getId();

            /** @var PlannedHours $authorPlannedHours */
            $authorPlannedHours = $this->plannedHoursRepository->findOneBy([
                'weekCommencing' => $monday,
                'author' => $author,
                'project' => $project
            ]);

            if (!isset($authorDurations[$authorId])) {
                $authorDurations[$authorId] = [
                    'duration' => new \DateInterval('PT00S'),
                    'authorName' => $author->getName(),
                ];
            }

            $authorDurations[$authorId]['plannedHours'] = $authorPlannedHours === null ? 0 : $authorPlannedHours->getHours();
        }

        return $this->render('project/single.html.twig', [
            'monday' => $monday,
            'saturday' => $sunday,
            'project' => $project,
            'projectActivities' => $projectActivities,
            'authorDurations' => $authorDurations
        ]);
    }

    private function addDateIntervals()
    {
        $reference = new \DateTimeImmutable;
        $endTime = clone $reference;

        foreach (\func_get_args() as $dateInterval) {
            $endTime = $endTime->add($dateInterval);
        }

        return $reference->diff($endTime);
    }
}
