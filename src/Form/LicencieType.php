<?php

namespace App\Form;

use App\Model\Licencie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use App\Entity\Categories;

class LicencieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('nom')
        ->add('prenom')
        ->add('mail')
        ->add('telephone')
        ->add('adresse')
        ->add('cp')
        ->add('ville')
        ->add('certificat', CheckboxType::class,[
            'required' => false
        ])
        ->add('cotisation', CheckboxType::class,[
            'required' => false
        ])
        ->add('divers')
        ->add('bureau', CheckboxType::class,[
            'required' => false
        ])
        ->add('num_licence')
        ->add('date_naissance', DateTimeType::class,[
            'widget' => 'single_text',
            'attr' => ['class' => 'js-datepicker'],
            'label' => 'Date de  naissance',
            'format' => 'dd/MM/yyyy',
            'html5'  => false
          ])
        ->add('nom_photo')
        ->add('date_certificat',DateTimeType::class,[
            'widget' => 'single_text',
            'attr' => ['class' => 'js-datepicker'],
            'label' => 'Date du  certificat',
            'format' => 'dd/MM/yyyy',
            'html5'  => false
        ])
        ->add('indiv', CheckboxType::class,[
            'required' => false
        ])
        ->add('contact_nom')
        ->add('contact_prenom')
        ->add('contact_tel')
        ->add('classement')
        ->add('role',ChoiceType::class, [
            'choices' => [
                'Administrateur' => 1,
                'Dirigeant'  => 2,
                'Capitaine'  => 3,
                'Redacteur'  => 4,
                'Joueur'  => 5,
            ],

            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}