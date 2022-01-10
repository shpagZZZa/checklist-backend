<?php

namespace App\Entity;

use App\Repository\FileRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FileRepository::class)
 */
class File implements \JsonSerializable
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
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $base64;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="files")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * @ORM\OneToMany(targetEntity=CallFileRel::class, mappedBy="file")
     */
    private $callFileRels;

    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'base64' => $this->base64,
            'author' => $this->author->jsonSerialze()
        ];
    }

    public function __construct()
    {
        $this->callFileRels = new ArrayCollection();
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

    public function getBase64(): ?string
    {
        return $this->base64;
    }

    public function setBase64(string $base64): self
    {
        $this->base64 = $base64;

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
