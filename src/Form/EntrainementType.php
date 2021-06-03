<?php

namespace App\Form;

use App\Entity\Entrainement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class EntrainementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('libelle',TextType::class,array(
            'label' => 'Description :  ',
            'attr' => array('style' => 'width: 400px')
        ))
            ->add('jour',TextType::class,array(
                'label' => 'Journee :  ',
            ))
            ->add('heure_debut',TimeType::class)
            ->add('heure_fin',TimeType::class)
            ->add('categorie',EntityType::class, array(
                'class' => 'App\Entity\Categories',
                'label' => 'Categorie : ',
                'choice_label' => function ($user) {
                return $user->getLibelle();
                },
                ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Entrainement::class,
        ]);
    }
}
