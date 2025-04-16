<?php

namespace App\Entity;

use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
#[UniqueEntity('key')]
class Project
{
    #[ORM\Id, ORM\Column, ORM\GeneratedValue]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private ?string $name = null;

    #[Assert\Length(min: 2, max: 10)]
    #[Assert\NotBlank]
    #[ORM\Column(name: '`key`', length: 10, unique: true)]
    private ?string $key = null;

    #[ORM\OneToMany(mappedBy: 'project', targetEntity: Issue::class, orphanRemoval: true)]
    private Collection $issues;

    #[ORM\ManyToOne(inversedBy: 'leadedProjects')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $lead = null;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'projects')]
    private Collection $people;

    public function __construct()
    {
        $this->issues = new ArrayCollection();
        $this->people = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getKey(): ?string
    {
        return $this->key;
    }

    public function setKey(string $key): static
    {
        $this->key = $key;

        return $this;
    }

    /**
     * @return Collection<int, Issue>
     */
    public function getIssues(): Collection
    {
        return $this->issues;
    }

    public function addIssue(Issue $issue): static
    {
        if (!$this->issues->contains($issue)) {
            $this->issues->add($issue);
            $issue->setProject($this);
        }

        return $this;
    }

    public function removeIssue(Issue $issue): static
    {
        if ($this->issues->removeElement($issue)) {
            // set the owning side to null (unless already changed)
            if ($issue->getProject() === $this) {
                $issue->setProject(null);
            }
        }

        return $this;
    }

    public function getLead(): ?User
    {
        return $this->lead;
    }

    public function setLead(?User $lead): static
    {
        $this->lead = $lead;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getPeople(): Collection
    {
        return $this->people;
    }

    public function addPerson(User $person): static
    {
        if (!$this->people->contains($person)) {
            $this->people->add($person);
        }

        return $this;
    }

    public function removePerson(User $person): static
    {
        $this->people->removeElement($person);

        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }
}
