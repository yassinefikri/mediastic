<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Friendship;
use App\Mapping\FriendshipMapping;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class AnswerFriendFormType extends AbstractType
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($builder->getData()->getReceiver() === $this->security->getUser()) {
            $builder
                ->add('add', SubmitType::class, [
                    'label'      => FriendshipMapping::ICON_ACCEPT,
                    'label_html' => true,
                    'attr'       => [
                        'class' => 'btn btn-success'
                    ]
                ]);
        }
        $builder
            ->add('remove', SubmitType::class, [
                'label'      => FriendshipMapping::ICON_REFUSE,
                'label_html' => true,
                'attr'       => [
                    'class' => 'btn btn-secondary'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Friendship::class,
        ]);
    }
}
