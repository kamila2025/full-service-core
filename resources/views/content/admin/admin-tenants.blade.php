@php
    $customizerHidden = 'customizer-hide';
@endphp

@extends('layouts/layoutMaster')

@section('title', '租戶管理')

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />
@endsection

@section('vendor-script')
    <script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/flatpickr/flatpickr.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/flatpickr/zh-tw.js') }}"></script>
@endsection

@section('page-script')
    <script>
        // 初始化 select2
        $('.select2').select2();
    </script>
    <script src="{{ asset('js/admin/admin-tenants.js?v=' . time()) }}"></script>
@endsection

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <div class="d-flex flex-column justify-content-center">
            <h3 class="mb-1">租戶管理</h3>
        </div>
        <div class="d-flex align-content-center flex-wrap gap-3">
            <a href="{{ route('admin.tenants.create') }}" class="btn btn-primary">
                <span>
                    <i class="bx bx-plus me-0 me-sm-2"></i>
                    <span class="d-none d-sm-inline-block"> 新增租戶 </span>
                </span>
            </a>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label for="searchId" class="form-label">租戶ID</label>
                    <input type="text" id="searchId" class="form-control" placeholder="搜尋租戶ID...">
                </div>
                <div class="col-md-3">
                    <label for="searchName" class="form-label">租戶名稱</label>
                    <input type="text" id="searchName" class="form-control" placeholder="搜尋租戶名稱...">
                </div>
                <div class="col-md-3">
                    <label for="searchStatus" class="form-label">租戶狀態</label>
                    <select id="searchStatus" class="select2 form-select">
                        <option value="">全部</option>
                        <option value="activated">已開通</option>
                        <option value="unactivated">未開通</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="searchUser" class="form-label">管理人員</label>
                    <select id="searchUser" class="select2 form-select">
                        <option value="">全部</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
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
            <table class="tenant-datatable table border-top">
                <thead>
                    <tr>
                        <th>租戶ID</th>
                        <th>租戶名稱</th>
                        <th>到期時間</th>
                        <th>管理人員</th>
                        <th>建立時間</th>
                        <th>狀態</th>
                        <th></th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection
