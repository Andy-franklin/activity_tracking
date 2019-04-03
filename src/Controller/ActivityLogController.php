<?php

namespace App\Controller;

use App\Entity\ActivityLog;
use App\EventSubscriber\ActivityLogUploadSubscriber;
use App\Form\ActivityLogType;
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
     * ActivityLogController constructor.
     *
     * @param EntityManagerInterface      $entityManager
     * @param ActivityLogUploadSubscriber $activityLogUploadSubscriber
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        ActivityLogUploadSubscriber $activityLogUploadSubscriber
    )
    {
        $this->entityManager = $entityManager;
        $this->activityLogUploadSubscriber = $activityLogUploadSubscriber;
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

            $this->entityManager->persist($activityLog);
            $this->entityManager->flush();

            $this->addFlash('success', 'Your file has been added to the queue for processing');
            $this->activityLogUploadSubscriber->setActivityLog($activityLog);

            return $this->redirectToRoute('index');
        }

        return $this->render('activityLog/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
