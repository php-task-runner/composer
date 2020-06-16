<?php

declare(strict_types=1);

namespace TaskRunner\Composer\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

/**
 * @coversDefaultClass \TaskRunner\Composer\TaskRunner\Commands\ComposerCommands
 */
class ComposerCommandsTest extends TestCase
{
    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();
        mkdir(__DIR__ . '/../sandbox');
    }

    /**
     * @covers ::install
     */
    public function testInstall(): void
    {
        $sandboxDir = realpath(__DIR__ . '/../sandbox');
        // Composer commands needs the path to the executable.
        $config = [
          'composer' => ['bin' => __DIR__ . '/../../vendor/bin/composer'],
        ];
        file_put_contents(__DIR__ . '/../../runner.yml', Yaml::dump($config));

        $composer = [
          'require' => [
            'foo/bar' => '*',
          ],
          'repositories' => [
            [
              'type' => 'path',
              'url' => './../fixtures/dependency',
            ],
          ],
          'minimum-stability' => 'dev',
        ];
        file_put_contents(
            "{$sandboxDir}/composer.json",
            json_encode(
                $composer,
                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
            )
        );

        $this->assertFileNotExists("{$sandboxDir}/composer.lock");
        $this->assertDirectoryNotExists("{$sandboxDir}/vendor/foo/bar");
        exec(
            __DIR__ . "/../../vendor/bin/run composer:install --working-dir={$sandboxDir}"
        );
        $this->assertFileExists("{$sandboxDir}/composer.lock");
        $this->assertDirectoryExists("{$sandboxDir}/vendor/foo/bar");
    }

    /**
     * {@inheritdoc}
     */
    public function tearDown(): void
    {
        (new Filesystem())->remove(__DIR__ . '/../sandbox');
        parent::tearDown();
    }
}
