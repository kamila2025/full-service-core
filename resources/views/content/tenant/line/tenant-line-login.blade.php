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
    <style>
        #loginBtn:hover .position-absolute {
            background-color: rgba(0, 0, 0, 0.1) !important;
        }

        #loginBtn:active .position-absolute {
            background-color: rgba(0, 0, 0, 0.3) !important;
        }

        #loginBtn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(6, 199, 85, 0.3);
        }

        #loginBtn:active {
            transform: translateY(0);
        }
    </style>
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
                            <a href="{{ $loginUrl }}" class="btn w-100 position-relative overflow-hidden" id="loginBtn"
                                style="background-color: #06C755; border: 1px solid #06C755; border-radius: 0.375rem; padding: 0.75rem 1.5rem; font-weight: 600; color: #FFFFFF; text-decoration: none; display: flex; align-items: center; justify-content: center; transition: all 0.3s ease;">
                                <div class="position-absolute top-0 start-0 w-100 h-100 rounded"
                                    style="background-color: rgba(0, 0, 0, 0); transition: background-color 0.3s ease;">
                                </div>
                                <img src="{{ asset('assets/img/icons/line.png') }}" alt="Line"
                                    style="width: 16px; height: 16px; margin-right: 8px; position: relative; z-index: 1;">
                                <span style="position: relative; z-index: 1;">使用 LINE 帳號登入</span>
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
