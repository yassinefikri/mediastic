<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

class AvatarCoverType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('avatar', FileType::class, [
                'required' => false,
                'mapped' => false,
                'constraints' => new Image(['mimeTypes' => ['image/jpg', 'image/jpeg', 'image/png']]),
                'attr' => [
                    'accept' => 'image/png, image/jpeg'
                ]
            ])
            ->add('cover', FileType::class, [
                'required' => false,
                'mapped' => false,
                'constraints' => new Image(['mimeTypes' => ['image/jpg', 'image/jpeg', 'image/png']]),
                'attr' => [
                    'accept' => 'image/png, image/jpeg'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Upload',
                'attr' => [
                    'class' => 'btn btn-success d-block mx-auto px-3'
                ]
            ])
        ;
    }
}
