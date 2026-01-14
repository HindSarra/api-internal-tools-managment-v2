<?php

namespace App\Entity;

use App\Repository\ToolRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;




#[ORM\Entity(repositoryClass: ToolRepository::class)]
class Tool
{
        public function __construct()
    {
        $now = new \DateTimeImmutable();
        $this->createdAt = $now;
        $this->updatedAt = $now;
    }
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 100)]
    private ?string $vendor = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $websiteUrl = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $monthlyCost = null;

    #[ORM\Column(length: 50)]
    private ?string $ownerDepartment = null;

    #[ORM\Column(length: 20)]
    private ?string $status = null;

    #[ORM\Column]
    private ?int $activeUsersCount = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'tools')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Category $category = null;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getVendor(): ?string
    {
        return $this->vendor;
    }

    public function setVendor(string $vendor): static
    {
        $this->vendor = $vendor;

        return $this;
    }

    public function getWebsiteUrl(): ?string
    {
        return $this->websiteUrl;
    }

    public function setWebsiteUrl(?string $websiteUrl): static
    {
        $this->websiteUrl = $websiteUrl;

        return $this;
    }

    public function getMonthlyCost(): ?string
    {
        return $this->monthlyCost;
    }

    public function setMonthlyCost(string $monthlyCost): static
    {
        $this->monthlyCost = $monthlyCost;

        return $this;
    }

    public function getOwnerDepartment(): ?string
    {
        return $this->ownerDepartment;
    }

    public function setOwnerDepartment(string $ownerDepartment): static
    {
        $this->ownerDepartment = $ownerDepartment;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getActiveUsersCount(): ?int
    {
        return $this->activeUsersCount;
    }

    public function setActiveUsersCount(int $activeUsersCount): static
    {
        $this->activeUsersCount = $activeUsersCount;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }
}

