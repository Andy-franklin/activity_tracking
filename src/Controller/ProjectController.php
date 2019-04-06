<?php

namespace App\Controller;

use App\Entity\ActivityItem;
use App\Entity\Project;
use App\Form\ProjectType;
use App\Repository\ActivityItemRepository;
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
     * @var ProjectRepository
     */
    private $projectRepository;
    /**
     * @var ActivityItemRepository
     */
    private $activityItemRepository;

    /**
     * ProjectController constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param ProjectRepository      $projectRepository
     * @param ActivityItemRepository $activityItemRepository
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        ProjectRepository $projectRepository,
        ActivityItemRepository $activityItemRepository
    )
    {
        $this->entityManager = $entityManager;
        $this->projectRepository = $projectRepository;
        $this->activityItemRepository = $activityItemRepository;
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

    public function single(Project $project)
    {
        $monday = new \DateTime('Monday this week');
        $saturday = new \DateTime('Saturday this week');
        $projectActivities = $this->activityItemRepository->findActivitiesForProjectInDateRange($project, $monday, $saturday);

        /** @var ActivityItem $activity */
        $authorDurations = [];
        foreach ($projectActivities as $activity) {
            if (isset($authorDurations[$activity->getAuthor()->getName()])) {
                $interval = $authorDurations[$activity->getAuthor()->getName()];
                $interval = $this->addDateIntervals($interval, $activity->getDuration());
            } else {
                $interval = $activity->getDuration();
            }

            $authorDurations[$activity->getAuthor()->getName()] = $interval;
        }

        return $this->render('project/single.html.twig', [
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
