<?php

namespace App\Form;

use App\Entity\User;
use App\Service\AccountFormService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;

class AccountGeneralType extends AbstractType
{
	private AccountFormService $formService;

	public function __construct(AccountFormService $accountFormService)
	{
		$this->formService = $accountFormService;
	}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username')
            ->add('email')
            ->add('firstName')
            ->add('lastName')
            ->add('currentPassword', PasswordType::class, [
                'mapped' => false,
                'constraints' => new UserPassword(['message' => 'Your password is incorrect']),
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Update',
                'attr' => [
                    'class' => 'btn btn-success d-block mx-auto px-3'
                ]
            ])
        ;
	    $this->formService->checkPasswordField($builder);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
