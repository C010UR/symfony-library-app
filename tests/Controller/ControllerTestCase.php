<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;

abstract class ControllerTestCase extends WebTestCase
{
    /**
     * @var string
     */
    final public const ADMIN_EMAIL = 'test.email.admin@mtec.by';

    private KernelBrowser $client;

    private EntityManagerInterface $entityManager;

    private string $endpoint;

    private string $dirAssets;

    public function joinPaths(...$paths): string
    {
        if ([] === $paths) {
            throw new \InvalidArgumentException('Paths are empty.');
        }

        foreach ($paths as $key => $argument) {
            $paths[$key] = trim((string) $argument);

            if (empty($paths[$key])) {
                unset($paths[$key]);
            }
        }

        return preg_replace('#/+#', '/', implode('/', $paths));
    }

    public function randomString(int $length = 64, string $keyspace = 'abcdefghijklmnopqrstuvwxyz'): string
    {
        if ($length < 1) {
            throw new \RangeException('Length must be a positive integer.');
        }

        $pieces = [];
        $max = strlen($keyspace) - 1;

        for ($i = 0; $i < $length; ++$i) {
            $pieces[] = $keyspace[random_int(0, $max)];
        }

        return implode('', $pieces);
    }

    public function appendRandomString(string $value): string
    {
        return sprintf('%s.%s', $this->randomString(12), $value);
    }

    public function removeEntityImage(mixed $entity): void
    {
        $entity->setImagePath(null);
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    public function setEndpoint(string $endpoint): void
    {
        $this->endpoint = $endpoint;
    }

    public function getEndpoint(...$paths): string
    {
        return $this->joinPaths([$this->endpoint, $paths]);
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

        if (!in_array('ROLE_ADMIN', $user->getRoles())) {
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

        $this->dirAssets = sprintf('%s/assets/uploads/test', $this->getContainer()->getParameter('kernel.project_dir'));

        $this->client->loginUser(
            $this->getRepository(UserRepository::class)->findOneBy(['email' => self::ADMIN_EMAIL]),
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
