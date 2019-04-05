<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="activity_items")
 */
class ActivityItem
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    private $title;

    /**
     * @ORM\ManyToMany(targetEntity="Tag")
     * @ORM\JoinColumn(name="tag_id", referencedColumnName="id")
     */
    private $tags;

    /**
     * @ORM\ManyToOne(targetEntity="Author", cascade={"persist"})
     */
    private $author;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $startTime;

    /**
     * @ORM\Column(type="dateinterval", nullable=true)
     */
    private $duration;

    /**
     * @ORM\ManyToOne(targetEntity="ActivityLog")
     * @ORM\JoinColumn(name="activity_log_id", referencedColumnName="id")
     */
    private $activityLog;

    /**
     * ActivityItem constructor.
     *
     * @param string      $title
     * @param             $tags
     * @param Author      $author
     * @param \DateTime   $startTime
     * @param ActivityLog $activityLog
     */
    public function __construct(string $title, $tags, Author $author, \DateTime $startTime, ActivityLog $activityLog)
    {
        $this->title     = $title;
        $this->tags      = $tags;
        $this->author    = $author;
        $this->startTime = $startTime;
        $this->activityLog = $activityLog;
    }

    /**
     * @return mixed
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * @param mixed $duration
     *
     * @return ActivityItem
     */
    public function setDuration(\DateInterval $duration): ActivityItem
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAuthor()
    {
        return $this->author;
    }
}
