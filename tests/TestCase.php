<?php

namespace Stephenjude\FilamentTwoFactorAuthentication\Tests;

use BladeUI\Heroicons\BladeHeroiconsServiceProvider;
use BladeUI\Icons\BladeIconsServiceProvider;
use Filament\Actions\ActionsServiceProvider;
use Filament\FilamentServiceProvider;
use Filament\Forms\FormsServiceProvider;
use Filament\Infolists\InfolistsServiceProvider;
use Filament\Notifications\NotificationsServiceProvider;
use Filament\Support\SupportServiceProvider;
use Filament\Tables\TablesServiceProvider;
use Filament\Widgets\WidgetsServiceProvider;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use RyanChandler\BladeCaptureDirective\BladeCaptureDirectiveServiceProvider;
use Spatie\LaravelPasskeys\LaravelPasskeysServiceProvider;
use Stephenjude\FilamentTwoFactorAuthentication\Tests\Common\AdminPanelProvider;
use Stephenjude\FilamentTwoFactorAuthentication\Tests\Common\User;
use Stephenjude\FilamentTwoFactorAuthentication\TwoFactorAuthenticationServiceProvider;

use function Pest\Laravel\actingAs;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        actingAs(User::createDefault());
    }

    protected function getPackageProviders($app)
    {
        return [
            ActionsServiceProvider::class,
            BladeCaptureDirectiveServiceProvider::class,
            BladeHeroiconsServiceProvider::class,
            BladeIconsServiceProvider::class,
            FilamentServiceProvider::class,
            FormsServiceProvider::class,
            InfolistsServiceProvider::class,
            LivewireServiceProvider::class,
            NotificationsServiceProvider::class,
            SupportServiceProvider::class,
            TablesServiceProvider::class,
            WidgetsServiceProvider::class,
            TwoFactorAuthenticationServiceProvider::class,
            LaravelPasskeysServiceProvider::class,
            AdminPanelProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('app.key', 'base64:Hupx3yAySikrM2/edkZQNQHslgDWYfiBfCuSThJ5SK8=');

        config()->set('database.default', 'testing');

        config()->set('auth.providers.users.model', User::class);

        config()->set('passkeys.models.authenticatable', User::class);

        User::createTable();

        $migration = include __DIR__.'/../database/migrations/add_two_factor_authentication_columns.php.stub';

        $migration->up();

        $migration = include __DIR__.'/../vendor/spatie/laravel-passkeys/database/migrations/create_passkeys_table.php.stub';

        $migration->up();
    }
}
