<?php

namespace App\Contracts\Spotify;

interface SpotifyAuthClientInterface
{
    public function getAuthorizationUrl(array $scopes = []): string;
    public function handleCallback(string $code): array;
    public function refreshAccessToken(string $refreshToken): array;
    public function revokeToken(string $accessToken): bool;
}

