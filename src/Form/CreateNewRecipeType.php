<?php
namespace App\Form;

use App\Entity\Recipe;
use App\Form\IngredientAmountFormType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;


class CreateNewRecipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Recipe Name',
                'attr' => ['class' => 'form-control']
            ])
            ->add('prep_time_hour', IntegerType::class, [
                'label' => 'Prep Time (Hours)',
                'attr' => ['class' => 'form-control']
            ])
            ->add('prep_time_min', IntegerType::class, [
                'label' => 'Prep Time (Minutes)',
                'attr' => ['class' => 'form-control']
            ])
            ->add('cook_time_hour', IntegerType::class, [
                'label' => 'Cooking Time (Hours)',
                'attr' => ['class' => 'form-control']
            ])
            ->add('cook_time_min', IntegerType::class, [
                'label' => 'Cooking Time (Minutes)',
                'attr' => ['class' => 'form-control']
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Instructions',
                'attr' => ['class' => 'form-control']
            ])
            ->add('ingredients', CollectionType::class, [
                'entry_type' => IngredientAmountFormType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'mapped' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
        ]);
    }
}