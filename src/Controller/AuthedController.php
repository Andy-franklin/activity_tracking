<?php

namespace App\Controller;

use App\Repository\ActivityLogRepository;
use App\Repository\AuthorRepository;
use App\Repository\ProjectRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AuthedController extends AbstractController
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
     * @var ActivityLogRepository
     */
    private $activityLogRepository;

    /**
     * AuthedController constructor.
     *
     * @param ProjectRepository     $projectRepository
     * @param ActivityLogRepository $activityLogRepository
     * @param AuthorRepository      $authorRepository
     */
    public function __construct(
        ProjectRepository $projectRepository,
        ActivityLogRepository $activityLogRepository,
        AuthorRepository $authorRepository
    )
    {
        $this->projectRepository = $projectRepository;
        $this->activityLogRepository = $activityLogRepository;
        $this->authorRepository = $authorRepository;
    }

    public function index()
    {
        $user = $this->getUser();

        $projects = $this->projectRepository->findBy(['user' => $user]);
        $activityLogs = $this->activityLogRepository->findBy(['user' => $user]);
        $authors = $this->authorRepository->findBy(['user' => $user]);

        return $this->render('authed/index.html.twig', [
            'projects' => $projects,
            'activityLogs' => $activityLogs,
            'authors' => $authors,
        ]);
    }
}
