<?php

namespace App\Form;

use App\Entity\Matchs;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class MatchsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $tabJoueurs = $options["tabJoueurs"];
        $builder
            ->add('victoire',ChoiceType::class,[
                'choices' => [
                    'oui' => true,
                    'non' => false],
                    'expanded' => true,
                    'label_attr'=>[
                    'class'=>'radio-inline'
                    ],
                    'multiple' => false,
                    'label' => 'Victoire ?'])
            ->add('double1',ChoiceType::class,[
                'choices' => [
                    'Double 1' => "1",
                    'Double 2' => "0"],
                'expanded' => true,
                'label_attr'=>[
                    'class'=>'radio-inline'
                ],
                'multiple' => false,
                'label' => 'Num. Double ?'])
            ->add('id_joueur1', ChoiceType::class, [
                'choices' => $tabJoueurs,
                'multiple' => false,
                'expanded' => false,
            ])
            ->add('id_joueur2', ChoiceType::class, [
                    'choices' => $tabJoueurs,
                    'multiple' => false,
                    'expanded' => false,
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Matchs::class,
            'tabJoueurs' => null,
        ]);
    }
}
