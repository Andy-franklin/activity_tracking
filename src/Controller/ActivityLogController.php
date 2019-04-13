<?php

namespace App\Controller;

use App\Entity\ActivityItem;
use App\Entity\ActivityLog;
use App\Entity\Tag;
use App\EventSubscriber\ActivityLogUploadSubscriber;
use App\Form\ActivityLogType;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

class ActivityLogController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var ActivityLogUploadSubscriber
     */
    private $activityLogUploadSubscriber;
    /**
     * @var ProjectRepository
     */
    private $projectRepository;

    /**
     * ActivityLogController constructor.
     *
     * @param EntityManagerInterface      $entityManager
     * @param ActivityLogUploadSubscriber $activityLogUploadSubscriber
     * @param ProjectRepository           $projectRepository
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        ActivityLogUploadSubscriber $activityLogUploadSubscriber,
        ProjectRepository $projectRepository
    )
    {
        $this->entityManager = $entityManager;
        $this->activityLogUploadSubscriber = $activityLogUploadSubscriber;
        $this->projectRepository = $projectRepository;
    }

    public function new(Request $request)
    {
        $activityLog = new ActivityLog();

        $form = $this->createForm(ActivityLogType::class, $activityLog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $file */
            $file = $activityLog->getUploadedLog();

            $fileName = md5(uniqid('', true)).'.'.$file->guessExtension();

            try {
                $file->move(
                    $this->getParameter('activity_log_directory'),
                    $fileName
                );
            } catch (FileException $exception) {
                throw new \RuntimeException('There was an error uploading your file. Sorry about that.');
            }

            $activityLog->setUploadedLog($fileName);
            $activityLog->setUser($this->getUser());

            if (true === $this->activityLogUploadSubscriber->setActivityLog($activityLog)) {
                $this->addFlash('success', 'Your file has been added to the queue for processing');

                $this->entityManager->persist($activityLog);
                $this->entityManager->flush();

                return $this->redirectToRoute('authed_dashboard');
            }

            $this->addFlash('error', 'You have already processed this activity file!');
        }

        return $this->render('activityLog/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function single(Request $request, ActivityLog $activityLog)
    {
        $activityLog->denyUnlessOwner($this->getUser());

        $categorisedActivityItems = $uncategorisedActivityItems = [];

        /** @var ActivityItem $activityItem */
        foreach ($activityLog->getActivityItems() as $activityItem) {
            $categorised = false;
            $tags = $activityItem->getTags();

            /** @var Tag $tag */
            foreach ($tags as $tag) {
                $projects = $tag->getProjects();

                if (\count($projects) > 0) {
                    $categorisedActivityItems[] = $activityItem;
                    $categorised = true;
                    break;
                }
            }

            if (false === $categorised) {
                $uncategorisedActivityItems[] = $activityItem;
            }
        }

        return $this->render('activityLog/single.html.twig', [
            'activityItems' => [
                'uncategorised' => $uncategorisedActivityItems,
                'categorised' => $categorisedActivityItems,
            ],
            'activityLog' => $activityLog
        ]);
    }
}
