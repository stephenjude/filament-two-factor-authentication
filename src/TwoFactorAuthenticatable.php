<?php

namespace Stephenjude\FilamentTwoFactorAuthentication;

use BaconQrCode\Renderer\Color\Rgb;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\Fill;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Support\Facades\Hash;
use Stephenjude\FilamentTwoFactorAuthentication\Events\RecoveryCodeReplaced;

trait TwoFactorAuthenticatable
{
    /**
     * Determine if two-factor authentication has been enabled.
     */
    public function hasEnabledTwoFactorAuthentication(): bool
    {
        return ! is_null($this->two_factor_secret) &&
            ! is_null($this->two_factor_confirmed_at);
    }

    public function isTwoFactorChallengePassed(): bool
    {
        $sessionKey = 'login_2fa_challenge_passed_' . $this->id;

        return Hash::check($this->two_factor_secret, session()->get($sessionKey));
    }

    public function setTwoFactorChallengePassed(): void
    {
        $sessionKey = 'login_2fa_challenge_passed_' . $this->id;
        $sessionValue = Hash::make($this->two_factor_secret);

        session()->put($sessionKey, $sessionValue);
        // session()->regenerate();
    }

    /**
     * Get the user's two-factor authentication recovery codes.
     */
    public function recoveryCodes(): array
    {
        return json_decode(decrypt($this->two_factor_recovery_codes), true);
    }

    /**
     * Replace the given recovery code with a new one in the user's stored codes.
     */
    public function replaceRecoveryCode(string $code): void
    {
        $this->forceFill([
            'two_factor_recovery_codes' => encrypt(
                str_replace(
                    $code,
                    RecoveryCode::generate(),
                    decrypt($this->two_factor_recovery_codes)
                )
            ),
        ])->save();

        RecoveryCodeReplaced::dispatch($this, $code);
    }

    /**
     * Get the QR code SVG of the user's two factor authentication QR code URL.
     */
    public function twoFactorQrCodeSvg(): string
    {
        $svg = (new Writer(
            new ImageRenderer(
                new RendererStyle(192, 0, null, null, Fill::uniformColor(new Rgb(255, 255, 255), new Rgb(45, 55, 72))),
                new SvgImageBackEnd
            )
        ))->writeString($this->twoFactorQrCodeUrl());

        return trim(substr($svg, strpos($svg, "\n") + 1));
    }

    /**
     * Get the two factor authentication QR code URL.
     */
    public function twoFactorQrCodeUrl(): string
    {
        return app(TwoFactorAuthenticationProvider::class)->qrCodeUrl(
            companyName: config('app.name'),
            companyEmail: $this->email,
            secret: decrypt($this->two_factor_secret)
        );
    }
}
