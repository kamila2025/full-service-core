<?php

namespace App\Http\Controllers\Tenant\Line;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use LINE\Clients\MessagingApi\Api\MessagingApiApi;
use LINE\Clients\MessagingApi\Configuration;

class TenantWebhookController extends Controller
{
  public function handle(Request $request)
  {
    $sig   = $request->header('X-Line-Signature');
    $body  = $request->getContent();

    $tenant = Tenant::find(tenant()->id);

    $secret = $tenant->channel_secret;

    // 計算我們這邊的簽章
    $expected = base64_encode(hash_hmac('sha256', $body, $secret, true));

    // 轉成 array
    $data = json_decode($body, true);

    // --- Log 收到的請求 ---
    Log::info('LINE Webhook 收到請求', [
      'headers' => $request->headers->all(),
      'body'    => $body,
    ]);

    // --- 驗簽 ---
    if (!hash_equals($expected, (string)$sig)) {
      // 如果是 Verify（events 為空陣列），放行但記錄
      if (isset($data['events']) && empty($data['events'])) {
        Log::info('LINE Webhook Verify 請求（簽章不符但為空 events）');
        return response('OK', 200);
      }

      // 其他情況簽章錯誤 → 回 400
      Log::warning('LINE Webhook 簽章驗證失敗', [
        'expected' => $expected,
        'got'      => $sig,
      ]);
      return response('Invalid signature', 400);
    }

    // --- 如果 events 為空（Verify 用） ---
    if (isset($data['events']) && empty($data['events'])) {
      Log::info('LINE Webhook Verify 請求（簽章正確，空 events）');
      return response('OK', 200);
    }

    $events = $data['events'] ?? [];

    // 初始化 LINE Messaging API client
    $config = new Configuration();
    $config->setAccessToken($tenant->channel_access_token);
    $client = new MessagingApiApi(new Client(), $config);

    // --- 處理事件 ---
    foreach ($events as $e) {
      Log::info('LINE Webhook 處理事件', $e);

      if (($e['type'] ?? '') === 'message' && ($e['message']['type'] ?? '') === 'text') {
        $client->replyMessage([
          'replyToken' => $e['replyToken'],
          'messages'   => [[
            'type' => 'text',
            'text' => 'Hi！Webhook OK 🎉',
          ]],
        ]);
      }
    }

    return response('OK', 200);
  }
}
