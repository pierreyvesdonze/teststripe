<?php

namespace App\Entity;

use App\Repository\AddressRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AddressRepository::class)]
class Address
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 64)]
    private ?string $firstName = null;

    #[ORM\Column(length: 120)]
    private ?string $lastName = null;

    #[ORM\Column(length: 255)]
    private ?string $addressFirstLine = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $addressSecondLine = null;

    #[ORM\Column(type: Types::INTEGER)]
    private ?int $addressPostal = null;

    #[ORM\Column(length: 255)]
    private ?string $addressTown = null;

    #[ORM\OneToOne(inversedBy: 'address', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;


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

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
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

    public function getAddressFirstLine(): ?string
    {
        return $this->addressFirstLine;
    }

    public function setAddressFirstLine(string $addressFirstLine): self
    {
        $this->addressFirstLine = $addressFirstLine;

        return $this;
    }

    public function getAddressSecondLine(): ?string
    {
        return $this->addressSecondLine;
    }

    public function setAddressSecondLine(?string $addressSecondLine): self
    {
        $this->addressSecondLine = $addressSecondLine;

        return $this;
    }

    public function getAddressPostal(): ?int
    {
        return $this->addressPostal;
    }

    public function setAddressPostal(int $addressPostal): self
    {
        $this->addressPostal = $addressPostal;

        return $this;
    }

    public function getAddressTown(): ?string
    {
        return $this->addressTown;
    }

    public function setAddressTown(string $addressTown): self
    {
        $this->addressTown = $addressTown;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
