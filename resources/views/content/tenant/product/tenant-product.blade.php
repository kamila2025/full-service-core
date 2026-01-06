@php
    $customizerHidden = 'customizer-hide';
@endphp

@extends('layouts/layoutMaster')

@section('title', '商品管理')

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
@endsection

@section('vendor-script')
    <script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
@endsection

@section('page-script')
    <script>
        // 初始化 select2
        $('.select2').select2({
            placeholder: '請選擇',
            allowClear: true,
        });
    </script>
    <script src="{{ asset('js/tenant/product/tenant-product.js?v=' . time()) }}"></script>
@endsection

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <div class="d-flex flex-column justify-content-center">
            <h3 class="mb-1"> 商品管理 </h3>
        </div>
        <div class="d-flex align-content-center flex-wrap gap-3">
            <a href="{{ route('tenant.products.create', ['tenant' => tenant('id')]) }}" class="btn btn-primary">
                <span>
                    <i class="bx bx-plus me-0 me-sm-2"></i>
                    <span class="d-none d-sm-inline-block"> 新增商品 </span>
                </span>
            </a>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label for="searchName" class="form-label">商品名稱</label>
                    <input type="text" id="searchName" class="form-control" placeholder="搜尋商品名稱...">
                </div>
                <div class="col-md-3">
                    <label for="searchStatus" class="form-label">發佈狀態</label>
                    <select id="searchStatus" class="select2 form-select">
                        <option value="">全部</option>
                        <option value="active">已發佈</option>
                        <option value="inactive">未發佈</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="searchCategory" class="form-label">分類</label>
                    <select id="searchCategory" class="select2 form-select" multiple>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row g-3 mt-2">
                <div class="col-12 d-flex justify-content-end">
                    <button type="button" id="resetBtn" class="btn btn-outline-secondary me-2">
                        <i class="bx bx-refresh me-1"></i>重置
                    </button>
                    <button type="button" id="searchBtn" class="btn btn-primary me-2">
                        <i class="bx bx-search me-1"></i>查詢
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-datatable text-nowrap">
            <table class="product-datatable table border-top">
                <thead>
                    <tr>
                        <th width="100">商品圖片</th>
                        <th>商品名稱</th>
                        <th width="100">庫存數量</th>
                        <th width="100">上架狀態</th>
                        <th width="100">價格</th>
                        <th width="100"></th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection
