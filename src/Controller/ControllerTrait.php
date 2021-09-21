<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;

trait ControllerTrait
{
    /**
     * @param FormInterface<string|FormInterface> $form
     * @param string|null   $formName
     *
     * @return array <int|string,mixed>
     */
    private function formGetErrors(FormInterface $form, string $formName = null): array
    {
        $errors = [];
        /**
         * @var FormError $error
         */
        foreach ($form->getErrors() as $error) {
            if (null === $formName) {
                $errors['global'][] = $error->getMessage();
            } else {
                $errors[] = $error->getMessage();
            }
        }

        if (null === $formName) {
            $formName = $form->getName();
        }

        foreach ($form->all() as $fieldName => $child) {
            $name = "{$formName}_{$fieldName}";
            if (false === empty($subErrors = $this->formGetErrors($child, $name))) {
                $errors[$name] = $subErrors;
            }
        }

        return $errors;
    }
}
