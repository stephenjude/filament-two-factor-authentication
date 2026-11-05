<?php

use Filament\Facades\Filament;
use Filament\Livewire\DatabaseNotifications;
use Filament\Notifications\Notification;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Testing\TestResponse;
use Livewire\Livewire;
use Stephenjude\FilamentTwoFactorAuthentication\Tests\Common\User;

function livewireSnapshot(string $html, string $componentName): ?string
{
    $normalize = fn (string $name) => strtolower(str_replace(['\\', '.', '-'], '', $name));

    preg_match_all('/wire:snapshot="([^"]+)"/', $html, $matches);

    return collect($matches[1])
        ->map(fn (string $snapshot) => html_entity_decode($snapshot))
        ->first(fn (string $snapshot) => $normalize(json_decode($snapshot, true)['memo']['name']) === $normalize($componentName));
}

function callLivewire(string $snapshot, string $method, array $updates = []): TestResponse
{
    return test()
        ->withHeaders(['X-Livewire' => '1'])
        ->postJson(Livewire::getUpdateUri(), [
            '_token' => csrf_token(),
            'components' => [[
                'snapshot' => $snapshot,
                'updates' => (object) $updates,
                'calls' => [['path' => '', 'method' => $method, 'params' => []]],
            ]],
        ]);
}

beforeEach(function () {
    Schema::create('notifications', function (Blueprint $table) {
        $table->uuid('id')->primary();
        $table->string('type');
        $table->morphs('notifiable');
        $table->text('data');
        $table->timestamp('read_at')->nullable();
        $table->timestamps();
    });

    Filament::getPanel('admin')
        ->databaseNotifications()
        ->lazyLoadedDatabaseNotifications(false);

    $this->user = User::createDefault();

    $this->user->forceFill([
        'two_factor_secret' => encrypt('secret'),
        'two_factor_recovery_codes' => encrypt(json_encode(['first-code', 'second-code'])),
        'two_factor_confirmed_at' => now(),
    ])->save();

    $this->actingAs($this->user);

    Notification::make()->title('Private notification')->sendToDatabase($this->user);
});

it('does not mount the panel top bar before the challenge is passed', function (string $url) {
    $html = $this->get($url)->assertOk()->getContent();

    expect(livewireSnapshot($html, DatabaseNotifications::class))->toBeNull();

    expect($html)->not->toContain('fi-simple-user-menu-ctn');
})->with([
    'challenge' => '/admin/two-factor-challenge',
    'recovery' => '/admin/two-factor-recovery',
]);

it('lets the user log out before the challenge is passed', function (string $url) {
    $this->get($url)->assertOk()->assertSee(Filament::getPanel('admin')->getLogoutUrl());
})->with([
    'challenge' => '/admin/two-factor-challenge',
    'recovery' => '/admin/two-factor-recovery',
]);

it('rejects livewire calls from a protected page once the challenge is no longer passed', function () {
    $this->user->setTwoFactorChallengePassed();

    $html = $this->get('/admin/two-factor-setup')->assertOk()->getContent();

    $snapshot = livewireSnapshot($html, DatabaseNotifications::class);

    expect($snapshot)->not->toBeNull();

    session()->forget("login:challenge:secret:{$this->user->id}");

    callLivewire($snapshot, 'markAllNotificationsAsRead')->assertForbidden();

    expect(DB::table('notifications')->value('read_at'))->toBeNull();
});

it('allows livewire calls from a protected page after the challenge is passed', function () {
    $this->user->setTwoFactorChallengePassed();

    $html = $this->get('/admin/two-factor-setup')->assertOk()->getContent();

    callLivewire(livewireSnapshot($html, DatabaseNotifications::class), 'markAllNotificationsAsRead')->assertOk();

    expect(DB::table('notifications')->value('read_at'))->not->toBeNull();
});

it('still accepts a recovery code submitted through a real livewire request', function () {
    $html = $this->get('/admin/two-factor-recovery')->assertOk()->getContent();

    $snapshot = livewireSnapshot($html, 'filament-two-factor-authentication.pages.recovery');

    callLivewire($snapshot, 'authenticate', ['data.recovery_code' => 'first-code'])->assertOk();

    expect($this->user->fresh()->isTwoFactorChallengePassed())->toBeTrue();
});
