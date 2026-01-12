@php
    $customizerHidden = 'customizer-hide';
@endphp

@extends('layouts/layoutMaster')

@section('title', '會員管理')

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}">
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
        $('.select2').select2({
            placeholder: '請選擇',
            allowClear: true,
        });

        // 初始化 flatpickr
        const flatpickr = $('.flatpickr');
        if (flatpickr) {
            flatpickr.flatpickr({
                allowInput: true,
                monthSelectorType: 'static',
                locale: 'zh_tw'
            });
        }
    </script>
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
                <div class="col-md-2">
                    <label for="searchName" class="form-label">會員名稱</label>
                    <input type="text" id="searchName" class="form-control">
                </div>
                <div class="col-md-2">
                    <label for="searchEmail" class="form-label">會員信箱</label>
                    <input type="text" id="searchEmail" class="form-control">
                </div>
                <div class="col-md-2">
                    <label for="searchPhone" class="form-label">會員手機</label>
                    <input type="text" id="searchPhone" class="form-control">
                </div>
                <div class="col-md-2">
                    <label for="searchGender" class="form-label">會員性別</label>
                    <select id="searchGender" class="form-select select2">
                        <option value="">全部</option>
                        <option value="male">男</option>
                        <option value="female">女</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="searchBirthdayStartDate" class="form-label">會員生日(起)</label>
                    <input type="text" class="flatpickr form-control" id="searchBirthdayStartDate"
                        name="searchBirthdayStartDate">
                </div>
                <div class="col-md-2">
                    <label for="searchBirthdayEndDate" class="form-label">會員生日(訖)</label>
                    <input type="text" class="flatpickr form-control" id="searchBirthdayEndDate"
                        name="searchBirthdayEndDate">
                </div>
                <div class="col-md-2">
                    <label for="searchStatus" class="form-label">郵遞區號</label>
                    <input type="text" id="searchZipcode" class="form-control">
                </div>
                <div class="col-md-2">
                    <label for="searchCity" class="form-label">會員城市</label>
                    <select id="searchCity" class="form-select select2">
                        <option value="">全部</option>
                        @foreach ($cities as $city)
                            <option value="{{ $city }}">{{ $city }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="searchDistrict" class="form-label">會員地區</label>
                    <select id="searchDistrict" class="form-select select2">
                        <option value="">全部</option>
                        @foreach ($districts as $district)
                            <option value="{{ $district }}">{{ $district }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="searchStatus" class="form-label">會員狀態</label>
                    <select id="searchStatus" class="form-select select2">
                        <option value="">全部</option>
                        <option value="active">啟用</option>
                        <option value="inactive">停用</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="searchCreatedStartDate" class="form-label">建立時間(起)</label>
                    <input type="text" class="flatpickr form-control" id="searchCreatedStartDate"
                        name="searchCreatedStartDate">
                </div>
                <div class="col-md-2">
                    <label for="searchCreatedEndDate" class="form-label">建立時間(訖)</label>
                    <input type="text" class="flatpickr form-control" id="searchCreatedEndDate"
                        name="searchCreatedEndDate">
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
                    <button type="button" id="exportBtn" class="btn btn-success">
                        <i class="bx bx-export me-1"></i>匯出
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
                        <th>會員名稱</th>
                        <th>會員信箱</th>
                        <th>會員手機</th>
                        <th>會員性別</th>
                        <th>會員生日</th>
                        <th>會員地址</th>
                        <th>會員狀態</th>
                        <th>建立時間</th>
                        <th></th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection
