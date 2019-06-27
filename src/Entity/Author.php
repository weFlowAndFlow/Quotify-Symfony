<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AuthorRepository")
 * @UniqueEntity(fields={"forename", "name"})
 */
class Author
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $forename;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min=1)
     * @Assert\NotBlank
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Quote", mappedBy="author")
     */
    private $quotes;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\OriginalWork", mappedBy="authors", cascade={"persist"})
     */
    private $originalWorks;


    public function __construct()
    {
        $this->quotes = new ArrayCollection();
        $this->originalWorks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getForename(): ?string
    {
        return $this->forename;
    }

    public function setForename(?string $forename): self
    {
        $this->forename = $forename;

        return $this;
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

    /**
     * @return Collection|Quote[]
     */
    public function getQuotes(): Collection
    {
        return $this->quotes;
    }

    public function addQuote(Quote $quote): self
    {
        if (!$this->quotes->contains($quote)) {
            $this->quotes[] = $quote;
        }

        return $this;
    }

    public function removeQuote(Quote $quote): self
    {
        if ($this->quotes->contains($quote)) {
            $this->quotes->removeElement($quote);
        }

        return $this;
    }

    /**
     * @return Collection|OriginalWork[]
     */
    public function getOriginalWorks(): Collection
    {
        return $this->originalWorks;
    }

    public function addOriginalWork(OriginalWork $originalWork): self
    {
        if (!$this->originalWorks->contains($originalWork)) {
            $this->originalWorks[] = $originalWork;
            $originalWork->addAuthor($this);
        }

        return $this;
    }

    public function removeOriginalWork(OriginalWork $originalWork): self
    {
        if ($this->originalWorks->contains($originalWork)) {
            $this->originalWorks->removeElement($originalWork);
            $originalWork->removeAuthor($this);
        }

        return $this;
    }

    public function getDisplayName()
    {
        if (isset($this->name))
        {
            return $this->forename . " " . $this->name;
        } else 
        {
            return "Unknown";
        }
        
    }



}
