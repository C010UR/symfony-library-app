<?php

namespace App\Tests\Controller;

use App\DataFixtures\UserFixturesTest;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Utils\Utils;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\HttpFoundation\Response;

class LoginControllerPhpTest extends ControllerTestCase
{
    protected function setUp(): void
    {
        parent::setup();
        $this->loginClient(null);
    }

    public static function getUser(): iterable
    {
        $users = self::getRepository(UserRepository::class)->findAll();

        /** @var User $user */
        foreach ($users as $user) {
            if (!$user->isActive()) {
                continue;
            }

            if ($user->isDeleted()) {
                continue;
            }

            yield $user->getEmail() => [$user, UserFixturesTest::PASSWORD];
        }
    }

    #[DataProvider('getUser')]
    public function testLogin(User $user, string $password): void
    {
        $this->getClientInstance()->jsonRequest('POST', '/api/v1/login', [
            'username' => $user->getEmail(),
            'password' => $password,
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertEquals($user->format(), $this->getJsonResponseData());

        $this->getClientInstance()->request('GET', '/api/v1/profile');

        $this->assertResponseIsSuccessful();
        $this->assertEquals($user->format(), $this->getJsonResponseData());

        $this->getClientInstance()->request('GET', '/api/v1/logout');

        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }

    public function assertLoginFail(string $email, string $password, string $expectedMessage): void
    {
        $this->getClientInstance()->jsonRequest('POST', '/api/v1/login', [
            'username' => $email,
            'password' => $password,
        ]);

        $this->assertStringContainsString($expectedMessage, $this->getJsonResponseData()['exception']['message']);
        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    #[DataProvider('getUser')]
    public function testDisableUser(User $user, string $password): void
    {
        $this->assertLoginFail(
            $user->getEmail(),
            Utils::appendRandomString($password),
            'Email and/or Password are incorrect.',
        );

        $this->assertLoginFail(
            $user->getEmail(),
            Utils::appendRandomString($password),
            'Email and/or Password are incorrect.',
        );

        $this->assertLoginFail(
            $user->getEmail(),
            Utils::appendRandomString($password),
            'Email and/or Password are incorrect.',
        );

        $this->assertLoginFail($user->getEmail(), Utils::appendRandomString($password), 'Account was deactivated.');

        $this->assertLoginFail($user->getEmail(), $password, 'Account is not active');
    }

    #[DataProvider('getUser')]
    public function testWrongCredentials(User $user, string $password): void
    {
        $this->assertLoginFail(
            $user->getEmail(),
            Utils::appendRandomString($password),
            'Email and/or Password are incorrect.',
        );

        $this->assertLoginFail(
            Utils::appendRandomString($user->getEmail()),
            $password,
            'Email and/or Password are incorrect.',
        );

        $this->assertLoginFail(
            Utils::appendRandomString($user->getEmail()),
            Utils::appendRandomString($password),
            'Email and/or Password are incorrect.',
        );
    }

    public function testProfileForUnauthorized(): void
    {
        $this->getClientInstance()->request('GET', '/api/v1/profile');

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    public function testLogoutForUnauthorized(): void
    {
        $this->getClientInstance()->request('GET', '/api/v1/logout');

        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }
}
