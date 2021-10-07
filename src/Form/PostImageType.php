<?php

namespace App\Form;

use App\Entity\PostImage;
use App\Manager\ImagesManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class PostImageType extends AbstractType
{
    private ImagesManager $manager;

    public function __construct(ImagesManager $manager)
    {
        $this->manager = $manager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('image', FileType::class, [
                'label'       => false,
                'constraints' => [
                    new Image(['mimeTypes' => ['image/jpg', 'image/jpeg', 'image/png']]),
                    new Callback([$this, 'validateImage']),
                ],
                'attr'        => [
                    'class' => 'post-image-input'
                ],
            ]);
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            if (null !== $event->getData()) {
                $options             = $event->getForm()->get('image')->getConfig()->getOptions();
                $options['required'] = false;
                $event->getForm()->remove('image');
                $event->getForm()->add('image', FileType::class, $options);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => PostImage::class,
        ]);
    }

    public function validateImage(?UploadedFile $value, ExecutionContextInterface $context): void
    {
        /**
         * @var Form $imageFormInterface
         */
        $imageFormInterface = $context->getObject();
        /**
         * @var Form $postImageFormInterface
         */
        $postImageFormInterface = $imageFormInterface->getParent();
        $postImage              = $postImageFormInterface->getData();
        if (null === $postImage->getImageName()) {
            if (null === $value) {
                $this->throwViolation($context);
                return;
            }
            $imageName = $this->manager->uploadPostImage($value);
            if (null === $imageName) {
                $this->throwViolation($context);
                return;
            }

            $post = $context->getRoot()->getData();
            $postImage->setImageName($imageName);
            $postImage->setPost($post);
        }
    }

    private function throwViolation(ExecutionContextInterface $context): void
    {
        $context
            ->buildViolation('The image is not valid')
            ->addViolation();
    }
}
