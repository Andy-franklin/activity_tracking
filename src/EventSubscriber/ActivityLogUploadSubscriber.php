<?php

namespace App\EventSubscriber;

use App\Entity\ActivityLog;
use App\Entity\Tag;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\PostResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ActivityLogUploadSubscriber implements EventSubscriberInterface
{
    /**
     * @var ActivityLog
     */
    private $activityLog;

    /**
     * @var string
     */
    private $activityLogDirectory;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var TagRepository
     */
    private $tagRepository;

    /**
     * ActivityLogUploadSubscriber constructor.
     *
     * @param string                 $activityLogDirectory
     * @param EntityManagerInterface $entityManager
     * @param TagRepository          $tagRepository
     */
    public function __construct(
        string $activityLogDirectory,
        EntityManagerInterface $entityManager,
        TagRepository $tagRepository
    )
    {
        $this->activityLogDirectory = $activityLogDirectory;
        $this->entityManager = $entityManager;
        $this->tagRepository = $tagRepository;
    }


    public function setActivityLog(ActivityLog $activityLog): void
    {
        $this->activityLog = $activityLog;
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::TERMINATE => [
                ['processActivityLogFile', 0]
            ]
        ];
    }

    /**
     * Processes the uploaded CSV file into the individual activities within
     * the log.
     *
     * @param PostResponseEvent $event
     */
    public function processActivityLogFile(PostResponseEvent $event): void
    {
        if (null === $this->activityLog) {
            return;
        }

        $filename = $this->activityLogDirectory . '/' .$this->activityLog->getUploadedLog();

        $rawActivityData = [];
        if (($handle = fopen($filename, 'rb')) !== false) {
            while (($data = fgetcsv($handle, ',')) !== false) {
                $rawActivityData[] = $data;
            }
        }
        fclose($handle);

        $user = $this->activityLog->getUser();
        foreach ($rawActivityData as $rawActivityDatum) {
            $rawTags = $this->extractTagStrings($rawActivityDatum[3]);
            $rawTags = array_unique($rawTags);

            foreach ($rawTags as $rawTag) {
                /** @noinspection PhpUnhandledExceptionInspection */
                $existingTag = $this->tagRepository->findOneByNameAndUser($rawTag, $user);
                if (null === $existingTag) {
                    $tag = new Tag($rawTag, $user);
                    $this->entityManager->persist($tag);
                }
            }
            $this->entityManager->flush();
        }

    }

    private function extractTagStrings($titleString): array
    {
        preg_match_all('/(#\w+)/', $titleString, $matches);

        $keywords = [];
        foreach ($matches[0] as $tag) {
            $keywords[] = $tag;
        }
        return $keywords;
    }
}
