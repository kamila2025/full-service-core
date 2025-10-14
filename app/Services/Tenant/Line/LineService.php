<?php

namespace App\Services\Tenant;

use App\Models\Tenant;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LineService
{
  /**
   * 構建 Line Login URL
   */
  public function buildLineLoginUrl(Tenant $tenant, string $state): string
  {
    $params = http_build_query([
      'client_id'     => $tenant->channel_id,
      'redirect_uri'  => route('tenant.line.login.callback', ['tenant' => $tenant->id]),
      'response_type' => 'code',
      'scope'         => 'profile openid email',
      'state'         => $state,
      'nonce'         => Str::random(16)
    ]);

    return 'https://access.line.me/oauth2/v2.1/authorize?' . $params;
  }

  /**
   * 取得 Line 使用者資料
   */
  public function getLineUserData(Tenant $tenant, string $code): ?array
  {
    $tokenResponse = $this->getLineAccessToken($tenant, $code);

    if (!$tokenResponse) return null;

    $userResponse = $this->getLineUserProfile($tokenResponse['access_token']);

    if (!$userResponse) return null;

    return [
      'userId'        => $userResponse['userId'],
      'displayName'   => $userResponse['displayName'],
      'pictureUrl'    => $userResponse['pictureUrl'] ?? null,
      'statusMessage' => $userResponse['statusMessage'] ?? null,
      'access_token'  => $tokenResponse['access_token'],
      'id_token'      => $tokenResponse['id_token'] ?? null,
    ];
  }

  /**
   * 交換授權碼取得 Line access token
   */
  public function getLineAccessToken(Tenant $tenant, string $code): ?array
  {
    $data = [
      'grant_type'    => 'authorization_code',
      'code'          => $code,
      'redirect_uri'  => route('tenant.line.login.callback', ['tenant' => $tenant->id]),
      'client_id'     => $tenant->channel_id,
      'client_secret' => $tenant->channel_secret,
    ];

    $response = $this->makeHttpRequest('https://api.line.me/oauth2/v2.1/token', $data);

    return $response ? json_decode($response, true) : null;
  }


  /**
   * 取得 Line 使用者資料
   */
  public function getLineUserProfile(string $accessToken): ?array
  {
    $headers = [
      'Authorization: Bearer ' . $accessToken
    ];

    $response = $this->makeHttpRequest('https://api.line.me/v2/profile', [], $headers);

    return $response ? json_decode($response, true) : null;
  }

  /**
   * 發送 HTTP 請求
   */
  public function makeHttpRequest(string $url, array $data = [], array $headers = []): ?string
  {
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    if (!empty($data)) {
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    }

    if (!empty($headers)) {
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    if ($httpCode !== 200) {
      Log::error('Line API 請求失敗', [
        'url'       => $url,
        'http_code' => $httpCode,
        'response'  => $response
      ]);

      return null;
    }

    return $response;
  }
}
