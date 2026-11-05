<?php

use Illuminate\Support\Facades\Event;
use Stephenjude\FilamentTwoFactorAuthentication\Events\RecoveryCodeReplaced;
use Stephenjude\FilamentTwoFactorAuthentication\Events\ValidTwoFactorRecoveryCodeProvided;
use Stephenjude\FilamentTwoFactorAuthentication\Pages\Recovery;
use Stephenjude\FilamentTwoFactorAuthentication\Tests\Common\User;

use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->user = User::createDefault();

    $this->user->forceFill([
        'two_factor_secret' => encrypt('secret'),
        'two_factor_recovery_codes' => encrypt(json_encode(['first-code', 'second-code'])),
        'two_factor_confirmed_at' => now(),
    ])->save();

    $this->actingAs($this->user);
});

it('passes the challenge with a valid recovery code', function () {
    Event::fake([ValidTwoFactorRecoveryCodeProvided::class]);

    livewire(Recovery::class)
        ->set('data.recovery_code', 'first-code')
        ->call('authenticate')
        ->assertHasNoErrors();

    expect($this->user->isTwoFactorChallengePassed())->toBeTrue();

    Event::assertDispatched(ValidTwoFactorRecoveryCodeProvided::class);
});

it('rejects a recovery code that has already been used', function () {
    livewire(Recovery::class)
        ->set('data.recovery_code', 'first-code')
        ->call('authenticate')
        ->assertHasNoErrors();

    session()->forget("login:challenge:secret:{$this->user->id}");

    livewire(Recovery::class)
        ->set('data.recovery_code', 'first-code')
        ->call('authenticate')
        ->assertHasErrors('data.recovery_code');

    expect($this->user->isTwoFactorChallengePassed())->toBeFalse();
});

it('replaces only the used recovery code', function () {
    Event::fake([RecoveryCodeReplaced::class]);

    livewire(Recovery::class)
        ->set('data.recovery_code', 'first-code')
        ->call('authenticate');

    $storedCodes = $this->user->fresh()->recoveryCodes();

    expect($storedCodes)
        ->toHaveCount(2)
        ->toContain('second-code')
        ->not->toContain('first-code');

    Event::assertDispatched(
        RecoveryCodeReplaced::class,
        fn (RecoveryCodeReplaced $event) => $event->code === 'first-code',
    );
});

it('does not pass the challenge or change stored codes for an invalid recovery code', function () {
    Event::fake([RecoveryCodeReplaced::class]);

    livewire(Recovery::class)
        ->set('data.recovery_code', 'invalid-code')
        ->call('authenticate')
        ->assertHasErrors('data.recovery_code');

    expect($this->user->isTwoFactorChallengePassed())->toBeFalse();

    expect($this->user->fresh()->recoveryCodes())->toBe(['first-code', 'second-code']);

    Event::assertNotDispatched(RecoveryCodeReplaced::class);
});

it('does not accept a partial recovery code', function () {
    expect($this->user->consumeRecoveryCode('first'))->toBeFalse();

    expect($this->user->fresh()->recoveryCodes())->toBe(['first-code', 'second-code']);
});

it('allows only one of two requests holding the same stale user to consume a code', function () {
    $firstRequestUser = User::find($this->user->id);
    $secondRequestUser = User::find($this->user->id);

    expect($firstRequestUser->consumeRecoveryCode('first-code'))->toBeTrue();

    expect($secondRequestUser->consumeRecoveryCode('first-code'))->toBeFalse();
});

it('keeps the in memory user in sync after consuming a code', function () {
    $this->user->consumeRecoveryCode('first-code');

    expect($this->user->recoveryCodes())
        ->toBe($this->user->fresh()->recoveryCodes())
        ->not->toContain('first-code');

    expect($this->user->isDirty())->toBeFalse();
});

it('does not consume a code when two factor authentication has been disabled', function () {
    $staleUser = User::find($this->user->id);

    $this->user->forceFill(['two_factor_recovery_codes' => null])->save();

    expect($staleUser->consumeRecoveryCode('first-code'))->toBeFalse();
});
