<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class MojoAuthService
{
    const MOJO_URL = 'https://api.mojoauth.com/users';

    protected $apiKey;

    protected $language;

    public function __construct()
    {
        $this->apiKey = config('app.mojo_auth_api_key');
        $this->language = app()->getLocale();
    }

    public function formatUrl($type)
    {
        $url = self::MOJO_URL . '/' . $type . '?language=' . urlencode($this->language);

        return $url;
    }

    public function formatMagicLinkUrl()
    {
        $rediectUrl = config('app.mojo_auth_magic_link_redirect_url');
        return self::MOJO_URL . '/magiclink?language=' . urlencode($this->language . '&redirect_url=' . $rediectUrl);
    }

    public function headers()
    {
        return [
            'X-API-Key' => $this->apiKey,
            'Content-Type' => 'application/json',
        ];
    }

    public function sendOtp($type, $value)
    {
        $url = $this->formatUrl($type === 'email' ? 'emailotp' : 'phone');

        $payload[$type] = $value;

        $response = Http::withHeaders($this->headers())
            ->post($url, $payload);

        return [
            'status' => $response->status(),
            'state_id' => $response->json()['state_id'] ?? null
        ];
    }

    public function verifyOtp($data)
    {
        $type = $data['otp_type'];
        $value = $data['otp_value'];
        $state_id = $data['state_id'];

        $url = $this->formatUrl($type === 'email' ? 'emailotp/verify' : 'phone/verify');

        $payload = [
            'OTP' => $value,
            'state_id' => $state_id,
        ];

        $response = Http::withHeaders($this->headers())
            ->post($url, $payload);

        return [
            'status' => $response->status(),
            'user' => $response->json()['user'] ?? null,
            'token' => $response->json()['token'] ?? null,
        ];
    }

    public function resendOtp($type, $stateId)
    {
        $url = $this->formatUrl($type === 'email' ? 'emailotp/resend' : 'phone/resend');

        $payload = [
            'state_id' => $stateId,
        ];

        $response = Http::withHeaders($this->headers())
            ->post($url, $payload);

        return [
            'status' => $response->status(),
            'state_id' => $response->json()['state_id'] ?? null
        ];
    }

    public function sendMagicLink($email)
    {
        $url = $this->formatMagicLinkUrl();

        $response = Http::withHeaders($this->headers())
            ->post($url, ['email' => $email]);

        return [
            'status' => $response->status(),
            'state_id' => $response->json()['state_id'] ?? null
        ];
    }

    public function validateMagicLink($stateId)
    {
        $url = self::MOJO_URL . '/status?state_id=' . $stateId;

        $response = Http::withHeaders($this->headers())
            ->get($url);

        return [
            'status' => $response->status(),
            'user' => $response->json()['user'] ?? null
        ];
    }
}
