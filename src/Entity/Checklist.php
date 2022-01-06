<?php

namespace App\Entity;

use App\Repository\ChecklistRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;

/**
 * @ORM\Entity(repositoryClass=ChecklistRepository::class)
 */
class Checklist implements \JsonSerializable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="checklists")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $message;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $unique_id;

    /**
     * @ORM\OneToMany(targetEntity=Task::class, mappedBy="checklist")
     */
    private $tasks;


    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'author' => $this->author->jsonSerialize(),
            'unique_id' => $this->unique_id,
            'tasks' => array_map(function (Task $task) {
                return $task->jsonSerialize();
            }, $this->tasks instanceof PersistentCollection ? $this->tasks->toArray() : $this->tasks)
        ];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getTasks()
    {
        return $this->tasks;
    }

    /**
     * @param mixed $tasks
     */
    public function setTasks($tasks): void
    {
        $this->tasks = $tasks;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getUniqueId(): ?string
    {
        return $this->unique_id;
    }

    public function setUniqueId(string $unique_id): self
    {
        $this->unique_id = $unique_id;

        return $this;
    }
}
