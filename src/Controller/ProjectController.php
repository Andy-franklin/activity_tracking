<?php

namespace App\Controller;

use App\Entity\Project;
use App\Form\ProjectType;
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
     * ProjectController constructor.
     *
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
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
}
