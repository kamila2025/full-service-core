<!doctype html>
<html lang="zh-TW">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $tenant->name }} - Line 登入成功</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            padding: 40px;
            max-width: 500px;
            width: 100%;
            text-align: center;
        }

        .success-icon {
            width: 100px;
            height: 100px;
            background: #00C300;
            border-radius: 50%;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            color: white;
            animation: bounce 0.6s ease-in-out;
        }

        @keyframes bounce {

            0%,
            20%,
            50%,
            80%,
            100% {
                transform: translateY(0);
            }

            40% {
                transform: translateY(-10px);
            }

            60% {
                transform: translateY(-5px);
            }
        }

        h1 {
            color: #333;
            margin-bottom: 10px;
            font-size: 28px;
            font-weight: 600;
        }

        .subtitle {
            color: #666;
            margin-bottom: 30px;
            font-size: 16px;
        }

        .member-info {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 30px;
            text-align: left;
        }

        .member-info h3 {
            color: #333;
            margin-bottom: 15px;
            font-size: 18px;
            text-align: center;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #e9ecef;
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-label {
            color: #666;
            font-weight: 500;
        }

        .info-value {
            color: #333;
            font-weight: 600;
        }

        .actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        .btn {
            flex: 1;
            padding: 12px 20px;
            border-radius: 25px;
            font-size: 16px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background: #00C300;
            color: white;
        }

        .btn-primary:hover {
            background: #00A300;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 195, 0, 0.3);
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(108, 117, 125, 0.3);
        }

        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            margin-top: 20px;
        }

        .status-new {
            background: #d4edda;
            color: #155724;
        }

        .status-existing {
            background: #d1ecf1;
            color: #0c5460;
        }

        .avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            margin: 0 auto 15px;
            background: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            overflow: hidden;
        }

        .avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        @media (max-width: 480px) {
            .actions {
                flex-direction: column;
            }

            .container {
                padding: 30px 20px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="success-icon">✅</div>
        <h1>登入成功！</h1>
        <p class="subtitle">歡迎使用 {{ $tenant->name }} 的服務</p>

        @if (isset($lineUser) && $lineUser->profile)
            <div class="avatar">
                @if (isset($lineUser->profile['pictureUrl']) && $lineUser->profile['pictureUrl'])
                    <img src="{{ $lineUser->profile['pictureUrl'] }}" alt="Line 頭像">
                @else
                    👤
                @endif
            </div>
        @endif

        <div class="member-info">
            <h3>📋 會員資訊</h3>
            <div class="info-item">
                <span class="info-label">會員姓名</span>
                <span class="info-value">{{ $member->name }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">會員編號</span>
                <span class="info-value">#{{ $member->id }}</span>
            </div>
            @if ($member->phone)
                <div class="info-item">
                    <span class="info-label">聯絡電話</span>
                    <span class="info-value">{{ $member->phone }}</span>
                </div>
            @endif
            @if ($member->email)
                <div class="info-item">
                    <span class="info-label">電子郵件</span>
                    <span class="info-value">{{ $member->email }}</span>
                </div>
            @endif
            @if (isset($lineUser))
                <div class="info-item">
                    <span class="info-label">Line 使用者</span>
                    <span class="info-value">{{ $lineUser->line_user_id }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">綁定時間</span>
                    <span class="info-value">{{ $lineUser->created_at->format('Y-m-d H:i') }}</span>
                </div>
            @endif
        </div>

        <div class="status-badge {{ $isExisting ? 'status-existing' : 'status-new' }}">
            {{ $isExisting ? '🔄 重新登入' : '🆕 新會員註冊' }}
        </div>

        <div class="actions">
            <a href="#" class="btn btn-primary" onclick="window.close(); return false;">
                關閉視窗
            </a>
            <a href="{{ route('tenant.line.login', ['tenant' => $tenant->id]) }}" class="btn btn-secondary">
                重新登入
            </a>
        </div>
    </div>

    <script>
        // 自動關閉視窗（如果是從 Line 開啟的）
        if (window.opener) {
            setTimeout(() => {
                window.close();
            }, 5000);
        }

        // 通知父視窗登入成功
        if (window.opener) {
            window.opener.postMessage({
                type: 'LINE_LOGIN_SUCCESS',
                memberId: {{ $member->id }},
                @if (isset($lineUser))
                    lineUserId: '{{ $lineUser->line_user_id }}'
                @endif
            }, '*');
        }
    </script>
</body>

</html>
