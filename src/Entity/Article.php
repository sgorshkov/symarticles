<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ArticleRepository;
use App\Util\ArrayTypeCheckUtil;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UlidGenerator;
use Symfony\Bridge\Doctrine\Types\UlidType;
use Symfony\Component\Uid\Ulid;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
#[ORM\Table(name: "articles")]
class Article
{
    #[ORM\Id]
    #[ORM\Column(type: UlidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UlidGenerator::class)]
    private ?Ulid $id;

    #[ORM\Column(length: 255)]
    private ?string $title;

    #[ORM\OneToMany(
        mappedBy: "article",
        targetEntity: ArticleTag::class,
        cascade: ["persist", "remove"],
        fetch: "EXTRA_LAZY",
        orphanRemoval: true
    )]
    // TODO: doctrine EAGER loading bug https://github.com/symfony/symfony/issues/39135#issuecomment-1294757245
    private Collection $articleTags;

    public function __construct($title)
    {
        $this->id = new Ulid();
        $this->articleTags = new ArrayCollection();
        $this->title = $title;
    }

    public function getId(): ?Ulid
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @param ArticleTag[] $articleTags
     */
    public function setArticleTags(array $articleTags): void
    {
        ArrayTypeCheckUtil::check(ArticleTag::class, $articleTags);
        $this->articleTags->clear();
        $this->articleTags = new ArrayCollection($articleTags);
    }

    public function getArticleTags(): array
    {
        return $this->articleTags->toArray();
    }
}
