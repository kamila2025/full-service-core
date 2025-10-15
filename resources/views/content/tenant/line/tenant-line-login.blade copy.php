<!doctype html>
<html lang="zh-TW">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $tenant->name }} - Line 登入綁定</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      min-height: 100vh;
    }

    .authentication-wrapper {
      display: flex;
      flex-basis: 100%;
      min-height: 100vh;
      width: 100%;
    }

    .authentication-basic {
      align-items: center;
      display: flex;
      flex-direction: column;
      justify-content: center;
      min-height: 100vh;
      padding: 1.5rem;
    }

    .authentication-inner {
      max-width: 400px;
      width: 100%;
    }

    .app-brand {
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 2rem;
    }

    .app-brand-link {
      display: flex;
      align-items: center;
      text-decoration: none;
      color: inherit;
    }

    .app-brand-logo {
      width: 25px;
      height: 25px;
      background: #00C300;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-right: 0.5rem;
    }

    .app-brand-text {
      font-size: 1.25rem;
      font-weight: 600;
    }

    .btn-line {
      background: #00C300;
      border: none;
      border-radius: 0.375rem;
      padding: 0.75rem 1.5rem;
      font-weight: 600;
      transition: all 0.3s ease;
    }

    .btn-line:hover {
      background: #00A300;
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(0, 195, 0, 0.3);
    }

    .info-card {
      background: #f8f9fa;
      border-radius: 0.5rem;
      padding: 1.25rem;
      margin-top: 1.5rem;
    }

    .spinner-border-line {
      color: #00C300;
    }

    .alert-error {
      background: #fee;
      color: #c33;
      border: none;
      border-radius: 0.5rem;
      display: none;
    }
  </style>
</head>

<body>
  <div class="container-xxl">
    <div class="authentication-wrapper authentication-basic container-p-y">
      <div class="authentication-inner">
        <div class="card">
          <div class="card-body">
            <!-- Logo -->
            <div class="app-brand justify-content-center">
              <a href="#" class="app-brand-link gap-2">
                <span class="app-brand-logo">
                  <i class="fas fa-mobile-alt text-white"></i>
                </span>
                <span class="app-brand-text text-body fw-bold">{{ $tenant->name }}</span>
              </a>
            </div>
            <!-- /Logo -->

            <h4 class="mb-2 text-center">Line 登入綁定</h4>
            <p class="mb-4 text-center">登入後將自動取得您的 Line 資料</p>

            <div class="mb-3">
              <a href="{{ $loginUrl }}" class="btn btn-line d-grid w-100" id="loginBtn">
                <i class="fab fa-line me-2"></i>Line 登入綁定
              </a>
            </div>

            <div class="d-none text-center" id="loading">
              <div class="spinner-border spinner-border-line mb-2" role="status">
                <span class="visually-hidden">載入中...</span>
              </div>
              <p class="text-muted">正在處理中...</p>
            </div>

            <div class="alert alert-error" id="error" role="alert">
              <p class="mb-0" id="errorMessage"></p>
            </div>

            <div class="info-card">
              <h5 class="mb-3">
                <i class="fas fa-clipboard-list me-2"></i>綁定說明
              </h5>
              <ul class="list-unstyled mb-0 text-start">
                <li class="mb-2">
                  <i class="fas fa-check text-success me-2"></i>點擊上方按鈕進行 Line 登入
                </li>
                <li class="mb-2">
                  <i class="fas fa-check text-success me-2"></i>登入後將自動取得您的 Line 資料
                </li>
                <li class="mb-2">
                  <i class="fas fa-check text-success me-2"></i>填寫會員資訊完成綁定
                </li>
                <li class="mb-0">
                  <i class="fas fa-check text-success me-2"></i>綁定後即可享受專屬服務
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.getElementById('loginBtn').addEventListener('click', function(e) {
      // 顯示載入動畫
      document.getElementById('loading').classList.remove('d-none');
      document.getElementById('loginBtn').classList.add('d-none');

      // 隱藏錯誤訊息
      document.getElementById('error').classList.add('d-none');
    });

    // 檢查是否有錯誤參數
    const urlParams = new URLSearchParams(window.location.search);
    const error = urlParams.get('error');
    const errorDescription = urlParams.get('error_description');

    if (error) {
      document.getElementById('loading').classList.add('d-none');
      document.getElementById('loginBtn').classList.remove('d-none');
      document.getElementById('error').classList.remove('d-none');
      document.getElementById('errorMessage').textContent =
        errorDescription || '登入過程中發生錯誤，請重新嘗試';
    }
  </script>
</body>

</html>
