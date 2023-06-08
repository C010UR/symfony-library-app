<?php

namespace App\Tests\Controller;

use App\Controller\PasswordResetController;
use App\Entity\User;
use App\Repository\ResetPasswordRequestRepository;
use App\Repository\UserRepository;
use App\Utils\FileUtils;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\MailerAssertionsTrait;
use Symfony\Component\HttpFoundation\Response;

#[CoversClass(PasswordResetController::class)]
class PasswordResetControllerPhpTest extends ControllerTestCase
{
    use MailerAssertionsTrait;

    /**
     * @var string
     */
    private const TEST_PASSWORD = 'test.password';

    public static function getUser(): iterable
    {
        $users = self::getRepository(UserRepository::class)->findAll();

        /** @var User $user */
        foreach ($users as $user) {
            if ($user->isDeleted()) {
                continue;
            }

            yield $user->getEmail() => [$user, self::TEST_PASSWORD];
        }
    }

    #[DataProvider('getUser')]
    public function testPasswordReset(User $user, string $oldPassword): void
    {
        $newPassword = $this->appendRandomString($oldPassword);

        $this->getClientInstance()->jsonRequest('POST', '/api/v1/reset-password', [
            'email' => $user->getEmail(),
            'link' => 'https://localhost',
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

        $this->getClientInstance()->jsonRequest(
            'POST',
            FileUtils::joinPaths(['/api/v1/reset-password/reset', preg_replace('/=\r?\n/m', '', (string) $urls[1][0])]),
            [
                'password' => $newPassword,
            ],
        );

        $this->assertResponseIsSuccessful();

        $this->getClientInstance()->jsonRequest('POST', '/api/v1/login', [
            'username' => $user->getEmail(),
            'password' => $oldPassword,
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);

        $this->getClientInstance()->jsonRequest('POST', '/api/v1/login', [
            'username' => $user->getEmail(),
            'password' => $newPassword,
        ]);

        $user->setIsActive(true);
        $this->assertResponseEqualsTo($user->format());
    }

    #[DataProvider('getUser')]
    public function testResetRequestFail(User $user): void
    {
        $this->getClientInstance()->jsonRequest('POST', '/api/v1/reset-password', [
            'link' => 'https://localhost:8000',
        ]);
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        $this->getClientInstance()->jsonRequest('POST', '/api/v1/reset-password', [
            'email' => $user->getEmail(),
        ]);
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        $this->getClientInstance()->jsonRequest('POST', '/api/v1/reset-password', [
            'email' => sprintf('wrong.%s', $user->getEmail()),
            'link' => 'https://localhost:8000',
        ]);
        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);

        $this->getClientInstance()->jsonRequest('POST', '/api/v1/reset-password', [
            'email' => $user->getEmail(),
            'link' => '',
        ]);
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        $this->getClientInstance()->jsonRequest('POST', '/api/v1/reset-password', [
            'email' => '',
            'link' => 'https://localhost:8000',
        ]);
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        $this->getClientInstance()->Request(
            'POST',
            '/api/v1/reset-password',
            content: json_encode([
                'email' => sprintf('wrong.%s', $user->getEmail()),
                'link' => 'https://localhost:8000',
            ], JSON_THROW_ON_ERROR),
        );
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        $this->assertEmailCount(0);
    }

    #[DataProvider('getUser')]
    public function testResetFail(User $user, string $oldPassword): void
    {
        $newPassword = $this->appendRandomString($oldPassword);

        $this->getClientInstance()->jsonRequest('POST', '/api/v1/reset-password/reset/', [
            'password' => $newPassword,
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);

        $this->getClientInstance()->jsonRequest('POST', '/api/v1/reset-password', [
            'email' => $user->getEmail(),
            'link' => 'http://localhost:8000',
        ]);

        $this->assertResponseIsSuccessful();

        $selector = $this->getRepository(ResetPasswordRequestRepository::class)
            ->findOneBy(['user' => $user])
            ->getSelector();

        $this->getClientInstance()->jsonRequest(
            'POST',
            FileUtils::joinPaths(['/api/v1/reset-password/reset', $this->appendRandomString($selector)]),
            [
                'password' => $newPassword,
            ],
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        $this->getClientInstance()->jsonRequest('POST', FileUtils::joinPaths(['/api/v1/reset-password/reset', $selector]), [
            'password' => '',
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        $this->getClientInstance()->Request(
            'POST',
            FileUtils::joinPaths(['/api/v1/reset-password/reset', $selector]),
            content: json_encode([
                'password' => $newPassword,
            ], JSON_THROW_ON_ERROR),
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }
}
