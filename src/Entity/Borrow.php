<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BorrowRepository")
 */
class Borrow
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\Type("\DateTime")
     */
    private $date;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Assert\Type("\DateTime")
     */
    private $dateOfReturn;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Pupil", inversedBy="Borrow")
     * @ORM\JoinColumn(nullable=false)
     */
    private $pupil;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Book", inversedBy="borrow")
     * @ORM\JoinColumn(nullable=false)
     */
    private $book;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getDateOfReturn(): ?\DateTimeInterface
    {
        return $this->dateOfReturn;
    }

    public function setDateOfReturn(?\DateTimeInterface $dateOfReturn): self
    {
        $this->dateOfReturn = $dateOfReturn;

        return $this;
    }

    public function getPupil(): ?Pupil
    {
        return $this->pupil;
    }

    public function setPupil(?Pupil $pupil): self
    {
        $this->pupil = $pupil;

        return $this;
    }

    public function getBook(): ?Book
    {
        return $this->book;
    }

    public function setBook(?Book $book): self
    {
        $this->book = $book;

        return $this;
    }
}
