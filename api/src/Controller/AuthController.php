<?php

namespace App\Controller;

use App\Manager\UserManager;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Exception;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;

class AuthController
{
    /**
     * @var UserManager
     */
    private UserManager $userManager;

    public function  __construct(
        UserManager $userManager
    ) {
        $this->userManager = $userManager;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        try {
            $user = $this->userManager->register(
                $request->getContent()
            );
        } catch (UniqueConstraintViolationException $e) {
            return new JsonResponse([
                'success' => false,
                'error' => 'Duplicate username.',
            ]);
        } catch (Exception $e) {
            return new JsonResponse([
                'success' => false,
                'error' => $e->getMessage(),
            ]);
        }

        return new JsonResponse([
            'success' => true,
            'user_id' => $user->getId(),
        ]);
    }

    /**
     * @param UserInterface $user
     * @param JWTTokenManagerInterface $JWTManager
     * @return JsonResponse
     */
    public function getTokenUser(UserInterface $user, JWTTokenManagerInterface $JWTManager): JsonResponse
    {
        return new JsonResponse(['token' => $JWTManager->create($user)]);
    }

}