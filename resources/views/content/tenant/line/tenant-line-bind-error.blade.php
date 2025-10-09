<!DOCTYPE html>
<html lang="zh-TW">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Line 綁定錯誤</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .error-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .error-header {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }

        .error-content {
            padding: 2rem;
            text-align: center;
        }

        .error-icon {
            color: #dc3545;
            font-size: 4rem;
            margin-bottom: 1rem;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="error-container mt-5">
                    <div class="error-header">
                        <i class="fas fa-exclamation-triangle error-icon"></i>
                        <h2>綁定服務暫時無法使用</h2>
                    </div>

                    <div class="error-content">
                        <p class="text-muted mb-4">{{ $message }}</p>

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            請聯繫客服人員或稍後再試
                        </div>

                        <a href="javascript:history.back()" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>返回
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
