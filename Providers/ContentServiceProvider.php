<?php

namespace Modules\Content\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Factory;
use Modules\Content\Composers\Admin\Entry\EntryTitle;
use Modules\Content\Composers\Admin\Entry\FormHeader;
use Modules\Content\Composers\Admin\Entry\IndexNavTabs;
use Modules\Content\Composers\Admin\Entry\Revisions;
use Modules\Content\Composers\Admin\Partials\LanguageTabs;

class ContentServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->registerFactories();
        $this->registerComposers();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        // Load stapler (it is not shipped with package auto-discovery)
        $this->app->register(\Codesleeve\LaravelStapler\Providers\L5ServiceProvider::class);
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            __DIR__ . '/../Config/config.php' => config_path('netcore/module-content.php'),
        ], 'config');

        $this->mergeConfigFrom(__DIR__ . '/../Config/config.php', 'content');
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/content');

        $sourcePath = __DIR__ . '/../Resources/views';

        $this->publishes([
            $sourcePath => $viewPath
        ]);

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/content';
        }, \Config::get('view.paths')), [$sourcePath]), 'content');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/content');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'content');
        } else {
            $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'content');
        }
    }

    /**
     * Register an additional directory of factories.
     * @source https://github.com/sebastiaanluca/laravel-resource-flow/blob/develop/src/Modules/ModuleServiceProvider.php#L66
     */
    public function registerFactories()
    {
        if (!app()->environment('production')) {
            app(Factory::class)->load(__DIR__ . '/Database/factories');
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }

    /**
     *
     */
    public function registerComposers()
    {
        $map = [
            'content::module_content.entries.form.header'    => FormHeader::class,
            'content::module_content.index.nav_tabs'         => IndexNavTabs::class,
            'content::module_content.partials.language_tabs' => LanguageTabs::class,
            'content::module_content.entries.form.revisions' => Revisions::class
        ];

        foreach ($map as $view => $composerClass) {
            view()->composer($view, $composerClass);
        }
    }
}
