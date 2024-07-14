<?php

namespace App\Entity;

use App\Repository\IngredientAmountRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: IngredientAmountRepository::class)]
class IngredientAmount
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 6, scale: 2)]
    private ?string $amount = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Ingredient $ingredient_id = null;

    #[ORM\ManyToOne(inversedBy: 'ingredients')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Recipe $recipe_id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?IngredientAmountType $ingredient_amount_type_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    public function getIngredientId(): ?Ingredient
    {
        return $this->ingredient_id;
    }

    public function setIngredientId(?Ingredient $ingredient_id): static
    {
        $this->ingredient_id = $ingredient_id;

        return $this;
    }

    public function getRecipeId(): ?Recipe
    {
        return $this->recipe_id;
    }

    public function setRecipeId(?Recipe $recipe_id): static
    {
        $this->recipe_id = $recipe_id;

        return $this;
    }

    public function getIngredientAmountTypeId(): ?IngredientAmountType
    {
        return $this->ingredient_amount_type_id;
    }

    public function setIngredientAmountTypeId(?IngredientAmountType $ingredient_amount_type_id): static
    {
        $this->ingredient_amount_type_id = $ingredient_amount_type_id;

        return $this;
    }
}
