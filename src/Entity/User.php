<?php


namespace App\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="Tag", mappedBy="user")
     */
    private $tags;

    /**
     * @ORM\OneToMany(targetEntity="TaskTracker", mappedBy="user")
     */
    private $taskTrackers;

    /**
     * @return mixed
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @return mixed
     */
    public function getTaskTrackers()
    {
        return $this->taskTrackers;
    }

    /**
     * @param mixed $taskTrackers
     *
     * @return User
     */
    public function setTaskTrackers($taskTrackers)
    {
        $this->taskTrackers = $taskTrackers;

        return $this;
    }
}
