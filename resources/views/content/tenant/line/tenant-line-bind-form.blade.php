<!doctype html>
<html lang="zh-TW">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $tenant->name }} - 綁定表單</title>
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
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin: 0 auto 15px;
            background: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            overflow: hidden;
        }

        .avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        h1 {
            color: #333;
            margin-bottom: 5px;
            font-size: 24px;
            font-weight: 600;
        }

        .subtitle {
            color: #666;
            font-size: 16px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
        }

        input[type="text"],
        input[type="tel"],
        input[type="email"] {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e1e5e9;
            border-radius: 10px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }

        input[type="text"]:focus,
        input[type="tel"]:focus,
        input[type="email"]:focus {
            outline: none;
            border-color: #00C300;
        }

        .submit-button {
            background: #00C300;
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 25px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 20px;
        }

        .submit-button:hover {
            background: #00A300;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 195, 0, 0.3);
        }

        .submit-button:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .loading {
            display: none;
            text-align: center;
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

        .success {
            background: #efe;
            color: #363;
            padding: 15px;
            border-radius: 10px;
            margin-top: 20px;
            display: none;
        }

        .line-info {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            text-align: center;
        }

        .line-info h3 {
            color: #333;
            margin-bottom: 5px;
            font-size: 16px;
        }

        .line-info p {
            color: #666;
            font-size: 14px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="avatar">
                @if (isset($lineUserData['pictureUrl']) && $lineUserData['pictureUrl'])
                    <img src="{{ $lineUserData['pictureUrl'] }}" alt="Line 頭像">
                @else
                    👤
                @endif
            </div>
            <h1>會員綁定</h1>
            <p class="subtitle">請填寫您的會員資訊</p>
        </div>

        @if (isset($lineUserData))
            <div class="line-info">
                <h3>✅ Line 登入成功</h3>
                <p>歡迎，{{ $lineUserData['displayName'] }}！</p>
            </div>
        @endif

        <form id="bindForm">
            <input type="hidden" name="line_user_id" value="{{ $lineUserData['userId'] ?? '' }}">
            <input type="hidden" name="bind_token" value="{{ $bindToken }}">

            <div class="form-group">
                <label for="name">姓名 *</label>
                <input type="text" id="name" name="name" value="{{ $lineUserData['displayName'] ?? '' }}"
                    required>
            </div>

            <div class="form-group">
                <label for="phone">電話 *</label>
                <input type="tel" id="phone" name="phone" required>
            </div>

            <div class="form-group">
                <label for="email">電子郵件</label>
                <input type="email" id="email" name="email">
            </div>

            <button type="submit" class="submit-button" id="submitBtn">
                完成綁定
            </button>
        </form>

        <div class="loading" id="loading">
            <div class="spinner"></div>
            <p style="margin-top: 10px; color: #666;">正在處理中...</p>
        </div>

        <div class="error" id="error">
            <p id="errorMessage"></p>
        </div>

        <div class="success" id="success">
            <p id="successMessage"></p>
        </div>
    </div>

    <script>
        document.getElementById('bindForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const submitBtn = document.getElementById('submitBtn');
            const loading = document.getElementById('loading');
            const error = document.getElementById('error');
            const success = document.getElementById('success');

            // 顯示載入狀態
            submitBtn.disabled = true;
            loading.style.display = 'block';
            error.style.display = 'none';
            success.style.display = 'none';

            try {
                const formData = new FormData(this);
                const response = await fetch('{{ route('tenant.line.bind.submit') }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute(
                            'content') || ''
                    }
                });

                const result = await response.json();

                if (result.success) {
                    success.style.display = 'block';
                    document.getElementById('successMessage').textContent = result.message;

                    // 3秒後跳轉到成功頁面
                    setTimeout(() => {
                        if (result.redirect_url) {
                            window.location.href = result.redirect_url;
                        }
                    }, 3000);
                } else {
                    throw new Error(result.message || '綁定失敗');
                }

            } catch (err) {
                error.style.display = 'block';
                document.getElementById('errorMessage').textContent = err.message;
            } finally {
                submitBtn.disabled = false;
                loading.style.display = 'none';
            }
        });
    </script>
</body>

</html>
