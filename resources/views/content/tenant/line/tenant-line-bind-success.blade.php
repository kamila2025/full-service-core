@php
    $customizerHidden = 'customizer-hide';
@endphp

@extends('layouts/layoutMaster')

@section('title', $tenant->name . ' - Line 登入成功')

@section('vendor-style')
@endsection

@section('page-style')
    <style>
        .success-icon {
            width: 100px;
            height: 100px;
            background: #06C755;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            color: white;
            margin: 0 auto 1.25rem;
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

        .member-info-card {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .info-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid #e9ecef;
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .btn-line {
            background: #06C755;
            border: none;
            border-radius: 0.375rem;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-line:hover {
            background: #05B34A;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(6, 199, 85, 0.3);
        }

        .btn-secondary-custom {
            background: #6c757d;
            border: none;
            border-radius: 0.375rem;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-secondary-custom:hover {
            background: #5a6268;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(108, 117, 125, 0.3);
        }

        .status-badge {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 600;
            margin-top: 1.25rem;
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
            margin: 0 auto 1rem;
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
    </style>
@endsection

@section('vendor-script')
@endsection

@section('page-script')
    <script>
        $(document).ready(function() {
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
                    lineUserId: '{{ $lineUser->line_user_id }}'
                }, '*');
            }
        });
    </script>
@endsection

@section('content')
    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner">
                <div class="card">
                    <div class="card-body">
                        <!-- Logo -->
                        <div class="app-brand justify-content-center">
                            <a href="{{ url('/') }}" class="app-brand-link gap-2">
                                <span class="app-brand-logo demo">@include('_partials.macros', [
                                    'width' => 25,
                                    'withbg' => 'var(--bs-primary)',
                                ])</span>
                                <span
                                    class="app-brand-text demo text-body fw-bold">{{ config('variables.templateName') }}</span>
                            </a>
                        </div>
                        <!-- /Logo -->

                        <div class="success-icon">
                            <i class="fas fa-check"></i>
                        </div>
                        <h1 class="h2 mb-2 text-dark">登入成功！</h1>
                        <p class="text-muted mb-4">歡迎使用 {{ $tenant->name }} 的服務</p>

                        @if (isset($lineUser) && $lineUser->profile)
                            <div class="avatar">
                                @if (isset($lineUser->profile['pictureUrl']) && $lineUser->profile['pictureUrl'])
                                    <img src="{{ $lineUser->profile['pictureUrl'] }}" alt="Line 頭像" class="img-fluid">
                                @else
                                    <i class="fas fa-user"></i>
                                @endif
                            </div>
                        @endif

                        <div class="member-info-card">
                            <h5 class="mb-3">
                                <i class="fas fa-user-circle me-2"></i>會員資訊
                            </h5>
                            <div class="info-item">
                                <span class="text-muted fw-medium">會員姓名</span>
                                <span class="text-dark fw-bold">{{ $member->name }}</span>
                            </div>
                            @if ($member->phone)
                                <div class="info-item">
                                    <span class="text-muted fw-medium">聯絡電話</span>
                                    <span class="text-dark fw-bold">{{ $member->phone }}</span>
                                </div>
                            @endif
                            @if ($member->email)
                                <div class="info-item">
                                    <span class="text-muted fw-medium">電子郵件</span>
                                    <span class="text-dark fw-bold">{{ $member->email }}</span>
                                </div>
                            @endif
                            @if (isset($lineUser))
                                <div class="info-item">
                                    <span class="text-muted fw-medium">Line 使用者</span>
                                    <span class="text-dark fw-bold">{{ $lineUser->line_user_id }}</span>
                                </div>
                                <div class="info-item">
                                    <span class="text-muted fw-medium">綁定時間</span>
                                    <span class="text-dark fw-bold">{{ $lineUser->created_at->format('Y-m-d H:i') }}</span>
                                </div>
                            @endif
                        </div>

                        <div class="status-badge {{ $isExisting ? 'status-existing' : 'status-new' }}">
                            {{ $isExisting ? '🔄 重新登入' : '🆕 新會員註冊' }}
                        </div>

                        <div class="d-flex gap-3 mt-4">
                            <a href="#" class="btn btn-line flex-fill" onclick="window.close(); return false;">
                                <i class="fas fa-times me-2"></i>關閉視窗
                            </a>
                            <a href="{{ route('tenant.line.login', ['tenant' => $tenant->id]) }}"
                                class="btn btn-secondary-custom flex-fill">
                                <i class="fas fa-redo me-2"></i>重新登入
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
