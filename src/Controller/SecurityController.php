<?php

namespace App\Controller;

use App\Entity\User\User;
use App\Security\SendConfirmTokenHandler;
use App\Security\VerifyTokenEvent;
use App\Security\VerifyTokenHandler;
use App\ValueObject\Role;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SecurityController extends AbstractController
{
    public const ROUTE_LOGIN = '/login';

    public const ROUTE_TOKEN_REFRESH = '/refresh-token';

    public const ROUTE_SEND_VERIFY_CODE = '/send-code';

    public const ROUTE_VERIFY_CODE = '/verify-code/{code}';

    #[Route(path: self::ROUTE_SEND_VERIFY_CODE, methods: ['POST'])]
    public function sendCode(SendConfirmTokenHandler $confirmTokenHandler, RateLimiterFactory $confirmTokenLimiter): JsonResponse
    {
        $this->denyAccessUnlessGranted(Role::INACTIVE);

        /** @var User $user */
        $user = $this->getUser();
        $limiter = $confirmTokenLimiter->create($user->getId());
        if ($limiter->consume()->isAccepted() === false) {
            throw new TooManyRequestsHttpException();
        }

        $confirmTokenHandler($user);

        return $this->json([]);
    }

    #[Route(path: self::ROUTE_VERIFY_CODE)]
    public function verifyEmail(
        string $code,
        VerifyTokenHandler $handler,
        ValidatorInterface $validator
    ): JsonResponse
    {
        $this->denyAccessUnlessGranted(Role::INACTIVE);

        /** @var User $user */
        $user = $this->getUser();
        $command = new VerifyTokenEvent($user, $code);

        $violations = $validator->validate($command);
        if ($violations->count() !== 0) {
            return $this->json($violations, Response::HTTP_OK);
        }

        $handler($command);

        return new JsonResponse(null, Response::HTTP_OK);
    }
}
