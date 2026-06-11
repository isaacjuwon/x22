<?php

namespace App\Providers;

<<<<<<< HEAD
use App\Console\Commands\AnalyticsCommand;
use App\Console\Commands\GenerateStaticCommand;
use App\Console\Commands\MakeBlogPostCommand;
use App\Console\Commands\MakeDocCommand;
use App\Console\Commands\MakePageCommand;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
=======
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
>>>>>>> 39d8a93ad41414dfcb6cdcc58894db1308285e6a
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
<<<<<<< HEAD
        $this->sharePergamentAssetVersions();

        if ($this->app->runningInConsole()) {
            $this->commands([
                AnalyticsCommand::class,
                GenerateStaticCommand::class,
                MakeBlogPostCommand::class,
                MakeDocCommand::class,
                MakePageCommand::class,
            ]);
        }
    }

    /**
     * Share CSS/JS asset version hashes with all CMS views.
     * Falls back to an empty string when assets are not yet published.
     */
    protected function sharePergamentAssetVersions(): void
    {
        $hash = static function (string $file): string {
            $published = public_path('vendor/pergament/' . $file);

            return is_file($published) ? substr(md5_file($published), 0, 8) : '';
        };

        View::share('pergamentCssVersion', $hash('pergament.css'));
        View::share('pergamentJsVersion', $hash('pergament.js'));
=======
>>>>>>> 39d8a93ad41414dfcb6cdcc58894db1308285e6a
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
