@extends('layouts/layoutMaster')

@section('title', isset($member) ? '編輯會員' : '新增會員')

@section('vendor-style')
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/@form-validation/umd/styles/index.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/flatpickr/flatpickr.css') }}" />
@endsection

@section('vendor-script')
    <script src="{{ asset('assets/vendor/libs/@form-validation/umd/bundle/popular.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/@form-validation/umd/plugin-bootstrap5/index.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/libs/@form-validation/umd/plugin-auto-focus/index.min.js') }}"></script>
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
        const flatpickrDate = $('#birthday');
        if (flatpickrDate) {
            flatpickrDate.flatpickr({
                allowInput: true,
                monthSelectorType: 'static',
                locale: 'zh_tw'
            });
        }
    </script>
    <script src="{{ asset('js/tenant/member/tenant-member-add.js?v=' . time()) }}"></script>
@endsection

@section('content')
    <div class="app-ecommerce">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
            <div class="d-flex flex-column justify-content-center">
                @if (isset($member))
                    <h3 class="mb-2 mt-3">{{ $member->name }}</h3>
                    <p class="text-muted">最後更新於 : {{ $member->updated_at->format('Y-m-d H:i') }}</p>
                @else
                    <h3 class="mb-1">新增會員</h3>
                @endif
            </div>
            <div class="d-flex align-content-center flex-wrap gap-3">
                <a href="{{ route('tenant.members.index', ['tenant' => tenant('id')]) }}"
                    class="btn btn-label-secondary">返回</a>
                @if (isset($member))
                    <a href="javascript:void(0)" type="button" id="deleteBtn" class="btn btn-label-danger">刪除</a>
                @endif
                <button type="button" id="saveButton" class="btn btn-primary">儲存</button>
            </div>
        </div>
        <form id="memberForm">
            <input type='hidden' id="member_id" name='member_id' value="{{ $member->id ?? '' }}">

            <div class="row">
                <!-- 左側欄位 -->
                <div class="col-12 col-lg-8">
                    <!-- 會員資訊 -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-tile mb-0">會員資訊</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6 mb-2">
                                    <label for="name" class="form-label">會員名稱</label>
                                    <input type="text" id="name" name="name" class="form-control"
                                        value="{{ isset($member) ? $member->name : '' }}">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label for="phone" class="form-label">會員手機</label>
                                    <input type="text" id="phone" name="phone" class="form-control"
                                        value="{{ isset($member) ? $member->phone : '' }}">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label for="email" class="form-label">會員信箱</label>
                                    <input type="text" id="email" name="email" class="form-control"
                                        value="{{ isset($member) ? $member->email : '' }}">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label for="birthday" class="form-label">會員生日</label>
                                    <input type="text" id="birthday" name="birthday" class="form-control"
                                        value="{{ isset($member) ? $member->birthday : '' }}">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label for="gender" class="form-label">會員性別</label>
                                    <select id="gender" name="gender" class="select2 form-select">
                                        <option value="">請選擇</option>
                                        <option value="male"
                                            {{ isset($member) && $member->gender ? ($member->gender->value == 'male' ? 'selected' : '') : '' }}>
                                            男</option>
                                        <option value="female"
                                            {{ isset($member) && $member->gender ? ($member->gender->value == 'female' ? 'selected' : '') : '' }}>
                                            女</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- 會員資訊 -->

                    <!-- 會員地址 -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-tile mb-0">會員地址</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6 mb-2">
                                    <label for="city" class="form-label">城市</label>
                                    <select id="city" name="city" class="form-select" required>
                                        <option value="">請選擇城市</option>
                                        @if (isset($member) && $member->city)
                                            <option value="{{ $member->city }}" selected>{{ $member->city }}</option>
                                        @endif
                                    </select>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label for="district" class="form-label">地區</label>
                                    <select id="district" name="district" class="form-select" required>
                                        <option value="">請先選擇城市</option>
                                        @if (isset($member) && $member->district)
                                            <option value="{{ $member->district }}" selected>{{ $member->district }}
                                            </option>
                                        @endif
                                    </select>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label for="zipcode" class="form-label">郵遞區號</label>
                                    <input type="text" id="zipcode" name="zipcode" class="form-control"
                                        value="{{ isset($member) ? $member->zipcode : '' }}" readonly>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <label for="address" class="form-label">地址</label>
                                    <input type="text" id="address" name="address" class="form-control"
                                        value="{{ isset($member) ? $member->address : '' }}" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- 會員地址 -->
                </div>
                <!-- 左側欄位 -->

                <!-- 右側欄位 -->
                <div class="col-12 col-lg-4">
                    <!-- 會員狀態 -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-tile mb-0">會員狀態</h5>
                        </div>
                        <div class="card-body">
                            <div class="col-12 mb-2">
                                <select id="status" name="status" class="form-select select2">
                                    <option value="active"
                                        {{ isset($member) ? ($member->status->value == 'active' ? 'selected' : '') : 'selected' }}>
                                        啟用</option>
                                    <option value="inactive"
                                        {{ isset($member) ? ($member->status->value == 'inactive' ? 'selected' : '') : '' }}>
                                        停用</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <!-- 會員狀態 -->

                    <!-- Line User -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-tile mb-0">Line 資訊</h5>
                        </div>
                        <div class="card-body">
                            <div class="col-md-12 mb-2">
                                <label for="line_user_id" class="form-label">Line ID</label>
                                <input type="text" id="line_user_id" class="form-control"
                                    value="{{ isset($member) && $member->lineUser ? $member->lineUser->line_user_id : '' }}"
                                    disabled>
                            </div>
                            <div class="col-md-12 mb-2">
                                <label for="line_profile_displayName" class="form-label">Line 名稱</label>
                                <input type="text" id="line_profile_displayName" class="form-control"
                                    value="{{ isset($member) && $member->lineUser ? $member->lineUser->profile['displayName'] : '' }}"
                                    disabled>
                            </div>
                            <div class="col-md-12 mb-2">
                                <label for="line_profile_pictureUrl" class="form-label">Line 頭像連結</label>
                                <input type="text" id="line_profile_pictureUrl" class="form-control"
                                    value="{{ isset($member) && $member->lineUser ? $member->lineUser->profile['pictureUrl'] : '' }}"
                                    disabled>
                            </div>
                        </div>
                    </div>
                    <!-- Line User -->
                </div>
                <!-- 右側欄位 -->
            </div>
        </form>
    </div>
@endsection
