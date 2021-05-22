<?php

namespace App\Form;

use App\Entity\Articles;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;

class ArticlesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('auteur', TextType::class,[
                'label' => 'Taille image en %',
            ])
            ->add('text',CKEditorType::class,[
                'label' => 'Texte de l\'article',
                'attr' => array('cols' => '5', 'rows' => '10'),
            ])
            ->add('date', DateTimeType::class,[
                'widget' => 'single_text',
                'attr' => ['class' => 'js-datepicker'],
                'label' => 'Date',
                'format' => 'dd/MM/yyyy',
                'html5'  => false
            ])
            ->add('en_ligne')
            ->add('titre')
            ->add('fichier',FileType::class,[
                'label' => 'Image',
                'multiple' => false,
                'mapped' => false,
                'required' => false
            ])
            ->add('joueur', EntityType::class, array(
                'class' => 'App\Entity\Joueurs',
                'label' => 'Auteur',
                'choice_label' => function ($user) {
                return $user->getNom() . " " . $user->getPrenom();
                }
                ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Articles::class,
        ]);
    }
}
