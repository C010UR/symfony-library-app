<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Repository\ResetPasswordRequestRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\MailerAssertionsTrait;
use Symfony\Component\HttpFoundation\Response;

class ResetPasswordControllerPhpTest extends ControllerTestCase
{
    use MailerAssertionsTrait;

    private const TEST_PASSWORD = 'test.password';

    public function getUser(): iterable
    {
        $users = $this->getRepository(UserRepository::class)->findAll();

        /** @var User $user */
        foreach ($users as $user) {
            if ($user->isDeleted()) {
                continue;
            }

            yield $user->getEmail() => [$user, self::TEST_PASSWORD];
        }
    }

    /**
     * @dataProvider getUser
     */
    public function testPasswordReset(User $user, string $oldPassword): void
    {
        $newPassword = $this->appendRandomString($oldPassword);

        $this->getClient()->jsonRequest('POST', '/api/v1/password-reset', [
            'email' => $user->getEmail(),
            'link' => 'https://localhost:8000',
        ]);

        $this->assertResponseIsSuccessful();

        $this->assertEmailCount(1);
        $message = $this->getMailerMessage();

        $this->assertEmailHtmlBodyContains($message, 'RESET YOUR PASSWORD');

        $urls = [];
        preg_match_all('/href=\w+"https?:\/\/.*\/(.+)"/sU', $message->toString(), $urls);

        if (empty($urls[1])) {
            $this->fail('Could not find url');
        }

        $this->getClient()->jsonRequest(
            'POST',
            $this->joinPaths('/api/v1/password-reset/reset', preg_replace('/=\r?\n/m', '', $urls[1][0])),
            [
                'password' => $newPassword,
            ],
        );

        $this->assertResponseIsSuccessful();

        $this->getClient()->jsonRequest('POST', '/api/v1/login', [
            'username' => $user->getEmail(),
            'password' => $oldPassword,
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);

        $this->getClient()->jsonRequest('POST', '/api/v1/login', [
            'username' => $user->getEmail(),
            'password' => $newPassword,
        ]);

        $user->setIsActive(true);
        $this->assertResponseEqualsTo(User::format($user));
    }

    /**
     * @dataProvider getUser
     */
    public function testResetRequestFail(User $user): void
    {
        $this->getClient()->jsonRequest('POST', '/api/v1/password-reset', [
            'link' => 'https://localhost:8000',
        ]);
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        $this->getClient()->jsonRequest('POST', '/api/v1/password-reset', [
            'email' => $user->getEmail(),
        ]);
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        $this->getClient()->jsonRequest('POST', '/api/v1/password-reset', [
            'email' => sprintf('wrong.%s', $user->getEmail()),
            'link' => 'https://localhost:8000',
        ]);
        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);

        $this->getClient()->jsonRequest('POST', '/api/v1/password-reset', [
            'email' => $user->getEmail(),
            'link' => '',
        ]);
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        $this->getClient()->jsonRequest('POST', '/api/v1/password-reset', [
            'email' => '',
            'link' => 'https://localhost:8000',
        ]);
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        $this->getClient()->Request(
            'POST',
            '/api/v1/password-reset',
            content: json_encode([
                'email' => sprintf('wrong.%s', $user->getEmail()),
                'link' => 'https://localhost:8000',
            ]),
        );
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        $this->assertEmailCount(0);
    }

    /**
     * @dataProvider getUser
     */
    public function testResetFail(User $user, string $oldPassword): void
    {
        $newPassword = $this->appendRandomString($oldPassword);

        $this->getClient()->jsonRequest('POST', '/api/v1/password-reset/reset/', [
            'password' => $newPassword,
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);

        $this->getClient()->jsonRequest('POST', '/api/v1/password-reset', [
            'email' => $user->getEmail(),
            'link' => 'http://localhost:8000',
        ]);

        $this->assertResponseIsSuccessful();

        $selector = $this->getRepository(ResetPasswordRequestRepository::class)
            ->findOneBy(['user' => $user])
            ->getSelector();

        $this->getClient()->jsonRequest(
            'POST',
            $this->joinPaths('/api/v1/password-reset/reset', $this->appendRandomString($selector)),
            [
                'password' => $newPassword,
            ],
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        $this->getClient()->jsonRequest('POST', $this->joinPaths('/api/v1/password-reset/reset', $selector), [
            'password' => '',
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        $this->getClient()->Request(
            'POST',
            $this->joinPaths('/api/v1/password-reset/reset', $selector),
            content: json_encode([
                'password' => $newPassword,
            ]),
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }
}
