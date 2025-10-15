<?php

namespace App\Http\Controllers\Tenant;

use App\Enums\Tenant\PermissionNameEnum;
use Illuminate\Http\Request;
use App\Services\Tenant\MemberService;
use App\Repositories\MemberRepository;
use Yajra\DataTables\Facades\DataTables;

class TenantMemberController extends BaseTenantController
{
  public function __construct(protected MemberService $memberService, protected MemberRepository $memberRepository) {}

  /**
   * 會員管理頁面
   */
  public function index(Request $request)
  {
    $this->authorizePermission(PermissionNameEnum::會員管理);

    if ($request->ajax()) {
      $attributes = $request->validate([
        'name'   => 'nullable|string|max:255',
        'phone'  => 'nullable|string|max:255',
        'email'  => 'nullable|string|max:255',
      ]);

      $records = $this->memberService->getMembers($attributes);

      return DataTables::of($records)
        ->addColumn('member_name',       fn($record) => $record->name)
        ->addColumn('member_email',      fn($record) => $record->email)
        ->addColumn('member_created_at', fn($record) => $record->created_at->format('Y-m-d H:i:s'))
        ->make(true);
    }

    return view('content.tenant.member.tenant-member');
  }

  /**
   * 顯示新增會員表單
   */
  public function create()
  {
    $this->authorizePermission(PermissionNameEnum::會員管理);

    return view('content.tenant.member.tenant-member-add');
  }

  /**
   * 創建會員
   */
  public function store(Request $request)
  {
    $this->authorizePermission(PermissionNameEnum::會員管理);

    try {
      $attributes = $request->validate([
        'name'     => 'required|string|max:255',
        'phone'    => 'nullable|string|max:20',
        'email'    => 'nullable|email|max:255',
        'birthday' => 'nullable|date',
        'gender'   => 'nullable|string|in:male,female',
        'city'     => 'nullable|string|max:100',
        'district' => 'nullable|string|max:100',
        'zipcode'  => 'nullable|string|max:10',
        'address'  => 'nullable|string|max:255',
        'status'   => 'required|string|in:active,inactive',
      ], [], [
        'name'     => '會員姓名',
        'phone'    => '會員手機',
        'email'    => '會員信箱',
        'birthday' => '會員生日',
        'gender'   => '會員性別',
        'city'     => '城市',
        'district' => '地區',
        'zipcode'  => '郵遞區號',
        'address'  => '地址',
        'status'   => '會員狀態',
      ]);

      $member = $this->memberService->createMember($attributes);

      return $this->successResponse('會員新增成功', ['redirect_url' => route('tenant.members.index', ['tenant' => tenant('id')])], 200);
    } catch (\Throwable $e) {
      return $this->errorResponse('會員新增失敗，請聯絡管理者', 500);
    }
  }

  /**
   * 顯示會員資料
   */
  public function show($id)
  {
    $this->authorizePermission(PermissionNameEnum::會員管理);

    $member = $this->memberRepository->findById($id);

    return $this->successResponse('會員取得成功', ['member' => $member], 200);
  }

  /**
   * 顯示編輯會員表單
   */
  public function edit($id)
  {
    $this->authorizePermission(PermissionNameEnum::會員管理);

    $member = $this->memberRepository->findById($id);

    return view('content.tenant.member.tenant-member-add', [
      'member' => $member,
    ]);
  }

  /**
   * 更新會員
   */
  public function update(Request $request, $id)
  {
    $this->authorizePermission(PermissionNameEnum::會員管理);

    try {
      $attributes = $request->validate([
        'name'     => 'required|string|max:255',
        'phone'    => 'nullable|string|max:20',
        'email'    => 'nullable|email|max:255',
        'birthday' => 'nullable|date',
        'gender'   => 'nullable|string|in:male,female',
        'city'     => 'nullable|string|max:100',
        'district' => 'nullable|string|max:100',
        'zipcode'  => 'nullable|string|max:10',
        'address'  => 'nullable|string|max:255',
        'status'   => 'required|string|in:active,inactive',
      ], [], [
        'name'     => '會員姓名',
        'phone'    => '會員手機',
        'email'    => '會員信箱',
        'birthday' => '會員生日',
        'gender'   => '會員性別',
        'city'     => '城市',
        'district' => '地區',
        'zipcode'  => '郵遞區號',
        'address'  => '地址',
        'status'   => '會員狀態',
      ]);

      $member = $this->memberService->updateMember($id, $attributes);

      return $this->successResponse('會員更新成功', ['redirect_url' => route('tenant.members.index', ['tenant' => tenant('id')])], 200);
    } catch (\Throwable $e) {
      return $this->errorResponse('會員更新失敗，請聯絡管理者', null, 500);
    }
  }

  /**
   * 刪除會員
   */
  public function destroy($id)
  {
    $this->authorizePermission(PermissionNameEnum::會員管理);

    try {
      $this->memberService->deleteMember($id);

      return $this->successResponse('會員刪除成功', ['redirect_url' => route('tenant.members.index', ['tenant' => tenant('id')])], 200);
    } catch (\Throwable $e) {
      return $this->errorResponse('會員刪除失敗，請聯絡管理者', null, 500);
    }
  }
}
