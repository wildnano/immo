<?php

namespace App\Form;

use App\Entity\Property;
use App\Entity\Tag;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PropertyType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('description')
            ->add('surface')
            ->add('rooms')
            ->add('bedrooms')
            ->add('floor')
            ->add('price')
            ->add('heat',
                ChoiceType::class, [
                'choices' => $this->getChoices()
            ])
            ->add('tags', EntityType::class, [
                'class' => Tag::class,
                'required' => fzalse,
                'choice_label' => 'name',
                'multiple' => true
            ])

            ->add('imageFile', FileType::class, [
                      'required' => false
            ])
            ->add('city')
            ->add('address')
            ->add('postalCode')
            ->add('sold',
                CheckboxType::class,[
                'required' => false
            ])
//            ->add('createdAt')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Property::class,
            'translation_domain' => 'forms'
        ]);
    }

    private function getChoices()
    {
         $choices = Property::HEAT;
         $output = [];
         foreach($choices as $k => $v)
         {
             $output[$v] = $k;
         }
         return $output;
    }
}
