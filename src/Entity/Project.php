<?php


namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProjectRepository")
 * @ORM\Table(name="project")
 */
class Project
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="User")
     */
    private $user;

    /**
     * @ORM\ManyToMany(targetEntity="Tag", inversedBy="projects")
     * @ORM\JoinColumn(name="tag_id", referencedColumnName="id")
     */
    private $tags;

    /**
     * @ORM\Column(type="string")
     */
    private $title;

    public function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    public function setTags($tags)
    {
        $this->tags = $tags;
        return $this;
    }

    public function getTags()
    {
        return $this->tags;
    }

    public function getId()
    {
        return $this->id;
    }
}
