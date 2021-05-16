<?php

namespace App\Form;

use App\Entity\Competition;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use App\Entity\Joueurs;

class CompetitionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('num_journee')
            ->add('date',DateTimeType::class,[
                'widget' => 'single_text',
                'attr' => ['class' => 'js-datepicker'],
                'label' => 'Date competition',
                'format' => 'dd/MM/yyyy',
                'html5'  => false
            ])
            ->add('type_competition',EntityType::class, array(
                'class' => 'App\Entity\TypeCompetition',
                'label' => 'Competition',
                'choice_label' => function ($user) {
                return $user->getLibelle();
                },
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Competition::class,
        ]);
    }
}
