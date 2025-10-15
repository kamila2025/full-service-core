@php
    $customizerHidden = 'customizer-hide';
@endphp

@extends('layouts/layoutMaster')

@section('title', $tenant->name . ' - Line 登入綁定')

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/@form-validation/umd/styles/index.min.css') }}" />
@endsection

@section('page-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/css/pages/page-auth.css') }}">
@endsection

@section('vendor-script')
@endsection

@section('page-script')
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

                        <h4 class="mb-4 text-center">Line 登入綁定</h4>

                        <div class="mb-3">
                            <a href="{{ $loginUrl }}" class="btn d-grid w-100" id="loginBtn"
                                style="background-color: #06C755; border: none; border-radius: 0.375rem; padding: 0.75rem 1.5rem; font-weight: 600; color: #FFFFFF; transition: all 0.3s ease; text-decoration: none; display: flex; align-items: center; justify-content: center;"
                                onmouseover="this.style.backgroundColor='#05B34A'; this.style.transform='translateY(-1px)'; this.style.boxShadow='0 4px 12px rgba(6, 199, 85, 0.3)'"
                                onmouseout="this.style.backgroundColor='#06C755'; this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                                <svg width="20" height="20" viewBox="0 0 24 24" style="margin-right: 8px;">
                                    <path fill="#FFFFFF"
                                        d="M19.365 9.863c.349 0 .63.285.63.631 0 .345-.281.63-.63.63H17.61v1.125h1.755c.349 0 .63.283.63.63 0 .344-.281.629-.63.629h-2.386c-.345 0-.627-.285-.627-.629V8.108c0-.345.282-.63.63-.63h2.386c.349 0 .63.285.63.63 0 .349-.281.63-.63.63H17.61v1.125h1.755zm-3.855 3.016c0 .27-.174.51-.432.596-.064.021-.133.031-.199.031-.211 0-.391-.09-.51-.25l-2.443-3.317v2.94c0 .344-.279.629-.631.629-.346 0-.626-.285-.626-.629V8.108c0-.27.173-.51.43-.595.06-.023.136-.033.194-.033.195 0 .375.104.495.254l2.462 3.33V8.108c0-.345.282-.63.63-.63.345 0 .63.285.63.63v4.771zm-6.741.018c0 .344-.282.629-.631.629-.345 0-.627-.285-.627-.629V8.108c0-.345.282-.63.63-.63.346 0 .628.285.628.63v4.771zm-2.466.629H4.917c-.345 0-.63-.285-.63-.629V8.108c0-.345.285-.63.63-.63.348 0 .63.285.63.63v4.141h1.755c.348 0 .63.283.63.629 0 .344-.282.629-.63.629M24 10.314C24 4.943 18.615.572 12 .572S0 4.943 0 10.314c0 4.811 4.27 8.842 10.035 9.608.391.082.923.258 1.058.59.12.301.079.766.038 1.08l-.164 1.02c-.045.301-.24 1.186 1.049.645 1.291-.539 6.916-4.078 9.436-6.975C23.176 14.393 24 12.458 24 10.314" />
                                </svg>
                                與LINE連動
                            </a>
                        </div>

                        <div class="d-none text-center" id="loading">
                            <div class="spinner-border mb-2" role="status" style="color: #06C755;">
                                <span class="visually-hidden">載入中...</span>
                            </div>
                            <p class="text-muted">正在處理中...</p>
                        </div>

                        <div class="alert" id="error" role="alert"
                            style="background: #fee; color: #c33; border: none; border-radius: 0.5rem; display: none;">
                            <p class="mb-0" id="errorMessage"></p>
                        </div>

                        <div style="background: #f8f9fa; border-radius: 0.5rem; padding: 1.25rem; margin-top: 1.5rem;">
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
@endsection
