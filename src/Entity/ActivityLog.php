<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="")
 * @ORM\Table(name="activity_log_files")
 */
class ActivityLog
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\Column(type="string")
     *
     * @Assert\NotBlank(message="Please, upload the activity log as a CSV file.")
     * @Assert\File(mimeTypes={ "text/plain" })
     */
    private $uploadedLog;

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
     * @return ActivityLog
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     *
     * @return ActivityLog
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUploadedLog()
    {
        return $this->uploadedLog;
    }

    /**
     * @param mixed $uploadedLog
     *
     * @return ActivityLog
     */
    public function setUploadedLog($uploadedLog)
    {
        $this->uploadedLog = $uploadedLog;

        return $this;
    }
}
