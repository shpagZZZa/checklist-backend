<?php

namespace App\Entity;

use App\Repository\CallFileRelRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CallFileRelRepository::class)
 */
class CallFileRel
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Call::class, inversedBy="callFileRels")
     * @ORM\JoinColumn(nullable=false)
     */

    private $call;

    /**
     * @ORM\ManyToOne(targetEntity=File::class, inversedBy="callFileRels")
     * @ORM\JoinColumn(nullable=false)
     */
    private $file;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="callFileRels")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFile(): ?File
    {
        return $this->file;
    }

    /**
     * @param mixed $call
     */
    public function setCall($call): void
    {
        $this->call = $call;
    }

    /**
     * @return mixed
     */
    public function getCall(): Call
    {
        return $this->call;
    }

    public function setFile(?File $file): self
    {
        $this->file = $file;

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
}
