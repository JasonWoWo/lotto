<?php


namespace Happy\Lotto\ServiceProvider;

use Happy\Lotto\Commands\DrawLottery;
use Happy\Lotto\Commands\MigrationCommand;
use Illuminate\Support\ServiceProvider;

class LottoServiceProvider extends ServiceProvider
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
        // Publish config files
        $this->publishes([
            __DIR__.'/../config/config.php' => app()->basePath() . '/config/lotto.php',
        ]);
        $this->commands('command.lotto.migration');
        $this->commands('command.lotto.drawing');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {

        $this->registerCommands();

        $this->mergeConfig();
    }

    /**
     * Register the artisan commands.
     *
     * @return void
     */
    private function registerCommands()
    {
        $this->app->singleton('command.lotto.migration', function ($app) {
            return new MigrationCommand();
        });
        $this->app->singleton('command.lotto.drawing', function ($app) {
            return new DrawLottery();
        });
    }

    /**
     * Merges user's and entrust's configs.
     *
     * @return void
     */
    private function mergeConfig()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/config.php', 'lotto'
        );
    }

    /**
     * Get the services provided.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'command.lotto.migration',
            'command.lotto.drawing'
        ];
    }
}
