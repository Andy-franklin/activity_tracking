<?php

namespace App\EventSubscriber;

use App\Entity\ActivityItem;
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
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
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

        array_shift($rawActivityData);

        $user = $this->activityLog->getUser();
        $activityItems = [];

        foreach ($rawActivityData as $rawActivityDatum) {
            $rawAuthor = $rawActivityDatum[4]; //todo refactor with list()
            $rawTitle = $rawActivityDatum[3];
            $rawStartTime = $rawActivityDatum[1];
            $rawTags = $this->extractTagStrings($rawTitle);
            $rawTags = array_unique($rawTags);

            $startTime = new \DateTime($rawStartTime);

            $tags = [];
            foreach ($rawTags as $rawTag) {
                /** @noinspection PhpUnhandledExceptionInspection */
                $existingTag = $this->tagRepository->findOneByNameAndUser($rawTag, $user);
                if (null === $existingTag) {
                    $tag = new Tag($rawTag, $user);
                    $this->entityManager->persist($tag);
                    $tags[] = $tag;
                }
            }

            //Set the current activityItem
            $activityItem = new ActivityItem($rawTitle, $tags, $rawAuthor, $startTime, $this->activityLog);
            $this->entityManager->persist($activityItem);
            $activityItems[] = $activityItem;

            //Flush here so that the next activity tags are unique from database
            //We also flush each single activityItem in its entirety - including
            //any pause/resume tags or any other special tags. This is so that we
            //can allow the user to go back and change these tags and rerun processing
            $this->entityManager->flush();
        }

        //We have all our activity items - calculate the duration of each item per author.
        /** @var ActivityItem $currentActivityItem */
        foreach ($activityItems as $key => $currentActivityItem) {
            if (isset($activityItems[$key+1]) && $currentActivityItem->getAuthor() === $activityItems[$key+1]->getAuthor()) {
                $nextTask = $activityItems[$key+1];
            } else {
                //This is the end of the tasks for this user.
                //Todo: Ensure that the csv is grouped by author and ordered by startTime.
                $nextTask = null;
            }
            /** @var \DateTime $timeOfTask */
            $timeOfTask = $currentActivityItem->getStartTime();

            if (null !== $nextTask) {
                //We have another task after this, current task ended when
                //the next one started
                $endTime = $nextTask->getStartTime();
            } else {
                //There is no next activity - if this time is after home time
                //we should assume that this is a 5:30 finish unless the time is
                //already past 5:30, then we should go just ignore this duration
                $hour = $timeOfTask->format('H');
                $minute = $timeOfTask->format('mm');

                if ($hour <= 5 && $minute < 30) { //todo: extract to parameters
                    //Home time!
                    $endTime = $timeOfTask->setTime(5, 30, 00);
                } else {
                    //todo: we need to check if this is a #home tag before doing this
                    $endTime = $timeOfTask;
                }
            }

            $duration = $timeOfTask->diff($endTime);
            $currentActivityItem->setDuration($duration);
            $this->entityManager->persist($currentActivityItem);
        }
        $this->entityManager->flush();
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