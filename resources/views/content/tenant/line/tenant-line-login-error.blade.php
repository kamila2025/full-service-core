<!doctype html>
<html lang="zh-TW">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Line 登入錯誤</title>
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
            max-width: 400px;
            width: 100%;
            text-align: center;
        }

        .error-icon {
            width: 100px;
            height: 100px;
            background: #dc3545;
            border-radius: 50%;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            color: white;
        }

        h1 {
            color: #333;
            margin-bottom: 15px;
            font-size: 24px;
            font-weight: 600;
        }

        .error-message {
            color: #666;
            margin-bottom: 30px;
            font-size: 16px;
            line-height: 1.5;
        }

        .actions {
            display: flex;
            gap: 15px;
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
        <div class="error-icon">❌</div>
        <h1>登入失敗</h1>
        <p class="error-message">{{ $message ?? '發生未知錯誤，請稍後再試。' }}</p>

        <div class="actions">
            <a href="#" class="btn btn-primary" onclick="window.close(); return false;">
                關閉視窗
            </a>
            <a href="javascript:history.back()" class="btn btn-secondary">
                返回重試
            </a>
        </div>
    </div>

    <script>
        // 如果是從 Line 開啟的，通知父視窗
        if (window.opener) {
            window.opener.postMessage({
                type: 'LINE_BIND_ERROR',
                message: '{{ $message ?? '登入失敗' }}'
            }, '*');
        }
    </script>
</body>

</html>
