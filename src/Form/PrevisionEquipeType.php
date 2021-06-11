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
use Symfony\Component\Form\Extension\Core\Type\FileType;


class PrevisionEquipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $nomEquipereadOnly = $options["nomEquipereadOnly"];
        if ($nomEquipereadOnly == null){
            $nomEquipereadOnly = false;
        }
        $tabEquipesPresentes = $options["tabEquipesPresentes"];
        
        $nomEquipe = $options["nomEquipe"];
        
        $tabEquipes = array();
        for ($i = 1; $i < 10; $i++) {
            if (!in_array("LUCON ".$i, $tabEquipesPresentes)){
                $tabEquipes["LUCON ".$i] = "LUCON ".$i;
            }
        }
        
        $builder
        
            ->add('nom', ChoiceType::class, [
                'choices' => $tabEquipes,
                'empty_data' => $nomEquipe,
                'attr' => array(
                    'disabled' => $nomEquipereadOnly,
                ),
            ])
            ->add('division')
            ->add('saison')
            ->add('joueur', EntityType::class, [
                'class' => Joueurs::class,
                'choice_label' => function ($user) {
                    return $user->getNom()." ".$user->getPrenom();
                },
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('nom_photo',FileType::class,[
                'label' => 'Image',
                'multiple' => false,
                'mapped' => false,
                'required' => false 
            ])
            ->add('capitaine')
            ->add('salle')
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
            'nomEquipereadOnly' => null,
            'tabEquipesPresentes' => null,
            'nomEquipe' => null,
        ]);
    }
}
