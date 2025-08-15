<?php

use Stephenjude\FilamentTwoFactorAuthentication\Livewire\PasskeyAuthentication;
use Stephenjude\FilamentTwoFactorAuthentication\Livewire\TwoFactorAuthentication;
use Stephenjude\FilamentTwoFactorAuthentication\Pages\Challenge;
use Stephenjude\FilamentTwoFactorAuthentication\Pages\Recovery;
use Stephenjude\FilamentTwoFactorAuthentication\Pages\Setup;
use Stephenjude\FilamentTwoFactorAuthentication\TwoFactorAuthenticationPlugin;

use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->plugin = TwoFactorAuthenticationPlugin::get();
});

it('enables two factor authentication', function () {
    $this->plugin->enableTwoFactorAuthentication();

    expect($this->plugin->hasEnabledTwoFactorAuthentication())->toBeTrue();
});

it('enables passkey authentication', function () {
    $this->plugin->enablePasskeyAuthentication();

    expect($this->plugin->hasEnabledPasskeyAuthentication())->toBeTrue();
});

it('adds 2FA to user menu item', function () {
    $this->plugin->addTwoFactorMenuItem();

    expect($this->plugin->hasTwoFactorMenuItem())->toBeTrue();
});

it('sets custom challenge middleware on plugin', function () {
    $middleware = 'CustomMiddleware';
    $this->plugin->enableTwoFactorAuthentication(true, $middleware);

    expect($this->plugin->getTwoFactorChallengeMiddleware())->toBe($middleware);
});

it('forces setup and toggles password requirement on plugin', function () {
    $this->plugin->forceTwoFactorSetup(true, false);

    expect($this->plugin->hasForcedTwoFactorSetup())->toBeTrue();
    expect($this->plugin->twoFactorSetupRequiresPassword())->toBeFalse();
});

it('can render setup page', function () {
    livewire(Setup::class)->assertSuccessful();
});

it('can render challenge page', function () {
    livewire(Challenge::class)
        ->assertSuccessful();
});

it('can render recovery page', function () {
    livewire(Recovery::class)
        ->assertOk();
});

it('can render two factor component', function () {
    livewire(TwoFactorAuthentication::class)
        ->assertSuccessful();
});

it('can render passkey component', function () {
    livewire(PasskeyAuthentication::class)
        ->assertSuccessful()
        ->assertActionExists('delete')
        ->assertTableHeaderActionsExistInOrder(['addPasskey']);
});
