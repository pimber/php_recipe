<?php

namespace App\Entity;

use App\Repository\RecipeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RecipeRepository::class)]
class Recipe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 60)]
    private ?string $name = null;

    #[ORM\Column]
    private ?int $prep_time_hour = null;

    #[ORM\Column]
    private ?int $prep_time_min = null;

    #[ORM\Column]
    private ?int $cook_time_hour = null;

    #[ORM\Column]
    private ?int $cook_time_min = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $created_on = null;

    #[ORM\ManyToOne(inversedBy: 'recipes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?user $user_id = null;

    /**
     * @var Collection<int, IngredientAmount>
     */
    #[ORM\OneToMany(targetEntity: IngredientAmount::class, mappedBy: 'recipe_id', orphanRemoval: true)]
    private Collection $ingredients;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    public function __construct()
    {
        $this->ingredients = new ArrayCollection();
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

    public function getPrepTimeHour(): ?int
    {
        return $this->prep_time_hour;
    }

    public function setPrepTimeHour(int $prep_time_hour): static
    {
        $this->prep_time_hour = $prep_time_hour;

        return $this;
    }

    public function getPrepTimeMin(): ?int
    {
        return $this->prep_time_min;
    }

    public function setPrepTimeMin(int $prep_time_min): static
    {
        $this->prep_time_min = $prep_time_min;

        return $this;
    }

    public function getCookTimeHour(): ?int
    {
        return $this->cook_time_hour;
    }

    public function setCookTimeHour(int $cook_time_hour): static
    {
        $this->cook_time_hour = $cook_time_hour;

        return $this;
    }

    public function getCookTimeMin(): ?int
    {
        return $this->cook_time_min;
    }

    public function setCookTimeMin(int $cook_time_min): static
    {
        $this->cook_time_min = $cook_time_min;

        return $this;
    }

    public function getCreatedOn(): ?\DateTimeInterface
    {
        return $this->created_on;
    }

    public function setCreatedOn(\DateTimeInterface $created_on): static
    {
        $this->created_on = $created_on;

        return $this;
    }

    public function getUserId(): ?user
    {
        return $this->user_id;
    }

    public function setUserId(?user $user_id): static
    {
        $this->user_id = $user_id;

        return $this;
    }

    /**
     * @return Collection<int, IngredientAmount>
     */
    public function getIngredients(): Collection
    {
        return $this->ingredients;
    }

    public function addIngredient(IngredientAmount $ingredient): static
    {
        if (!$this->ingredients->contains($ingredient)) {
            $this->ingredients->add($ingredient);
            $ingredient->setRecipeId($this);
        }

        return $this;
    }

    public function removeIngredient(IngredientAmount $ingredient): static
    {
        if ($this->ingredients->removeElement($ingredient)) {
            // set the owning side to null (unless already changed)
            if ($ingredient->getRecipeId() === $this) {
                $ingredient->setRecipeId(null);
            }
        }

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
}
