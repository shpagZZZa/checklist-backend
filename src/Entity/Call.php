<?php

namespace App\Entity;

use App\Repository\CallRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;

/**
 * @ORM\Entity(repositoryClass=CallRepository::class)
 * @ORM\Table(name="`checklist_call`")
 */
class Call implements \JsonSerializable
{
    public const STATUS_OPEN = 'open';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="calls")
     * @ORM\JoinColumn(nullable=false)
     */
    private $fromUser;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="calls")
     * @ORM\JoinColumn(nullable=false)
     */
    private $toUser;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $message;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $link;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $approvalType;

    /**
     * @ORM\OneToMany(targetEntity=Task::class, mappedBy="taskCall")
     */
    private $tasks;

    public function __construct()
    {
        $this->tasks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFromUser(): ?User
    {
        return $this->fromUser;
    }

    public function setFromUser(?User $fromUser): self
    {
        $this->fromUser = $fromUser;

        return $this;
    }

    public function getToUser(): ?User
    {
        return $this->toUser;
    }

    public function setToUser(?User $toUser): self
    {
        $this->toUser = $toUser;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(string $link): self
    {
        $this->link = $link;

        return $this;
    }

    public function getApprovalType(): ?string
    {
        return $this->approvalType;
    }

    public function setApprovalType(string $approvalType): self
    {
        $this->approvalType = $approvalType;

        return $this;
    }

    public function jsonSerialize()
    {
            return [
                'id' => $this->id,
                'status' => $this->status,
                'approvalType' => $this->approvalType,
                'from' => $this->fromUser->jsonSerialize(),
                'to' => $this->toUser->jsonSerialize(),
                'tasks' => array_map(function (Task $task) {
                    return $task->jsonSerialize();
                }, $this->tasks instanceof PersistentCollection ? $this->tasks->toArray() : $this->tasks)
            ];
    }

    /**
     * @param array $tasks
     */
    public function setTasks(array $tasks): void
    {
        $this->tasks = $tasks;
    }

    /**
     * @return Collection|Task[]
     */
    public function getTasks(): Collection
    {
        return $this->tasks;
    }

    public function addTask(Task $task): self
    {
        if (!$this->tasks->contains($task)) {
            $this->tasks[] = $task;
            $task->setTaskCall($this);
        }

        return $this;
    }

    public function removeTask(Task $task): self
    {
        if ($this->tasks->removeElement($task)) {
            // set the owning side to null (unless already changed)
            if ($task->getTaskCall() === $this) {
                $task->setTaskCall(null);
            }
        }

        return $this;
    }
}
