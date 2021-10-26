<?php

declare(strict_types = 1);

namespace App\Service;

use Symfony\Component\Form\FormBuilderInterface;

class AccountFormService
{
	public function checkPasswordField(FormBuilderInterface $builder): void
	{
		if (null === $builder->getData()->getPassword() && $builder->has('currentPassword')) {
			$builder->remove('currentPassword');
		}
	}
}
