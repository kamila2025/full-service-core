<!DOCTYPE html>
<html lang="zh-TW">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $tenant->name }} - Line 會員綁定</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .bind-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .bind-header {
            background: linear-gradient(135deg, #00C851 0%, #007E33 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }

        .bind-form {
            padding: 2rem;
        }

        .form-control {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 12px 16px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #00C851;
            box-shadow: 0 0 0 0.2rem rgba(0, 200, 81, 0.25);
        }

        .btn-bind {
            background: linear-gradient(135deg, #00C851 0%, #007E33 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-bind:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0, 200, 81, 0.3);
        }

        .line-icon {
            color: #00C851;
            font-size: 2rem;
        }

        .loading {
            display: none;
        }

        .success-message {
            display: none;
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            padding: 1rem;
            border-radius: 10px;
            margin-top: 1rem;
        }

        .error-message {
            display: none;
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 1rem;
            border-radius: 10px;
            margin-top: 1rem;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="bind-container mt-5">
                    <div class="bind-header">
                        <i class="fab fa-line line-icon mb-3"></i>
                        <h2>{{ $tenant->name }}</h2>
                        <p class="mb-0">Line 會員綁定服務</p>
                    </div>

                    <div class="bind-form">
                        <form id="bindForm">
                            <input type="hidden" name="bind_token" value="{{ $bindToken }}">

                            <div class="mb-3">
                                <label for="name" class="form-label">
                                    <i class="fas fa-user me-2"></i>姓名
                                </label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>

                            <div class="mb-3">
                                <label for="phone" class="form-label">
                                    <i class="fas fa-phone me-2"></i>電話號碼
                                </label>
                                <input type="tel" class="form-control" id="phone" name="phone" required>
                            </div>

                            <div class="mb-4">
                                <label for="email" class="form-label">
                                    <i class="fas fa-envelope me-2"></i>電子郵件 (選填)
                                </label>
                                <input type="email" class="form-control" id="email" name="email">
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-bind">
                                    <i class="fas fa-link me-2"></i>綁定會員
                                </button>
                            </div>

                            <div class="loading text-center mt-3">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">處理中...</span>
                                </div>
                                <p class="mt-2">正在處理綁定請求...</p>
                            </div>

                            <div class="success-message">
                                <i class="fas fa-check-circle me-2"></i>
                                <strong>綁定成功！</strong> 您已成功綁定會員帳號。
                            </div>

                            <div class="error-message">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>綁定失敗！</strong> <span class="error-text"></span>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <small class="text-white">
                        <i class="fas fa-shield-alt me-1"></i>
                        您的資料將安全地儲存在 {{ $tenant->name }} 的系統中
                    </small>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('bindForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const form = e.target;
            const formData = new FormData(form);

            // 隱藏表單，顯示載入中
            form.style.display = 'none';
            document.querySelector('.loading').style.display = 'block';
            document.querySelector('.success-message').style.display = 'none';
            document.querySelector('.error-message').style.display = 'none';

            try {
                const response = await fetch('{{ route('tenant.line.bind.submit', $tenant->id) }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    }
                });

                const result = await response.json();

                // 隱藏載入中
                document.querySelector('.loading').style.display = 'none';

                if (result.success) {
                    document.querySelector('.success-message').style.display = 'block';
                    form.reset();
                } else {
                    document.querySelector('.error-message').style.display = 'block';
                    document.querySelector('.error-text').textContent = result.message || '發生未知錯誤';
                    form.style.display = 'block';
                }
            } catch (error) {
                document.querySelector('.loading').style.display = 'none';
                document.querySelector('.error-message').style.display = 'block';
                document.querySelector('.error-text').textContent = '網路連線錯誤，請稍後再試';
                form.style.display = 'block';
            }
        });

        // 自動填入 Line 用戶 ID (如果有的話)
        const urlParams = new URLSearchParams(window.location.search);
        const lineUserId = urlParams.get('line_user_id');
        if (lineUserId) {
            // 添加隱藏欄位
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'line_user_id';
            hiddenInput.value = lineUserId;
            document.getElementById('bindForm').appendChild(hiddenInput);
        }
    </script>
</body>

</html>
