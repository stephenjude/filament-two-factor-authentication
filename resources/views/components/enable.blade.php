<h2 class="text-xl font-medium mb-4">
    {{__('You have not enabled two factor authentication.')}}
</h2>

<p class="text-sm mb-4">
    {{__("When two factor authentication is enabled, you will be prompted for a secure, random token during authentication. You may retrieve this token from your phone's Google Authenticator application.")}}
</p>

{{$this->enableTwoFactorAuthentication}}