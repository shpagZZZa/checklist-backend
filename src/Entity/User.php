<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @method string getUserIdentifier()
 */
class User implements UserInterface, \JsonSerializable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=16)
     */
    private $salt;

    /**
     * @ORM\OneToMany(targetEntity=Task::class, mappedBy="author")
     */
    private $tasks;

    /**
     * @ORM\OneToMany(targetEntity=File::class, mappedBy="author")
     */
    private $files;

    /**
     * @ORM\OneToMany(targetEntity=Call::class, mappedBy="fromUser")
     */
    private $calls;

    /**
     * @ORM\OneToMany(targetEntity=Checklist::class, mappedBy="author")
     */
    private $checklists;

    /**
     * @ORM\OneToMany(targetEntity=TaskUserRel::class, mappedBy="user")
     */
    private $taskUserRels;

    public function __construct()
    {
        $this->tasks = new ArrayCollection();
        $this->files = new ArrayCollection();
        $this->calls = new ArrayCollection();
        $this->checklists = new ArrayCollection();
        $this->taskUserRels = new ArrayCollection();
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email
        ];
    }

//    public function getUserIdentifier(): string
//    {
//        return $this->email;
//    }
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getSalt(): ?string
    {
        return $this->salt;
    }

    public function setSalt(string $salt): self
    {
        $this->salt = $salt;

        return $this;
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
            $task->setAuthor($this);
        }

        return $this;
    }

    public function removeTask(Task $task): self
    {
        if ($this->tasks->removeElement($task)) {
            // set the owning side to null (unless already changed)
            if ($task->getAuthor() === $this) {
                $task->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|File[]
     */
    public function getFiles(): Collection
    {
        return $this->files;
    }

    public function addFile(File $file): self
    {
        if (!$this->files->contains($file)) {
            $this->files[] = $file;
            $file->setAuthor($this);
        }

        return $this;
    }

    public function removeFile(File $file): self
    {
        if ($this->files->removeElement($file)) {
            // set the owning side to null (unless already changed)
            if ($file->getAuthor() === $this) {
                $file->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Call[]
     */
    public function getCalls(): Collection
    {
        return $this->calls;
    }

    public function addCall(Call $call): self
    {
        if (!$this->calls->contains($call)) {
            $this->calls[] = $call;
            $call->setFromUser($this);
        }

        return $this;
    }

    public function removeCall(Call $call): self
    {
        if ($this->calls->removeElement($call)) {
            // set the owning side to null (unless already changed)
            if ($call->getFromUser() === $this) {
                $call->setFromUser(null);
            }
        }

        return $this;
    }

    public function getRoles()
    {
        return [];
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getUsername()
    {
        return $this->name;
    }

    public function __call($name, $arguments)
    {
        // TODO: Implement @method string getUserIdentifier()
    }

    /**
     * @return Collection|Checklist[]
     */
    public function getChecklists(): Collection
    {
        return $this->checklists;
    }

    public function addChecklist(Checklist $checklist): self
    {
        if (!$this->checklists->contains($checklist)) {
            $this->checklists[] = $checklist;
            $checklist->setAuthor($this);
        }

        return $this;
    }

    public function removeChecklist(Checklist $checklist): self
    {
        if ($this->checklists->removeElement($checklist)) {
            // set the owning side to null (unless already changed)
            if ($checklist->getAuthor() === $this) {
                $checklist->setAuthor(null);
            }
        }

        return $this;
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
            $taskUserRel->setUser($this);
        }

        return $this;
    }

    public function removeTaskUserRel(TaskUserRel $taskUserRel): self
    {
        if ($this->taskUserRels->removeElement($taskUserRel)) {
            // set the owning side to null (unless already changed)
            if ($taskUserRel->getUser() === $this) {
                $taskUserRel->setUser(null);
            }
        }

        return $this;
    }
}
