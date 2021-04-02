<?php

namespace Rvsitebuilder\Larecipe;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class LarecipeServiceProvider extends ServiceProvider
{
    /**
     * Class event subscribers.
     *
     * @var array
     */
    protected $subscribe = [
        // \Rvsitebuilder\Larecipe\Listeners\LarecipeListener::class,
    ];

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->registerConfig();
    }

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        $this->bootRoute();
        $this->bootViews();
        $this->bootViewComposer();
        $this->bootTranslations();
        $this->defineMigrate();
        $this->defineVendorPublish();
        $this->loadHelpers();
        $this->defineConfigInterface();
    }

    public function defineMigrate(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }

    public function bootRoute(): void
    {
        include_route_files(__DIR__ . '/../routes');
    }

    public function defineVendorPublish(): void
    {
        $dirVendor = __DIR__ . '/../vendor/binarytorch/larecipe/publishable/assets';

        if (is_dir($dirVendor)) {
            $this->publishes([
                __DIR__ . '/../vendor/binarytorch/larecipe/publishable/assets' => public_path('vendor/binarytorch/larecipe/assets'),
            ], 'public');
        } else {
            $this->publishes([
                __DIR__ . '/../../../../vendor/binarytorch/larecipe/publishable/assets' => public_path('vendor/binarytorch/larecipe/assets'),
            ], 'public');
        }

        $this->publishes([
            __DIR__ . '/../public' => public_path('vendor/rvsitebuilder/larecipe'),
        ], 'public');
    }

    /**
     * boot views.
     */
    public function bootViews(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'rvsitebuilder/larecipe');
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'larecipe');
    }

    public function bootViewComposer(): void
    {
        View::composer(
            ['rvsitebuilder/larecipe::user.layouts.default'],
            \Rvsitebuilder\Core\Http\Composers\User\ViewComposer::class
        );
    }

    /**
     * boot translations.
     */
    public function bootTranslations(): void
    {
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'rvsitebuilder/larecipe');
    }

    /**
     * Register config.
     */
    protected function registerConfig(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'rvsitebuilder.larecipe');
        $collection = collect(config('view.paths'));
        $collection->prepend(__DIR__ . '/../resources/views');
        $this->app['config']->set('view.paths', $collection->toArray());

        Config::get('override')->push([
            'larecipe' => 'rvsitebuilder.larecipe',
        ]);
    }

    /**
     * Load helpers.
     */
    protected function loadHelpers(): void
    {
        $path = __DIR__ . '/../vendor/binarytorch/larecipe/src/Helpers';

        if (is_dir($path)) {
            foreach (glob(__DIR__ . '/../vendor/binarytorch/larecipe/src/Helpers/*.php') as $filename) {
                require_once $filename;
            }
        } else {
            foreach (glob(__DIR__ . '/../../../../vendor/binarytorch/larecipe/src/Helpers/*.php') as $filename) {
                require_once $filename;
            }
        }
    }

    protected function defineConfigInterface(): void
    {
        app('rvsitebuilderService')->siteconfiginterface('config', 'rvsitebuilder/larecipe::admin.config');
        app('rvsitebuilderService')->configRequestValidation('ConfigFormRequest');
    }
}
