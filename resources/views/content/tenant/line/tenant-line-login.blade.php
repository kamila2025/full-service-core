<!doctype html>
<html lang="zh-TW">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $tenant->name }} - Line 登入綁定</title>
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

        .logo {
            width: 80px;
            height: 80px;
            background: #00C300;
            border-radius: 50%;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            color: white;
        }

        h1 {
            color: #333;
            margin-bottom: 10px;
            font-size: 24px;
            font-weight: 600;
        }

        .subtitle {
            color: #666;
            margin-bottom: 30px;
            font-size: 16px;
        }

        .login-button {
            background: #00C300;
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 25px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            width: 100%;
            margin-bottom: 20px;
        }

        .login-button:hover {
            background: #00A300;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 195, 0, 0.3);
        }

        .info {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-top: 20px;
            text-align: left;
        }

        .info h3 {
            color: #333;
            margin-bottom: 10px;
            font-size: 16px;
        }

        .info p {
            color: #666;
            font-size: 14px;
            line-height: 1.5;
            margin-bottom: 8px;
        }

        .loading {
            display: none;
            margin-top: 20px;
        }

        .spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #00C300;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .error {
            background: #fee;
            color: #c33;
            padding: 15px;
            border-radius: 10px;
            margin-top: 20px;
            display: none;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="logo">📱</div>
        <h1>{{ $tenant->name }}</h1>
        <p class="subtitle">Line 登入綁定服務</p>

        <a href="{{ $loginUrl }}" class="login-button" id="loginBtn">
            🔗 Line 登入綁定
        </a>

        <div class="loading" id="loading">
            <div class="spinner"></div>
            <p style="margin-top: 10px; color: #666;">正在處理中...</p>
        </div>

        <div class="error" id="error">
            <p id="errorMessage"></p>
        </div>

        <div class="info">
            <h3>📋 綁定說明</h3>
            <p>• 點擊上方按鈕進行 Line 登入</p>
            <p>• 登入後將自動取得您的 Line 資料</p>
            <p>• 填寫會員資訊完成綁定</p>
            <p>• 綁定後即可享受專屬服務</p>
        </div>
    </div>

    <script>
        document.getElementById('loginBtn').addEventListener('click', function(e) {
            // 顯示載入動畫
            document.getElementById('loading').style.display = 'block';
            document.getElementById('loginBtn').style.display = 'none';

            // 隱藏錯誤訊息
            document.getElementById('error').style.display = 'none';
        });

        // 檢查是否有錯誤參數
        const urlParams = new URLSearchParams(window.location.search);
        const error = urlParams.get('error');
        const errorDescription = urlParams.get('error_description');

        if (error) {
            document.getElementById('loading').style.display = 'none';
            document.getElementById('loginBtn').style.display = 'block';
            document.getElementById('error').style.display = 'block';
            document.getElementById('errorMessage').textContent =
                errorDescription || '登入過程中發生錯誤，請重新嘗試';
        }
    </script>
</body>

</html>
