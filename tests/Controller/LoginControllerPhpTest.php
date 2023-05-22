<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Tests\Controller\ControllerTestCase;
use Symfony\Component\HttpFoundation\Response;

class LoginControllerPhpTest extends ControllerTestCase
{
    private const TEST_PASSWORD = 'test.password';

    public function setUp(): void
    {
        parent::setup();
        $this->loginClient(null);
    }

    public function getUser(): iterable
    {
        $users = $this->getRepository(UserRepository::class)->findAll();

        /** @var User $user */
        foreach ($users as $user) {
            if (!$user->isActive() || $user->isDeleted()) {
                continue;
            }

            yield $user->getEmail() => [$user, self::TEST_PASSWORD];
        }
    }

    /**
     * @dataProvider getUser
     */
    public function testLogin(User $user, string $password): void
    {
        $this->getClient()->jsonRequest('POST', '/api/v1/login', [
            'username' => $user->getEmail(),
            'password' => $password,
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertEquals(User::format($user), $this->getJsonResponseData());

        $this->getClient()->request('GET', '/api/v1/profile');

        $this->assertResponseIsSuccessful();
        $this->assertEquals(User::format($user), $this->getJsonResponseData());

        $this->getClient()->request('GET', '/api/v1/logout');

        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }

    public function assertLoginFail(string $email, string $password, string $expectedMessage): void
    {
        $this->getClient()->jsonRequest('POST', '/api/v1/login', [
            'username' => $email,
            'password' => $password,
        ]);

        $this->assertStringContainsString($expectedMessage, $this->getJsonResponseData()['exception']['message']);
        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @dataProvider getUser
     */
    public function testDisableUser(User $user, string $password): void
    {
        $this->assertLoginFail(
            $user->getEmail(),
            $this->appendRandomString($password),
            'Email and/or Password are incorrect.',
        );

        $this->assertLoginFail(
            $user->getEmail(),
            $this->appendRandomString($password),
            'Email and/or Password are incorrect.',
        );

        $this->assertLoginFail(
            $user->getEmail(),
            $this->appendRandomString($password),
            'Email and/or Password are incorrect.',
        );

        $this->assertLoginFail($user->getEmail(), $this->appendRandomString($password), 'Account was deactivated.');

        $this->assertLoginFail($user->getEmail(), $password, 'Account is not active');
    }

    /**
     * @dataProvider getUser
     */
    public function testWrongCredentials(User $user, string $password): void
    {
        $this->assertLoginFail(
            $user->getEmail(),
            $this->appendRandomString($password),
            'Email and/or Password are incorrect.',
        );

        $this->assertLoginFail(
            $this->appendRandomString($user->getEmail()),
            $password,
            'Email and/or Password are incorrect.',
        );

        $this->assertLoginFail(
            $this->appendRandomString($user->getEmail()),
            $this->appendRandomString($password),
            'Email and/or Password are incorrect.',
        );
    }

    public function testProfileForUnauthorized(): void
    {
        $this->getClient()->request('GET', '/api/v1/profile');

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    public function testLogoutForUnauthorized(): void
    {
        $this->getClient()->request('GET', '/api/v1/logout');

        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }
}
