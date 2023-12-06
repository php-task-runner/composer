<?php

declare(strict_types=1);

namespace TaskRunner\Composer\TaskRunner\Commands;

use EcEuropa\Toolkit\TaskRunner\AbstractCommands;
use Robo\Collection\CollectionBuilder;
use Robo\Task\Composer\Tasks;

/**
 * Provides Composer commands.
 */
class ComposerCommands extends AbstractCommands
{
    use Tasks;

    /**
     * Options to be used with most of Composer commands.
     *
     * @var array
     */
    protected const DEFAULT_OPTIONS = [
      'prefer-source' => false,
      'prefer-dist' => false,
      'dev' => true,
      'optimize-autoloader' => false,
      'ignore-platform-reqs' => false,
      'no-plugins' => false,
      'no-scripts' => false,
    ];

    /**
     * Runs composer install.
     *
     * @param array $options
     *   The command line options.
     *
     * @return \Robo\Collection\CollectionBuilder
     *   The Robo collection builder.
     *
     * @command composer:install
     */
    public function install(array $options = self::DEFAULT_OPTIONS): CollectionBuilder
    {
        $task = $this->taskComposerInstall(
            $this->getConfig()->get('composer.bin')
        );
        $this->applyOptions($task, $options);

        return $this->collectionBuilder()->addTask($task);
    }

    /**
     * Applies Composer options.
     *
     * @param \Robo\Collection\CollectionBuilder $task
     *   The Composer Robo task.
     * @param array $options
     *   The command line options.
     */
    protected function applyOptions(
        CollectionBuilder $task,
        array $options
    ): void {
        /** @var \Robo\Task\Composer\Base $task */
        if ($options['prefer-source']) {
            $task->preferSource();
        }

        $task
          ->preferDist($options['prefer-dist'])
          ->optimizeAutoloader($options['optimize-autoloader'])
          ->disablePlugins($options['no-plugins'])
          ->noScripts($options['no-scripts']);

        if (!$options['dev']) {
            $task->noDev();
        }
        if ($options['ignore-platform-reqs']) {
            $task->ignorePlatformRequirements($options['ignore-platform-reqs']);
        }
    }
}
