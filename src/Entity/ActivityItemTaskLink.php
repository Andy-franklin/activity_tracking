<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="activity_item_task_link")
 */
class ActivityItemTaskLink
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="ActivityItem", inversedBy="taskLinks", cascade={"persist"})
     */
    private $activityItem;

    /**
     * @ORM\Column(type="string")
     */
    private $parameter;

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
     * @return ActivityItemTaskLink
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getParameter()
    {
        return $this->parameter;
    }

    /**
     * @param mixed $parameter
     *
     * @return ActivityItemTaskLink
     */
    public function setParameter($parameter)
    {
        $this->parameter = $parameter;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getActivityItem()
    {
        return $this->activityItem;
    }

    /**
     * @param mixed $activityItem
     *
     * @return ActivityItemTaskLink
     */
    public function setActivityItem($activityItem)
    {
        $this->activityItem = $activityItem;
        return $this;
    }
}
