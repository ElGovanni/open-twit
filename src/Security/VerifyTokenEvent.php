<?php

namespace App\Security;

use App\Entity\User\User;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class VerifyTokenEvent
{
    private User $user;

    private string $code;

    public function __construct(User $user, string $code)
    {
        $this->user = $user;
        $this->code = $code;
    }

    #[Callback]
    public function validate(ExecutionContextInterface $context): void
    {
        if ($this->code !== $this->user->getConfirmationToken()) {
            $context
                ->buildViolation('Token is invalid.')
                ->atPath('code')
                ->addViolation();

            return;
        }

        if($this->user && $this->user->getConfirmationTokenExpireAt() <= new \DateTime()) {
            $context
                ->buildViolation('Token expired.')
                ->atPath('user.confirmationTokenExpireAt')
                ->addViolation();
            return;
        }
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
