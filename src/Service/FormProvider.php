<?php

declare(strict_types=1);

namespace App\Service;

use App\Form\AccountGeneralType;
use App\Form\AccountPasswordType;
use App\Form\AvatarCoverType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class FormProvider
{
    const ROUTE_FORM_MAPPING = [
        'user_account_general_front'  => [
            'class' => AccountGeneralType::class,
            'id'    => 'accountGeneralForm',
        ],
        'user_account_password_front' => [
            'class' => AccountPasswordType::class,
            'id'    => 'accountPasswordForm',
        ],
        'user_account_avatar_front'   => [
            'class' => AvatarCoverType::class,
            'id'    => 'accountAvatarForm',
        ]
    ];

    private FormFactoryInterface $formFactory;
    private Environment          $twig;

    public function __construct(FormFactoryInterface $formFactory, Environment $twig)
    {
        $this->formFactory = $formFactory;
        $this->twig        = $twig;
    }

    /**
     * @param string               $route
     * @param mixed                $data
     * @param array<string,string> $options
     *
     * @return JsonResponse
     */
    public function getResponse(string $route, $data = null, array $options = []): JsonResponse
    {
        $options = array_merge($options, ['attr' => ['id' => self::ROUTE_FORM_MAPPING[$route]['id']]]);
        $form = $this->formFactory->create(self::ROUTE_FORM_MAPPING[$route]['class'], $data, $options);

        try {
            return new JsonResponse($this->twig->render('form/form.html.twig', [
                'form' => $form->createView()
            ]));
        } catch (LoaderError | RuntimeError | SyntaxError$e) {
            return new JsonResponse(null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
