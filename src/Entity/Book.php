<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BookRepository")
 */
class Book
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     */
    private $title;
    
    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     */
    private $code;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    private $author;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $theme;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $sommary;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Assert\Type("\DateTime")
     */
    private $releaseDate;
    
     /**
     * @ORM\Column(type="boolean")
     */
    private $isBorrowed;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbBorrow;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Assert\Type("\DateTime")
     */
    private $dateLastBorrow;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Assert\Type("\DateTime")
     */
    private $dateLastReturn;

    /**
     * @ORM\Column(type="string", length=200, nullable=true)
     */
    private $originalLibrary;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Image", cascade={"persist", "remove"})
     */
    private $image;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Borrow", mappedBy="book", orphanRemoval=true)
     */
    private $borrow;

    public function __construct()
    {
        $this->borrow = new ArrayCollection();
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

    public function getAuthor(): ?string
    {
        return $this->author;
    }

    public function setAuthor(?string $author): self
    {
        $this->author = $author;

        return $this;
    }
    
    public function getCode(): ?int
    {
        return $this->code;
    }

    public function setCode(?int $code): self
    {
        $this->code = $code;

        return $this;
    }
    
    public function getTheme(): ?string
    {
        return $this->theme;
    }

    public function setTheme(?string $theme): self
    {
        $this->theme = $theme;

        return $this;
    }

    public function getSommary(): ?string
    {
        return $this->sommary;
    }

    public function setSommary(?string $sommary): self
    {
        $this->sommary = $sommary;

        return $this;
    }

    public function getReleaseDate(): ?\DateTimeInterface
    {
        return $this->releaseDate;
    }

    public function setReleaseDate(?\DateTimeInterface $releaseDate): self
    {
        $this->releaseDate = $releaseDate;

        return $this;
    }

    public function getNbBorrow(): ?int
    {
        return $this->nbBorrow;
    }

    public function setNbBorrow(int $nbBorrow): self
    {
        $this->nbBorrow = $nbBorrow;

        return $this;
    }
    
    public function getIsBorrowed(): ?bool
    {
        return $this->isBorrowed;
    }

    public function setIsBorrowed(?bool $isBorrowed): self
    {
        $this->isBorrowed = $isBorrowed;

        return $this;
    }
    
    public function getDateLastBorrow(): ?\DateTimeInterface
    {
        return $this->dateLastBorrow;
    }

    public function setDateLastBorrow(?\DateTimeInterface $dateLastBorrow): self
    {
        $this->dateLastBorrow = $dateLastBorrow;

        return $this;
    }

    public function getDateLastReturn(): ?\DateTimeInterface
    {
        return $this->dateLastReturn;
    }

    public function setDateLastReturn(?\DateTimeInterface $dateLastReturn): self
    {
        $this->dateLastReturn = $dateLastReturn;

        return $this;
    }

    public function getOriginalLibrary(): ?string
    {
        return $this->originalLibrary;
    }

    public function setOriginalLibrary(?string $originalLibrary): self
    {
        $this->originalLibrary = $originalLibrary;

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

    /**
     * @return Collection|Borrow[]
     */
    public function getBorrow(): Collection
    {
        return $this->borrow;
    }

    public function addBorrow(Borrow $borrow): self
    {
        if (!$this->borrow->contains($borrow)) {
            $this->borrow[] = $borrow;
            $borrow->setBook($this);
        }

        return $this;
    }

    public function removeBorrow(Borrow $borrow): self
    {
        if ($this->borrow->contains($borrow)) {
            $this->borrow->removeElement($borrow);
            // set the owning side to null (unless already changed)
            if ($borrow->getBook() === $this) {
                $borrow->setBook(null);
            }
        }

        return $this;
    }
}
