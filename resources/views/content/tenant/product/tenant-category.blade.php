@php
    $customizerHidden = 'customizer-hide';
@endphp

@extends('layouts/layoutMaster')

@section('title', '分類管理')

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/@form-validation/umd/styles/index.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/sortablejs/sortable.css') }}" />
@endsection

@section('vendor-script')
    <script src="{{ asset('assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/@form-validation/umd/bundle/popular.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/@form-validation/umd/plugin-bootstrap5/index.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/@form-validation/umd/plugin-auto-focus/index.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/sortablejs/sortable.js') }}"></script>
@endsection

@section('page-style')
    <style>
        .cat-item {
            list-style: none;
            margin: .25rem 0;
        }

        .cat-card {
            border: 1px solid #e0e0e0;
            border-radius: .5rem;
            padding: .5rem .75rem;
            background: #fff;
        }

        .cat-list {
            padding-left: 1.25rem;
            margin: .25rem 0 0;
        }

        .drag-handle {
            cursor: grab;
        }

        .drag-handle:active {
            cursor: grabbing;
        }

        .collapsed>.cat-list {
            display: none;
        }
    </style>
@endsection

@section('page-script')
    <script>
        // 初始化 select2
        $('.select2').select2({
            dropdownParent: $('#categoryModal')
        });
    </script>
    <script src="{{ asset('js/tenant/product/tenant-category.js?v=' . time()) }}"></script>
@endsection

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <div class="d-flex flex-column justify-content-center">
            <h3 class="mb-1"> 分類管理 </h3>
        </div>
        <div class="d-flex align-content-center flex-wrap gap-3">
            <a href="javascript:void(0)" class="btn btn-primary" id="addMainCategoryBtn">
                <i class="bx bx-plus me-0 me-sm-2"></i>
                <span class="d-none d-sm-inline-block"> 新增主分類 </span>
            </a>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header d-flex align-items-center justify-content-between">
            <div class="d-flex gap-2">
                <button class="btn btn-sm btn-primary" id="saveSortBtn" disabled>儲存排序</button>
            </div>
        </div>
        <div class="card-body">
            <div id="categoryTree" class="dd-list p-0 m-0">
            </div>
        </div>
    </div>


    <!-- 商品分類 Modal  -->
    <div class="modal fade" id="categoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered1 modal-simple modal-add-new-cc">
            <div class="modal-content p-3 p-md-5">
                <div class="modal-body">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    <div class="text-center mb-4">
                        <h3 class="card-title">新增商品分類</h3>
                    </div>
                    <form id="categoryForm" class="row g-3" onsubmit="return false">
                        <input type="hidden" id="categoryId" name="categoryId" />
                        <input type="hidden" id="parentId" name="parent_id" />
                        <div class="col-12">
                            <label class="form-label" for="name">分類名稱</label>
                            <input type="text" id="name" name="name" class="form-control" />
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="status">分類狀態</label>
                            <select id="status" name="status" class="select2 form-select">
                                <option value="active">已發佈</option>
                                <option value="inactive">未發佈</option>
                            </select>
                        </div>
                        <div class="col-12 text-center">
                            <button type="reset" class="btn btn-label-secondary btn-reset mt-3" data-bs-dismiss="modal"
                                aria-label="Close">取消</button>
                            <button type="submit" class="btn btn-primary me-sm-3 me-1 mt-3" id="saveBtn">儲存</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- 商品分類 Modal  -->
@endsection
