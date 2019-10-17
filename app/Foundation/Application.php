<?php

namespace App\Foundation;

use App\Contracts\Debug\ExceptionHandler;
use App\Exceptions\HandleExceptions;
use App\Providers\LogServiceProvider;
use Dotenv\Dotenv;
use Exception;
use Illuminate\Contracts\Config\Repository as RepositoryContract;
use Illuminate\Config\Repository;
use League\Container\Container;
use League\Container\Definition\DefinitionAggregateInterface;
use League\Container\Inflector\InflectorAggregateInterface;
use League\Container\ServiceProvider\ServiceProviderAggregateInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\Finder\Finder;

class Application extends Container
{
    const VERSION = 'dev';

    /**
     * @var string
     */
    protected $basePath;

    /**
     * @var static
     */
    public static $instance;

    public function __construct(
        $basePath = null,
        DefinitionAggregateInterface $definitions = null,
        ServiceProviderAggregateInterface $providers = null,
        InflectorAggregateInterface $inflectors = null
    )
    {
        parent::__construct($definitions, $providers, $inflectors);

        if ($basePath) {
            $this->setBasePath($basePath);
        }

        $this->loadEnvironment();
        $this->setDefaultTimezone();
        $this->registerBaseBindings();
        $this->loadConfiguration($this->get('config'));
        $this->registerBaseProviders();
    }

    public function version()
    {
        return self::VERSION;
    }

    public function setBasePath($basePath)
    {
        $this->basePath = rtrim($basePath, '\/');

        return $this;
    }

    protected function registerBaseBindings()
    {
        static::setInstance($this);

        $this->add('app', $this, true);

        $this->add(Container::class, $this, true);

        $this->add(ExceptionHandler::class, new HandleExceptions(), true);

        $this->add('config', Repository::class, true);
    }

    protected function registerBaseProviders()
    {
        $this->addServiceProvider(LogServiceProvider::class);
    }

    protected function setDefaultTimezone()
    {
        date_default_timezone_set('Asia/Shanghai');
    }

    /**
     * @return static
     */
    public static function getInstance()
    {
        return self::$instance;
    }

    /**
     * @param ContainerInterface $container
     * @return ContainerInterface
     */
    public static function setInstance(ContainerInterface $container)
    {
        return static::$instance = $container;
    }

    public function runningInConsole()
    {
        return php_sapi_name() === 'cli' || php_sapi_name() === 'phpdbg';
    }

    protected function loadEnvironment()
    {
        Dotenv::create(
            $this->basePath
        )->safeLoad();
    }

    protected function configPath()
    {
        return $this->basePath . DIRECTORY_SEPARATOR . 'config';
    }

    protected function loadConfiguration(RepositoryContract $repository)
    {
        $files = $this->loadConfigurationFiles();

        if (!isset($files['app'])) {
            throw new Exception('Unable to load the "app" configuration file.');
        }

        foreach ($files as $key => $path) {
            $repository->set($key, require $path);
        }
    }

    protected function loadConfigurationFiles()
    {
        $files = [];

        $configPath = realpath($this->configPath());

        foreach (Finder::create()->files()->name('*.php')->in($configPath) as $file) {
            $directory = $this->getNestedDirectory($file, $configPath);

            $files[$directory . basename($file->getRealPath(), '.php')] = $file->getRealPath();
        }

        ksort($files, SORT_NATURAL);

        return $files;
    }

    /**
     * Get the configuration file nesting path.
     *
     * @param \SplFileInfo $file
     * @param string $configPath
     * @return string
     */
    protected function getNestedDirectory(\SplFileInfo $file, $configPath)
    {
        $directory = $file->getPath();

        if ($nested = trim(str_replace($configPath, '', $directory), DIRECTORY_SEPARATOR)) {
            $nested = str_replace(DIRECTORY_SEPARATOR, '.', $nested) . '.';
        }

        return $nested;
    }
}