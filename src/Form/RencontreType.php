<?php

namespace App\Form;

use App\Entity\Rencontres;
use App\Entity\EquipeType;
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

class RencontreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date_rencontre', DateTimeType::class,[
                'widget' => 'single_text',
                'attr' => ['class' => 'js-datepicker'],
                'label' => 'Date Rencontre',
                'format' => 'dd/MM/yyyy',
                'html5'  => false
              ])
            ->add('equipeA')
            ->add('equipeB')
            ->add('domicile', ChoiceType::class,[
                'choices' => [
                    'oui' => true,
                    'non' => false,
                ]
            ])
            ->add('no_journee')
            //->add('victoire')
            ->add('phase', ChoiceType::class,[
                'choices' => [
                    '1' => 1,
                    '2' => 2,
            ]])
            ->add('equipeType', EntityType::class, [
                'class' => EquipeType::class,
                'label' => 'Equipe',
                'choice_label' => 'nom',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Rencontres::class,
        ]);
    }
}
