<?php

namespace App\Form;

use App\Entity\EquipeType;
use App\Entity\Joueurs;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\NotBlank;


class PrevisionEquipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('num_equipe')
            ->add('division')
            ->add('saison')
            ->add('joueur', EntityType::class, [
                'class' => Joueurs::class,
                'choice_label' => 'nom',
                'multiple' => true,
                'expanded' => false,
            ])
            ->add('photo')
            ->add('categories',EntityType::class, array(
            'class' => 'App\Entity\Categories',
            'label' => 'Categorie : ',
            'choice_label' => function ($user) {
            return $user->getLibelle();
            },
            ));
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => EquipeType::class,
        ]);
    }
}
