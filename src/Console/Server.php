<?php

/*
 * This file is part of Flarum.
 *
 * (c) Toby Zerner <toby.zerner@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Flarum\Console;

use Flarum\Database\Console\GenerateMigrationCommand;
use Flarum\Database\Console\MigrateCommand;
use Flarum\Foundation\Application;
use Flarum\Foundation\Console\CacheClearCommand;
use Flarum\Foundation\Console\InfoCommand;
use Flarum\Foundation\Site;
use Flarum\Install\Console\InstallCommand;
use Flarum\Install\InstallServiceProvider;
use Symfony\Component\Console\Application as ConsoleApplication;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Server
{
    /**
     * @var Application
     */
    protected $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @param Site $site
     * @return Server
     */
    public static function fromSite(Site $site)
    {
        return new static($site->boot());
    }

    /**
     * @param InputInterface|null $input
     * @param OutputInterface|null $output
     */
    public function listen(InputInterface $input = null, OutputInterface $output = null)
    {
        $console = $this->getConsoleApplication();

        exit($console->run($input, $output));
    }

    /**
     * @return ConsoleApplication
     */
    protected function getConsoleApplication()
    {
        $console = new ConsoleApplication('Flarum', $this->app->version());

        $this->app->register(InstallServiceProvider::class);

        $commands = [
            InstallCommand::class,
            MigrateCommand::class,
            GenerateMigrationCommand::class,
        ];

        if ($this->app->isInstalled()) {
            $commands = array_merge($commands, [
                InfoCommand::class,
                CacheClearCommand::class
            ]);
        }

        foreach ($commands as $command) {
            $console->add($this->app->make(
                $command,
                ['config' => $this->app->isInstalled() ? $this->app->make('flarum.config') : []]
            ));
        }

        return $console;
    }
}
