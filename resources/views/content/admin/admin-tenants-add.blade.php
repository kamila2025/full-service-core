@extends('layouts/layoutMaster')

@section('title', isset($tenant) ? '編輯租戶' : '新增租戶')

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
        $('.select2').select2();

        // 初始化 flatpickr
        const flatpickrDate = $('#expire_date');
        if (flatpickrDate) {
            flatpickrDate.flatpickr({
                allowInput: true,
                monthSelectorType: 'static',
                locale: 'zh_tw'
            });
        }
    </script>
    <script src="{{ asset('js/admin/admin-tenants-add.js?v=' . time()) }}"></script>
@endsection

@section('content')
    <div class="app-ecommerce">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
            <div class="d-flex flex-column justify-content-center">
                @if (isset($tenant))
                    <h3 class="mb-2 mt-3">{{ $tenant->name }}</h3>
                    <p class="text-muted">最後更新於 : {{ $tenant->updated_at->format('Y-m-d H:i') }}</p>
                @else
                    <h3 class="mb-1">新增租戶</h3>
                @endif
            </div>
            <div class="d-flex align-content-center flex-wrap gap-3">
                <a href="{{ route('admin.tenants.index') }}" class="btn btn-label-secondary">返回</a>
                @if (isset($tenant))
                    <a href="javascript:void(0)" type="button" id="deleteBtn" class="btn btn-label-danger">刪除</a>
                @endif
                <button type="button" id="saveButton" class="btn btn-primary">儲存</button>
            </div>
        </div>
        <form id="adminTenantForm">
            <div class="row">
                <div class="col-12">
                    <div class="card mb-2">
                        <div class="card-body">
                            <div class="row g-3">
                                <input type='hidden' id="tenant_id" name='tenant_id' value="{{ $tenant->id ?? '' }}">
                                <div class="col-md-6 mb-2">
                                    <label for="id" class="form-label">租戶ID</label>
                                    <input type="text" id="id" name="id" class="form-control"
                                        value="{{ isset($tenant) ? $tenant->id : $tenantId }}"
                                        {{ isset($tenant) ? 'readonly' : '' }}>
                                </div>

                                <div class="col-md-6 mb-2">
                                    <label for="name" class="form-label">租戶名稱</label>
                                    <input type="text" id="name" name="name" class="form-control"
                                        value="{{ isset($tenant) ? $tenant->name : $tenantId }}">
                                </div>

                                <div class="col-md-6 mb-2">
                                    <label for="email" class="form-label">租戶信箱</label>
                                    <input type="text" id="email" name="email" class="form-control"
                                        value="{{ isset($tenant) ? $tenant->email : '' }}">
                                </div>

                                <div class="col-md-6 mb-2 form-password-toggle">
                                    <label for="password" class="form-label">租戶密碼</label>
                                    <div class="input-group input-group-merge">
                                        <input type="password" id="password" class="form-control" name="password"
                                            value="" />
                                        <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-2">
                                    <label for="expire_date" class="form-label">到期時間</label>
                                    <input type="text" class="form-control" id="expire_date" name="expire_date"
                                        value="{{ isset($tenant) ? $tenant->expire_date : '' }}">
                                </div>

                                <div class="col-md-6 mb-2">
                                    <label for="status" class="form-label">租戶狀態</label>
                                    <select id="status" name="status" class="select2 form-select">
                                        <option value="activated"
                                            {{ isset($tenant) ? ($tenant->status == 'activated' ? 'selected' : '') : '' }}>
                                            已開通</option>
                                        <option value="unactivated"
                                            {{ isset($tenant) ? ($tenant->status == 'unactivated' ? 'selected' : '') : '' }}>
                                            未開通</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-2">
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6 mb-2">
                                    <label for="line_channel_id" class="form-label">Line Channel ID</label>
                                    <input type="text" id="line_channel_id" name="line_channel_id" class="form-control"
                                        value="{{ isset($tenant) ? $tenant->channel_id ?? '' : '' }}">
                                </div>

                                <div class="col-md-6 mb-2">
                                    <label for="line_channel_secret" class="form-label">Line Channel Secret</label>
                                    <input type="password" id="line_channel_secret" name="line_channel_secret"
                                        class="form-control"
                                        value="{{ isset($tenant) ? $tenant->channel_secret ?? '' : '' }}">
                                </div>

                                <div class="col-12 mb-2">
                                    <label for="line_channel_access_token" class="form-label">Line Channel
                                        Access Token</label>
                                    <textarea id="line_channel_access_token" name="line_channel_access_token" class="form-control" rows="3">{{ isset($tenant) ? $tenant->channel_access_token ?? '' : '' }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
