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
        $this->handleTextMessage($client, $e, $tenant);
      }
    }

    return response('OK', 200);
  }

  /**
   * 處理文字訊息
   */
  private function handleTextMessage($client, array $event, Tenant $tenant)
  {
    $messageText = $event['message']['text'] ?? '';
    $replyToken = $event['replyToken'] ?? '';

    // 檢查是否為綁定指令
    if (str_contains($messageText, '綁定') || str_contains($messageText, 'bind')) {
      $this->sendBindingInstructions($client, $replyToken, $tenant);
      return;
    }

    // 預設回覆
    $client->replyMessage([
      'replyToken' => $replyToken,
      'messages' => [[
        'type' => 'text',
        'text' => "您好！我是 {$tenant->name} 的客服機器人。\n\n如需綁定會員，請點擊下方按鈕：",
        'quickReply' => [
          'items' => [[
            'type' => 'action',
            'action' => [
              'type' => 'uri',
              'uri' => $this->getBindingUrl($tenant),
              'label' => '綁定會員'
            ]
          ]]
        ]
      ]],
    ]);
  }

  /**
   * 發送綁定說明
   */
  private function sendBindingInstructions($client, string $replyToken, Tenant $tenant)
  {
    $client->replyMessage([
      'replyToken' => $replyToken,
      'messages' => [[
        'type' => 'text',
        'text' => "歡迎使用 {$tenant->name} 的會員綁定服務！\n\n請點擊下方按鈕開始綁定：",
        'quickReply' => [
          'items' => [[
            'type' => 'action',
            'action' => [
              'type' => 'uri',
              'uri' => $this->getBindingUrl($tenant),
              'label' => '開始綁定'
            ]
          ]]
        ]
      ]],
    ]);
  }

  /**
   * 取得綁定頁面 URL
   */
  private function getBindingUrl(Tenant $tenant): string
  {
    $domain = $tenant->domains->first();
    $baseUrl = $domain ? "https://{$domain->domain}" : config('app.url');

    return "{$baseUrl}/{$tenant->id}/line/bind";
  }
}
