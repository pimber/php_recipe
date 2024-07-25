<?php
namespace App\Form;

use App\Entity\Recipe;
use App\Form\IngredientAmountFormType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\Image;


class CreateNewRecipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Opskriftens navn',
                'attr' => ['class' => 'form-control']
            ])
            ->add('prep_time_hour', IntegerType::class, [
                'label' => 'Forberedelsestid (timer)',
                'attr' => ['class' => 'form-control']
            ])
            ->add('prep_time_min', IntegerType::class, [
                'label' => 'Forberedelsestid (minutter)',
                'attr' => ['class' => 'form-control']
            ])
            ->add('cook_time_hour', IntegerType::class, [
                'label' => 'Tilberedningstid (timer)',
                'attr' => ['class' => 'form-control']
            ])
            ->add('cook_time_min', IntegerType::class, [
                'label' => 'Tilberedningstid (minutter)',
                'attr' => ['class' => 'form-control']
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Sådan gør du',
                'attr' => ['class' => 'form-control']
            ])
            ->add('ingredients', CollectionType::class, [
                'entry_type' => IngredientAmountFormType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'mapped' => false,
            ])
            ->add('imageFile', FileType::class, [
                'label' => 'Opskriftsbillede : Kun .jpg og .png filer under 2MB er tilladt',
                'required' => false,
                'mapped' => false,
                'attr' => ['class' => 'form-control'],
                'constraints' => [
                    new Image([
                        'mimeTypes' => ['image/jpeg', 'image/png'],
                        'mimeTypesMessage' => 'Billede skal være i formatet .jpg eller .png',
                        'maxSize' => '2M',
                        'maxSizeMessage' => 'Billedet må ikke være større end 2MB',
                    ])
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Recipe::class,
        ]);
    }
}