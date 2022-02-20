<?php

namespace App\Entity;

use App\Repository\CartefideliteRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CartefideliteRepository::class)
 */
class Cartefidelite
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=20)
     */
    private $num;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $nbpts;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $periodevalidation;

    /**
     * @ORM\Column(type="date")
     */
    private $dateexpiration;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNum(): ?string
    {
        return $this->num;
    }

    public function setNum(string $num): self
    {
        $this->num = $num;

        return $this;
    }

    public function getNbpts(): ?int
    {
        return $this->nbpts;
    }

    public function setNbpts(?int $nbpts): self
    {
        $this->nbpts = $nbpts;

        return $this;
    }

    public function getPeriodevalidation(): ?string
    {
        return $this->periodevalidation;
    }

    public function setPeriodevalidation(string $periodevalidation): self
    {
        $this->periodevalidation = $periodevalidation;

        return $this;
    }

    public function getDateexpiration(): ?\DateTimeInterface
    {
        return $this->dateexpiration;
    }

    public function setDateexpiration(\DateTimeInterface $dateexpiration): self
    {
        $this->dateexpiration = $dateexpiration;

        return $this;
    }

 
}
