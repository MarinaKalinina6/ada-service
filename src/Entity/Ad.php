<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\AdRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AdRepository::class)]
#[ORM\Table('ads')]
final class Ad
{
    #[ORM\Id] // primary key
    #[ORM\GeneratedValue] // autoincrement
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private readonly \DateTimeImmutable $createdAt;

    public function __construct(
        #[ORM\Column(type: Types::STRING, length: 220)]
        private readonly string $title,
        #[ORM\Column(type: Types::STRING, length: 1000)]
        private readonly string $description,
        #[ORM\Column(type: Types::JSON)]
        private readonly array $photos,
        #[ORM\Column(type: Types::INTEGER)]
        private readonly int $price,
    ) {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getPhotos(): array
    {
        return $this->photos;
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }
}
