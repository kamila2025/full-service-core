@php
    $customizerHidden = 'customizer-hide';
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Line 登入錯誤')

@section('vendor-style')
@endsection

@section('page-style')
    <style>
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
    </style>
@endsection

@section('vendor-script')
@endsection

@section('page-script')
    <script>
        $(document).ready(function() {
            // 如果是從 Line 開啟的，通知父視窗
            if (window.opener) {
                window.opener.postMessage({
                    type: 'LINE_BIND_ERROR',
                    message: '{{ $message ?? '登入失敗' }}'
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
            </div>
        </div>
    </div>
@endsection
