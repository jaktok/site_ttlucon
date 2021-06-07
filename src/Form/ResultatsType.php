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
use Symfony\Component\Form\Extension\Core\Type\FileType;

class ResultatsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('equipeA',null,[
                'attr' => array(
                    'readonly' => true,
                ),
                'label' => 'Equipe A'
            ])
            ->add('equipeB',null,[
                'attr' => array(
                    'readonly' => true,
                ),
                'label' => 'Equipe B'
            ])
            /*->add('victoire',ChoiceType::class,[
                'choices' => [
                    'oui' => true,
                    'non' => false,
                ],
                'label' => 'Victoire de Luçon'
            ])*/
            ->add('scoreA')
            ->add('scoreB')
            ->add('fichier',FileType::class,[
                'label' => 'Feuille de match',
                'multiple' => false,
                'mapped' => false,
                'required' => false
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
