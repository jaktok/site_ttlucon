<?php

namespace App\Form;

use App\Entity\Matchs;
use App\Entity\Joueurs;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class MatchsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('victoire',ChoiceType::class,[
                'choices' => [
                    'oui' => true,
                    'non' => false,
                ],
                'label' => 'Victoire de Luçon'])
            //->add('position')
            //->add('matchDouble')
            ->add('score')
            ->add('joueur', EntityType::class, [
                'class' => Joueurs::class,
                'choice_label' => 'nom',
                'multiple' => false,
                'expanded' => true,
            ])
            //->add('score2')
            /*->add('victoire2',ChoiceType::class,[
                'choices' => [
                    'oui' => true,
                    'non' => false,
                ],
                'label' => 'Victoire du double 2'])*/
            //->add('id_joueur1')
            ->add('id_joueur2', EntityType::class, [
                'class' => Joueurs::class,
                'choice_label' => 'nom',
                'multiple' => false,
                'expanded' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Matchs::class,
        ]);
    }
}
