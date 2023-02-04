<?php

namespace Netframe\Media;

use Illuminate\Support\ServiceProvider;
use Netframe\Media\Import\ImporterRegistry;
use Netframe\Media\Model\Media;
use Netframe\Media\Model\Observer\MediaObserver;
use Netframe\Media\Upload\FileSystemRegistry;
use Netframe\Media\Upload\FileType;
use Netframe\Media\Upload\FileTypeRegistry;
use Netframe\Media\Upload\LocalFileSystem;
use Netframe\Media\Upload\TimedSha1FileKeyGenerator;

/**
 * Service provider for the media package.
 */
class MediaServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        //$this->package('netframe/media');
        //$this->loadRoutesFrom(__DIR__.'/routes.php');

        //dd(__DIR__);

        require __DIR__ . '/../../routes.php';
        require __DIR__ . '/../../composers.php';
        require __DIR__ . '/../../events.php';

        $this->publishes([
            __DIR__.'/../../config/config.php' => config_path('media.php'),
        ]);

        Media::observe(new MediaObserver($this->app->make('media.manager')));

        $this->loadViewsFrom(__DIR__.'/../../views', 'media');
        $this->loadTranslationsFrom(__DIR__.'/../../lang', 'media');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //include __DIR__ . '/routes.php';


        $this->registerImporterRegistry();
        $this->registerFileTypeRegistry();
        $this->registerFileSystemRegistry();
        $this->registerFileKeyGenerator();
        $this->registerMediaManager();
        $this->registerImagine();
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array('media.manager');
    }

    private function registerImporterRegistry()
    {
        $this->app->singleton('media.importer_registry', function ($app) {
            $importers = config('media.importers');
            $registry = new ImporterRegistry();

            foreach ($importers as $importerClass) {
                $registry->addImporter(new $importerClass());
            }

            return $registry;
        });
    }

    private function registerFileTypeRegistry()
    {
        $this->app->singleton('media.file_type_registry', function ($app) {
            $configs = $app['config']->get('media.file_types');
            $registry = new FileTypeRegistry();

            foreach ($configs as $mediaType => $fileTypes) {
                foreach ($fileTypes as $fileType) {
                    $registry->addFileType(new FileType($mediaType, $fileType['extension'], $fileType['mime_type']));
                }
            }

            return $registry;
        });
    }

    private function registerFileSystemRegistry()
    {
        $this->app->singleton('media.file_system_registry', function ($app) {
            $configs = $app['config']->get('media.file_systems');
            $registry = new FileSystemRegistry();

            foreach ($configs as $mediaType => $config) {
                $adapterName = $config['adapter'];

                switch ($adapterName) {
                    case 'local':
                        $fileSystem = new LocalFileSystem($config['path']);
                        break;

                    default:
                        throw new \InvalidArgumentException(sprintf('Unknown adapter "%s"', $adapterName));
                }

                $registry->addFileSystem($fileSystem, $mediaType);
            }

            return $registry;
        });
    }

    private function registerFileKeyGenerator()
    {
        $this->app->singleton('media.file_key_generator', function ($app) {
            $fileKeyGenerator = $app['config']->get('media.file_key_generator');

            switch ($fileKeyGenerator) {
                case 'timed_sha1':
                    return new TimedSha1FileKeyGenerator();

                default:
                    throw new \InvalidArgumentException(sprintf('Unknown file key generator "%s"', $fileKeyGenerator));
            }
        });
    }

    private function registerMediaManager()
    {
        $this->app->singleton('media.manager', function ($app) {
            return new MediaManager(
                $app['media.importer_registry'],
                $app['media.file_type_registry'],
                $app['media.file_system_registry'],
                $app['media.file_key_generator']
            );
        });

        $this->app->bind('Netframe\Media\MediaManagerInterface', 'media.manager');
    }

    private function registerImagine()
    {
        $this->app->singleton('media.imagine', function ($app) {
            return new \Imagine\Imagick\Imagine();
        });

        $this->app->bind('Imagine\Image\ImagineInterface', 'media.imagine');
    }
}
