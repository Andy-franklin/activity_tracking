<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ActivityItemRepository")
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
     * @ORM\ManyToMany(targetEntity="Tag", inversedBy="activityItems")
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
     * @ORM\ManyToOne(targetEntity="ActivityLog", inversedBy="activityItems")
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
    public function __construct(
        string $title,
        $tags,
        Author $author,
        \DateTime $startTime,
        ActivityLog $activityLog
    ) {
        $this->title       = $title;
        $this->tags        = $tags;
        $this->author      = $author;
        $this->startTime   = $startTime;
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
     * @return mixed
     */
    public function getDuration()
    {
        return $this->duration;
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
    public function getAuthor(): ?Author
    {
        return $this->author;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     *
     * @return ActivityItem
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     *
     * @return ActivityItem
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param mixed $tags
     *
     * @return ActivityItem
     */
    public function setTags($tags)
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getActivityLog()
    {
        return $this->activityLog;
    }

    /**
     * @param mixed $activityLog
     *
     * @return ActivityItem
     */
    public function setActivityLog($activityLog)
    {
        $this->activityLog = $activityLog;

        return $this;
    }
}
