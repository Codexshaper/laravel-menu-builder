<?php

namespace CodexShaper\Menu\Commands;

use CodexShaper\Menu\MenuServiceProvider;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Process\Process;

class InstallMenuBuilder extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'menu:install';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the Laravel Menu Builder';
    /**
     * The database Seeder Path.
     *
     * @var string
     */
    protected $seedersPath = __DIR__.'/../../database/seeds/';

    /**
     * Get Option.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['force', null, InputOption::VALUE_NONE, 'Force the operation to run when in production', null],
        ];
    }

    /**
     * Get the composer command for the environment.
     *
     * @return string
     */
    protected function findComposer()
    {
        if (file_exists(getcwd().'/composer.phar')) {
            return '"'.PHP_BINARY.'" '.getcwd().'/composer.phar';
        }

        return 'composer';
    }

    /**
     * Execute the console command.
     *
     * @param \Illuminate\Filesystem\Filesystem $filesystem
     *
     * @return void
     */
    public function handle(Filesystem $filesystem)
    {
        $this->info('Publishing the MenuBuilder assets, database, and config files');
        // Publish only relevant resources on install
        $tags = ['menu.seeds', 'menu.config'];
        $this->call('vendor:publish', ['--provider' => MenuServiceProvider::class, '--tag' => $tags]);

        $this->info('Migrating the database tables into your application');
        $this->call('migrate', ['--force' => $this->option('force')]);

        $this->info('Dumping the autoloaded files and reloading all new files');
        $composer = $this->findComposer();
        $process = Process::fromShellCommandline($composer.' dump-autoload');
        $process->setTimeout(null); // Setting timeout to null to prevent installation from stopping at a certain point in time
        $process->setWorkingDirectory(base_path())->run();

        // Load Permission routes into application's 'routes/web.php'
        $this->info('Adding Permission routes to routes/web.php');
        $routes_contents = $filesystem->get(base_path('routes/web.php'));
        if (false === strpos($routes_contents, 'MenuBuilder::routes();')) {
            $filesystem->append(
                base_path('routes/web.php'),
                "\n\nMenuBuilder::routes();\n"
            );
        }

        // Seeding Dummy Data
        $class = 'MenuDatabaseSeeder';
        $file = $this->seedersPath.$class.'.php';

        if (file_exists($file) && !class_exists($class)) {
            require_once $file;
        }
        with(new $class())->run();

        $this->info('Seeding Completed');
    }
}
