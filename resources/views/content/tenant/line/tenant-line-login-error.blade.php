<!doctype html>
<html lang="zh-TW">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Line 登入錯誤</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .error-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            padding: 2.5rem;
            max-width: 400px;
        }

        .error-icon {
            width: 100px;
            height: 100px;
            background: #dc3545;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            color: white;
            margin: 0 auto 1.25rem;
        }

        .btn-line {
            background: #00C300;
            border: none;
            border-radius: 25px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-line:hover {
            background: #00A300;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 195, 0, 0.3);
        }

        .btn-secondary-custom {
            background: #6c757d;
            border: none;
            border-radius: 25px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-secondary-custom:hover {
            background: #5a6268;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(108, 117, 125, 0.3);
        }
    </style>
</head>

<body>
    <div class="container-fluid d-flex align-items-center justify-content-center min-vh-100 p-3">
        <div class="error-container text-center">
            <div class="error-icon">
                <i class="fas fa-times"></i>
            </div>
            <h1 class="h3 mb-3 text-dark">登入失敗</h1>
            <p class="text-muted mb-4 lh-base">{{ $message ?? '發生未知錯誤，請稍後再試。' }}</p>

            <div class="d-flex gap-3">
                <a href="#" class="btn btn-line flex-fill" onclick="window.close(); return false;">
                    <i class="fas fa-times me-2"></i>關閉視窗
                </a>
                <a href="javascript:history.back()" class="btn btn-secondary-custom flex-fill">
                    <i class="fas fa-arrow-left me-2"></i>返回重試
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
