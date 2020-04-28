<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PupilRepository")
 */
class Pupil
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=60)
     * @Assert\NotBlank
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=60)
     * @Assert\NotBlank
     */
    private $firstName;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Assert\Type("\DateTime")
     */
    private $dateOfBirth;

    /**
     * @ORM\Column(type="string", length=40, nullable=true)
     */
    private $grade;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Image", cascade={"persist", "remove"})
     */
    private $image;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Borrow", mappedBy="pupil", orphanRemoval=true)
     */
    private $Borrow;

    public function __construct()
    {
        $this->Borrow = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getDateOfBirth(): ?\DateTimeInterface
    {
        return $this->dateOfBirth;
    }

    public function setDateOfBirth(?\DateTimeInterface $dateOfBirth): self
    {
        $this->dateOfBirth = $dateOfBirth;

        return $this;
    }

    public function getGrade(): ?string
    {
        return $this->grade;
    }

    public function setGrade(?string $grade): self
    {
        $this->grade = $grade;

        return $this;
    }

    public function getImage(): ?Image
    {
        return $this->image;
    }

    public function setImage(?Image $image): self
    {
        $this->image = $image;

        return $this;
    }
    
    
    public function getCurrentBorrow()
    {
        return $this->Borrow;
    }
    
    /**
     * @return Collection|Borrow[]
     */
    public function getBorrow(): Collection
    {
        return $this->Borrow;
    }

    public function addBorrow(Borrow $borrow): self
    {
        if (!$this->Borrow->contains($borrow)) {
            $this->Borrow[] = $borrow;
            $borrow->setPupil($this);
        }

        return $this;
    }

    public function removeBorrow(Borrow $borrow): self
    {
        if ($this->Borrow->contains($borrow)) {
            $this->Borrow->removeElement($borrow);
            // set the owning side to null (unless already changed)
            if ($borrow->getPupil() === $this) {
                $borrow->setPupil(null);
            }
        }

        return $this;
    }
}
