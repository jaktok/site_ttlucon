<?php
namespace App\Form;

use App\Entity\Fonction;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\Joueurs;

class FonctionsType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('libelle')
            ->add('position')
            ->add('joueurs', EntityType::class, array(
            'class' => 'App\Entity\Joueurs',
            'label' => 'Joueur',
            'choice_label' => function ($user) {
                return $user->getNom() . " " . $user->getPrenom();
            }
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Fonction::class
        ]);
    }
}
