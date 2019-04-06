<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TagRepository")
 * @ORM\Table(name="tag")
 */
class Tag
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
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="tags")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\ManyToMany(targetEntity="Project", mappedBy="tags")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id")
     */
    private $projects;

    /**
     * @ORM\ManyToMany(targetEntity="ActivityItem", mappedBy="tags")
     * @ORM\JoinColumn(name="activity_item_id", referencedColumnName="id")
     */
    private $activityItems;

    /**
     * Tag constructor.
     *
     * @param string $name
     * @param User $user
     */
    public function __construct(string $name, User $user)
    {
        $this->name = $name;
        $this->user = $user;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    public function __toString()
    {
        return $this->name;
    }
}
