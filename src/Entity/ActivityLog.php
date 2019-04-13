<?php

namespace App\Entity;

use App\Exception\UnauthorizedException;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ActivityLogRepository")
 * @ORM\Table(name="activity_log_files")
 */
class ActivityLog implements UserResourceInterface
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
     * @ORM\Column(type="string", nullable=true)
     */
    private $contentHash;

    /**
     * @ORM\OneToMany(targetEntity="ActivityItem", mappedBy="activityLog")
     */
    private $activityItems;

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

    /**
     * @return mixed
     */
    public function getContentHash()
    {
        return $this->contentHash;
    }

    /**
     * @param mixed $contentHash
     *
     * @return ActivityLog
     */
    public function setContentHash($contentHash)
    {
        $this->contentHash = $contentHash;

        return $this;
    }

    /**
     * @param User $user
     */
    public function denyUnlessOwner(User $user): void
    {
        if ($this->user !== $user) {
            throw new UnauthorizedException();
        }
    }

    /**
     * @return mixed
     */
    public function getActivityItems()
    {
        return $this->activityItems;
    }
}
