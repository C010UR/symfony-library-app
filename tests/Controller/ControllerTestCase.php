<?php

namespace App\Tests\Controller;

use App\DataFixtures\UserFixturesTest;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Utils\FileUtils;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;

abstract class ControllerTestCase extends WebTestCase
{
    private KernelBrowser $client;

    private EntityManagerInterface $entityManager;

    private string $endpoint;

    private string $dirAssets;

    public function setEndpoint(string $endpoint): void
    {
        $this->endpoint = $endpoint;
    }

    public function getEndpoint(...$paths): string
    {
        return FileUtils::joinPaths([$this->endpoint, $paths]);
    }

    public function getEntityManager(): EntityManagerInterface
    {
        return $this->entityManager;
    }

    public function getClientInstance(): KernelBrowser
    {
        return $this->client;
    }

    public function loginClient(?User $user): void
    {
        $this->client->restart();

        if ($user instanceof \App\Entity\User) {
            $this->client->loginUser($user);
        }
    }

    public function getJsonResponseData(): array
    {
        return json_decode(
            $this->getClientInstance()
                ->getResponse()
                ->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR,
        );
    }

    public static function getRepository(string $repository): ServiceEntityRepository
    {
        return self::getContainer()->get($repository);
    }

    public function getUploadedFile(): UploadedFile
    {
        $file = tempnam(sys_get_temp_dir(), 'upl');
        file_put_contents($file, file_get_contents(sprintf('%s/image.png', $this->dirAssets)));

        return new UploadedFile($file, 'image.png', 'image/png', test: true);
    }

    public function assertUserAccess($user): void
    {
        if (!$user) {
            $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);

            return;
        }

        $this->getRepository(UserRepository::class)->find($user->getId());

        $this->assertResponseIsSuccessful();
    }

    public function assertAdminAccess($user): void
    {
        if (!$user) {
            $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);

            return;
        }

        $user = $this->getRepository(UserRepository::class)->find($user->getId());

        if (!in_array(User::ROLE_ADMIN, $user->getRoles())) {
            $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);

            return;
        }

        $this->assertResponseIsSuccessful();
    }

    public function assertResponseEqualsTo(mixed $expected)
    {
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json');
        $this->assertEquals($expected, $this->getJsonResponseData());
    }

    protected function setUp(): void
    {
        self::ensureKernelShutdown();
        $this->client = self::createClient();

        $this->entityManager = $this->getContainer()->get(EntityManagerInterface::class);

        $this->dirAssets = FileUtils::joinPaths([$this->getContainer()->getParameter('kernel.project_dir'), '/fixtures/uploads/test']);

        $this->client->loginUser(
            $this->getRepository(UserRepository::class)->findOneBy(['email' => UserFixturesTest::DATA[0]['email']]),
        );
    }

    public function getUsersSecurity(): iterable
    {
        $users = $this->getRepository(UserRepository::class)->findBy([], limit: 6);

        yield 'null' => [null];

        foreach ($users as $user) {
            yield $user->getEmail() => [$user];
        }
    }
}
