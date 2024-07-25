<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class IngredientAmountFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('ingredient', TextType::class, [
                'label' => 'Navn',
                'attr' => ['class' => 'form-control']
            ])
            ->add('amount', TextType::class, [
                'label' => 'Mængde',
                'attr' => ['class' => 'form-control']
            ])
            ->add('ingredientAmountType', TextType::class, [
                'label' => 'Enhed',
                'attr' => ['class' => 'form-control']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null, 
        ]);
    }
}