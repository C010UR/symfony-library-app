<?php

namespace App\Service\ControllerService;

use App\Entity\User;
use App\Form\ChangePasswordFormType;
use App\Form\ResetPasswordRequestFormType;
use App\Utils\RequestUtils;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

class PasswordResetService
{
    public function __construct(
        private readonly ResetPasswordHelperInterface $resetPasswordHelper,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly EntityManagerInterface $entityManager,
        private readonly MailerInterface $mailer,
        private readonly FormFactoryInterface $formFactory,
        private readonly string $projectDir,
    ) {
    }

    public function requestResetPassword(Request $request): void
    {
        $form = $this->formFactory->create(ResetPasswordRequestFormType::class);
        RequestUtils::submitForm($request, $form, true);

        $user = $this->entityManager->getRepository(User::class)->findOneBy([
            'email' => $form->get('email')->getData(),
        ]);

        if (!$user || $user->isDeleted()) {
            return;
        }

        try {
            $resetToken = $this->resetPasswordHelper->generateResetToken($user);
        } catch (ResetPasswordExceptionInterface $resetPasswordException) {
            throw new BadRequestException($resetPasswordException->getReason());
        }

        $email = (new TemplatedEmail())
            ->from(new Address('library@gismauas.edu', 'GismaUAS Library Password Reset'))
            ->to($user->getEmail())
            ->subject('Password Reset')
            ->htmlTemplate('emails/password-reset.html.twig')
            ->embed(fopen(sprintf('%s/public/images/logo.png', $this->projectDir), 'r'), 'logo')
            ->context([
                'resetToken' => $resetToken,
                'link' => $form->get('link')->getData(),
            ]);

        $this->mailer->send($email);
    }

    public function resetPassword(Request $request, string $token)
    {
        if (null === $token) {
            throw new NotFoundHttpException('No reset password token found in the URL or in the session.');
        }

        try {
            $user = $this->resetPasswordHelper->validateTokenAndFetchUser($token);
        } catch (ResetPasswordExceptionInterface $resetPasswordException) {
            throw new BadRequestException($resetPasswordException->getReason());
        }

        $form = $this->formFactory->create(ChangePasswordFormType::class);
        RequestUtils::submitForm($request, $form, true);

        $this->resetPasswordHelper->removeResetRequest($token);

        $encodedPassword = $this->passwordHasher->hashPassword($user, $form->get('password')->getData());

        $user->setPassword($encodedPassword);
        $user->setIsActive(true);
        $user->setLoginAttempts(0);

        $this->entityManager->flush();
    }
}
