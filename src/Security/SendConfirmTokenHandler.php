<?php

namespace App\Security;

use App\Entity\User\User;
use App\Security\ConfirmTokenGenerator\ConfirmTokenGeneratorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class SendConfirmTokenHandler
{
    public function __construct(
        private string $mailNoReply,
        private MailerInterface $mailer,
        private EntityManagerInterface $entityManager,
        private ConfirmTokenGeneratorInterface $confirmTokenGenerator,
    )
    {}

    public function __invoke(User $user)
    {
        $user->setConfirmationToken($this->confirmTokenGenerator->generate());
        $user->setConfirmationTokenExpireAt($this->confirmTokenGenerator->expireAt());

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->sendMail($user);
    }

    private function sendMail(User $user): void
    {
        $email = (new TemplatedEmail())
            ->from($this->mailNoReply)
            ->to($user->getEmail())
            ->subject('Verification code')
            ->htmlTemplate('emails/signup.html.twig')
            ->context([
                'username' => $user->getUsername(),
                'code' => $user->getConfirmationToken(),
                'expireAt' => $user->getConfirmationTokenExpireAt(),
            ])
        ;

        $this->mailer->send($email);
    }
}
