<?php

namespace App\Form;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class SearchConversationType extends AbstractType
{
    private Security       $security;
    private UserRepository $userRepository;

    public function __construct(Security $security, UserRepository $userRepository)
    {
        $this->security       = $security;
        $this->userRepository = $userRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('participants', null, [
                'constraints' => [
                    new NotBlank(),
                    new Callback([$this, 'validateParticipants'])
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }

    /**
     * @param null                      $object
     * @param ExecutionContextInterface $context
     */
    public function validateParticipants($object, ExecutionContextInterface $context): void
    {
        $participants = json_decode($context->getRoot()->get('participants')->getData());
        $count = count(array_filter($participants, function ($username) {
            return false === is_string($username);
        }));
        if ($count > 0) {
            $this->throwParticipantsViolation($context);
        }

        /**
         * @var User $user
         */
        $user = $this->security->getUser();
        if (false === in_array($user->getUserIdentifier(), $participants)) {
            $this->throwParticipantsViolation($context);
        }

        $users = $this->userRepository->findBy(['username' => $participants]);
        if (count($users) !== count($participants)) {
            $this->throwParticipantsViolation($context);
        }
    }

    private function throwParticipantsViolation(ExecutionContextInterface $context): void
    {
        $context
            ->buildViolation('the conversation participants are not valid')
            ->addViolation();
    }
}
