<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity()
 * @ORM\Table(name="link_structures")
 */
class LinkStructure
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $alias;

    //https://redmine.thedrum.com/issues/:p1/time_entries/new
    //https://redmine.thedrum.com/issues/:p1
    //https://trello.com/c/:p1
    /**
     * @ORM\Column(type="string")
     * @Assert\Url(message = "The url '{{ value }}' is not a valid url")
     */
    private $structure;

    /**
     * @ORM\ManyToOne(targetEntity="TaskTracker", inversedBy="linkStructures")
     */
    private $taskTracker;

    /**
     * @ORM\OneToMany(targetEntity="ActivityItemTaskLink", mappedBy="linkStructure")
     */
    private $activityItemTaskLinks;

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
     * @return LinkStructure
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @param mixed $alias
     *
     * @return LinkStructure
     */
    public function setAlias($alias)
    {
        $this->alias = $alias;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getStructure()
    {
        return $this->structure;
    }

    /**
     * @param mixed $structure
     *
     * @return LinkStructure
     */
    public function setStructure($structure)
    {
        $this->structure = $structure;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTaskTracker()
    {
        return $this->taskTracker;
    }

    /**
     * @param mixed $taskTracker
     *
     * @return LinkStructure
     */
    public function setTaskTracker($taskTracker)
    {
        $this->taskTracker = $taskTracker;

        return $this;
    }
}
