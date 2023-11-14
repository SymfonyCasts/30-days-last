<?php

namespace App\Form;

use App\Entity\Planet;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlanetType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('lightYearsFromEarth')
            ->add('imageFilename', ChoiceType::class, [
                'choices' => [
                    'Choose an image...' => '',
                    'Planet 1' => 'planet-1.png',
                    'Planet 2' => 'planet-2.png',
                    'Planet 3' => 'planet-3.png',
                    'Planet 4' => 'planet-4.png',
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Planet::class,
        ]);
    }
}
