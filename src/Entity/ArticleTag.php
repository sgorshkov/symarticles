<?php

declare(strict_types=1);

namespace App\Entity;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UlidGenerator;
use Symfony\Bridge\Doctrine\Types\UlidType;
use Symfony\Component\Uid\Ulid;

#[ORM\Entity]
#[ORM\Table(name: "article_tags")]
#[ORM\UniqueConstraint(name: "unique_article_tag", columns: ["article_id", "tag_id"])]
class ArticleTag
{
    #[ORM\Id]
    #[ORM\Column(type: UlidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UlidGenerator::class)]
    private ?Ulid $id;

    #[ORM\ManyToOne(targetEntity: Article::class, inversedBy: "tags")]
    #[ORM\JoinColumn(nullable: false)]
    private ?Article $article;

    #[ORM\ManyToOne(targetEntity: Tag::class, fetch: "EAGER")]
    #[ORM\JoinColumn(nullable: false)]
    private ?Tag $tag;

    #[ORM\Column(type: "datetime_immutable")]
    private ?DateTimeInterface $createdAt;

    public function __construct(Article $article, Tag $tag)
    {
        $this->id = new Ulid();
        $this->createdAt = new DateTimeImmutable('now', new DateTimeZone('UTC'));
        $this->article = $article;
        $this->tag = $tag;
    }

    public function getId(): ?Ulid
    {
        return $this->id;
    }

    public function getArticle(): ?Article
    {
        return $this->article;
    }

    public function getTag(): ?Tag
    {
        return $this->tag;
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }
}
