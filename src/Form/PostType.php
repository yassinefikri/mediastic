<?php

namespace App\Form;

use App\Entity\Post;
use App\Entity\User;
use App\Mapping\ConfidentialityMapping;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\Image;

class PostType extends AbstractType
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /**
         * @var User $user
         */
        $user = $this->security->getUser();
        $builder
            ->add('content', TextareaType::class, [
                'attr' => [
                    'placeholder' => "What's on your mind {$user->getFirstName()}"
                ],
            ])
            ->add('confidentiality', ChoiceType::class, [
                'choices' => ConfidentialityMapping::confs,
                'label_html' => true,
            ])
            ->add('postImages', CollectionType::class, [
                'mapped' => false,
                'entry_type' => FileType::class,
                'entry_options' => [
                    'constraints' => new Image(['mimeTypes' => ['image/jpg', 'image/jpeg', 'image/png']]),
                    'attr' => [
                        'class' => 'post-image-input'
                    ]
                ],
                'allow_add' => true,
                'allow_delete' => true,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Create',
                'attr' => [
                    'class' => 'btn btn-success d-block mx-auto px-3'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
