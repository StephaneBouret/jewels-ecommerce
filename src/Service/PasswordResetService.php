<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

final class PasswordResetService
{
    public function __construct(
        protected TokenGeneratorInterface $tokenGenerator,
        protected EntityManagerInterface $em,
        protected UrlGeneratorInterface $urlGenerator,
        protected SendMailService $email
    ) {}

    public function processPasswordReset(User $user): void
    {
        // Génération du token
        $token = $this->tokenGenerator->generateToken();
        $now = new \DateTimeImmutable();
        $user->setResetToken($token)
            ->setCreatedTokenAt($now);
        $this->em->flush();

        // Génération de l'URL
        $url = $this->urlGenerator->generate('app_reset_pw', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);

        // On crée les données du mail
        $context = [
            'url' => $url,
            'user' => $user
        ];

        // Envoi du mail
        $this->email->sendMail(
            null,
            'Infos de l\'application Jewels Paradise',
            $user->getEmail(),
            'Réinitialisation de mot de passe',
            'password_reset',
            $context
        );
    }

    public function getUserByResetToken(string $token, UserRepository $userRepository): ?User
    {
        return $userRepository->findOneBy(['resetToken' => $token]);
    }

    public function isTokenExpired(User $user, int $expirationInHours = 3): bool
    {
        $now = new \DateTimeImmutable();
        return $now > $user->getCreatedTokenAt()->modify("+{$expirationInHours} hour");
    }

    public function updatePassword(User $user, string $plainPassword, UserPasswordHasherInterface $hasher): void
    {
        $hasedPassword = $hasher->hashPassword($user, $plainPassword);
        $user->setPassword($hasedPassword);
        $user->invalidateTrustedDevices();
        $user->setResetToken(null)
            ->setCreatedTokenAt(null);

        $this->em->flush();
    }
}
