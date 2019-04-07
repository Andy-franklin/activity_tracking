<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PlannedHoursRepository")
 * @ORM\Table(name="planned_hours")
 */
class PlannedHours
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Project")
     */
    private $project;

    /**
     * @ORM\ManyToOne(targetEntity="Author")
     */
    private $author;

    /**
     * @ORM\Column(type="integer")
     */
    private $hours;

    /**
     * @ORM\Column(type="datetime")
     */
    private $weekCommencing;

    /**
     * @return mixed
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * @param mixed $project
     *
     * @return PlannedHours
     */
    public function setProject($project)
    {
        $this->project = $project;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param mixed $author
     *
     * @return PlannedHours
     */
    public function setAuthor($author)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getHours()
    {
        return $this->hours;
    }

    /**
     * @param mixed $hours
     *
     * @return PlannedHours
     */
    public function setHours($hours)
    {
        $this->hours = $hours;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getWeekCommencing()
    {
        return $this->weekCommencing;
    }

    /**
     * @param mixed $weekCommencing
     *
     * @return PlannedHours
     */
    public function setWeekCommencing($weekCommencing)
    {
        $this->weekCommencing = $weekCommencing;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }
}
