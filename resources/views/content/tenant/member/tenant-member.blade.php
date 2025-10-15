@php
    $customizerHidden = 'customizer-hide';
@endphp

@extends('layouts/layoutMaster')

@section('title', '會員管理')

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}">
@endsection

@section('vendor-script')
    <script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
@endsection

@section('page-script')
    <script src="{{ asset('js/tenant/member/tenant-member.js?v=' . time()) }}"></script>
@endsection

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <div class="d-flex flex-column justify-content-center">
            <h3 class="mb-1"> 會員管理 </h3>
        </div>
        <div class="d-flex align-content-center flex-wrap gap-3">
            <a href="{{ route('tenant.members.create', ['tenant' => tenant('id')]) }}" class="btn btn-primary">
                <span>
                    <i class="bx bx-plus me-0 me-sm-2"></i>
                    <span class="d-none d-sm-inline-block"> 新增會員 </span>
                </span>
            </a>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="searchName" class="form-label">會員名稱</label>
                    <input type="text" id="searchName" class="form-control" placeholder="搜尋會員名稱...">
                </div>
                <div class="col-md-4">
                    <label for="searchPhone" class="form-label">會員手機</label>
                    <input type="text" id="searchPhone" class="form-control" placeholder="搜尋會員手機...">
                </div>
                <div class="col-md-4">
                    <label for="searchEmail" class="form-label">會員信箱</label>
                    <input type="text" id="searchEmail" class="form-control" placeholder="搜尋會員信箱...">
                </div>
            </div>
            <div class="row g-3 mt-2">
                <div class="col-12 d-flex justify-content-end">
                    <button type="button" id="searchBtn" class="btn btn-primary me-2">
                        <i class="bx bx-search me-1"></i>搜尋
                    </button>
                    <button type="button" id="resetBtn" class="btn btn-outline-secondary">
                        <i class="bx bx-refresh me-1"></i>重置
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-datatable text-nowrap">
            <table class="member-datatable table border-top">
                <thead>
                    <tr>
                        <th>會員姓名</th>
                        <th>會員信箱</th>
                        <th>建立時間</th>
                        <th></th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection
