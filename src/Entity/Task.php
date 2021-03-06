<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;

/**
 * @ORM\Entity(repositoryClass=TaskRepository::class)
 */
class Task implements \JsonSerializable
{
    public const STATUS_OPEN = 'task_open';
    public const STATUS_CLOSED = 'task_closed';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="tasks")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * @ORM\OneToMany(targetEntity=Goal::class, mappedBy="task")
     */
    private $goals;

    /**
     * @ORM\ManyToOne(targetEntity=Checklist::class, inversedBy="tasks")
     * @ORM\JoinColumn(nullable=true)
     */
    private $checklist;

    private $status = self::STATUS_OPEN;

    /**
     * @ORM\OneToMany(targetEntity=TaskUserRel::class, mappedBy="task")
     */
    private $taskUserRels;

    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'status' => $this->status,
//            'description' => $this->description,
            'goals' => array_map(function (Goal $goal) {
                return $goal->jsonSerialize();
            }, $this->goals instanceof PersistentCollection ? $this->goals->toArray() : $this->goals)
        ];
    }

    public function __construct()
    {
        $this->goals = new ArrayCollection();
        $this->taskUserRels = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param array $goals
     */
    public function setGoals(array $goals): void
    {
        $this->goals = $goals;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
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

    /**
     * @return Collection|Goal[]
     */
    public function getGoals(): Collection
    {
        return $this->goals;
    }

    public function addGoal(Goal $goal): self
    {
        if (!$this->goals->contains($goal)) {
            $this->goals[] = $goal;
            $goal->setTask($this);
        }

        return $this;
    }

    public function removeGoal(Goal $goal): self
    {
        if ($this->goals->removeElement($goal)) {
            // set the owning side to null (unless already changed)
            if ($goal->getTask() === $this) {
                $goal->setTask(null);
            }
        }

        return $this;
    }

    public function getChecklist(): ?Checklist
    {
        return $this->checklist;
    }

    public function setChecklist(?Checklist $checklist): self
    {
        $this->checklist = $checklist;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    /**
     * @return Collection|TaskUserRel[]
     */
    public function getTaskUserRels(): Collection
    {
        return $this->taskUserRels;
    }

    public function addTaskUserRel(TaskUserRel $taskUserRel): self
    {
        if (!$this->taskUserRels->contains($taskUserRel)) {
            $this->taskUserRels[] = $taskUserRel;
            $taskUserRel->setTask($this);
        }

        return $this;
    }

    public function removeTaskUserRel(TaskUserRel $taskUserRel): self
    {
        if ($this->taskUserRels->removeElement($taskUserRel)) {
            // set the owning side to null (unless already changed)
            if ($taskUserRel->getTask() === $this) {
                $taskUserRel->setTask(null);
            }
        }

        return $this;
    }
}
