@extends('layouts/layoutMaster')

@section('title', isset($user) ? '編輯員工' : '新增員工')

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
    </script>
    <script src="{{ asset('js/tenant/setting/tenant-user-add.js?v=' . time()) }}"></script>
@endsection

@section('content')
    <div class="app-ecommerce">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
            <div class="d-flex flex-column justify-content-center">
                @if (isset($user))
                    <h3 class="mb-2 mt-3">{{ $user->name }}</h3>
                    <p class="text-muted">最後更新於 : {{ $user->updated_at->format('Y-m-d H:i') }}</p>
                @else
                    <h3 class="mb-1">新增員工</h3>
                @endif
            </div>
            <div class="d-flex align-content-center flex-wrap gap-3">
                <a href="{{ route('tenant.users.index', ['tenant' => tenant('id')]) }}"
                    class="btn btn-label-secondary">返回</a>
                @if (isset($user))
                    <a href="javascript:void(0)" type="button" id="deleteBtn" class="btn btn-label-danger">刪除</a>
                @endif
                <button type="button" id="saveButton" class="btn btn-primary">儲存</button>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form id="userForm" class="row g-3">
                            <input type='hidden' id="user_id" name='user_id' value="{{ $user->id ?? '' }}">
                            <div class="col-md-6 mb-2">
                                <label for="name" class="form-label">員工姓名</label>
                                <input type="text" id="name" name="name" class="form-control"
                                    value="{{ isset($user) ? $user->name : '' }}">
                            </div>

                            <div class="col-md-6 mb-2">
                                <label for="email" class="form-label">員工信箱</label>
                                <input type="text" id="email" name="email" class="form-control"
                                    value="{{ isset($user) ? $user->email : '' }}">
                            </div>

                            <div class="col-md-6 mb-2 form-password-toggle">
                                <label for="password" class="form-label">員工密碼</label>
                                <div class="input-group input-group-merge">
                                    <input type="password" id="password" class="form-control" name="password"
                                        value="" />
                                    <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                                </div>
                            </div>

                            <div class="col-md-6 mb-2">
                                <label for="role" class="form-label">員工角色</label>
                                <select id="role" name="role" class="select2 form-select">
                                    <option value="">請選擇</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->name }}"
                                            @if (isset($user) && $user->hasRole($role->name)) selected @endif>
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">權限設定</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">

                                    <div class="row">
                                        @foreach ($permissions as $permission)
                                            <div class="col-md-6 col-lg-4 mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input permission-checkbox" type="checkbox"
                                                        name="permissions[]" value="{{ $permission->name }}"
                                                        id="permission_{{ $permission->id }}"
                                                        @if (isset($user) && $user->hasPermissionTo($permission->name)) checked @endif disabled>
                                                    <label class="form-check-label" for="permission_{{ $permission->id }}">
                                                        {{ \App\Enums\Tenant\PermissionNameEnum::getDisplayName($permission->name) }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
