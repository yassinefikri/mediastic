<?php

namespace App\Form;

use App\Entity\Friendship;
use App\Mapping\FriendshipMapping;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PendingFriendFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('remove', SubmitType::class, [
                'label'      => FriendshipMapping::ICON_PENDING,
                'label_html' => true,
                'attr'       => [
                    'class' => 'btn btn-secondary'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Friendship::class,
        ]);
    }
}
