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
    public const STATUS_OPEN = 'call_open';

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
    private $toUser;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity=Checklist::class, inversedBy="calls")
     * @ORM\JoinColumn(nullable=false)
     */
    private $checklist;

    /**
     * @ORM\OneToMany(targetEntity=CallFileRel::class, mappedBy="file")
     */
    private $callFileRels;

    public function __construct()
    {
        $this->callFileRels = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function jsonSerialize()
    {
            return [
                'id' => $this->id,
                'status' => $this->status,
                'to' => $this->toUser->jsonSerialize(),
                'checklist' => $this->checklist->jsonSerialize(),
            ];
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
     * @return Collection|CallFileRel[]
     */
    public function getCallFileRels(): Collection
    {
        return $this->callFileRels;
    }

    public function addCallFileRel(CallFileRel $callFileRel): self
    {
        if (!$this->callFileRels->contains($callFileRel)) {
            $this->callFileRels[] = $callFileRel;
            $callFileRel->setFile($this);
        }

        return $this;
    }

    public function removeCallFileRel(CallFileRel $callFileRel): self
    {
        if ($this->callFileRels->removeElement($callFileRel)) {
            // set the owning side to null (unless already changed)
            if ($callFileRel->getFile() === $this) {
                $callFileRel->setFile(null);
            }
        }

        return $this;
    }
}
