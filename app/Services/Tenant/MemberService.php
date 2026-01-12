<?php

namespace App\Services\Tenant;

use App\Models\Member;
use App\Repositories\MemberRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\Tenant\TenantMemberExport;

class MemberService
{
  public function __construct(protected MemberRepository $memberRepository) {}

  /**
   * 創建會員
   */
  public function createMember(array $attributes): Member
  {
    try {
      DB::beginTransaction();

      $member = $this->memberRepository->create($attributes);

      DB::commit();

      return $member;
    } catch (\Throwable $e) {
      DB::rollBack();

      Log::error('創建會員失敗', [
        'message'     => $e->getMessage(),
        'attributes'  => $attributes,
        'tenant_id'   => tenant('id'),
      ]);

      throw $e;
    }
  }

  /**
   * 更新會員
   */
  public function updateMember(int $id, array $attributes): Member
  {
    try {
      DB::beginTransaction();

      $this->memberRepository->update($attributes, $id);

      DB::commit();

      return $this->memberRepository->findOrFail($id);
    } catch (\Throwable $e) {
      DB::rollBack();

      Log::error('更新會員失敗', [
        'message'     => $e->getMessage(),
        'member_id'   => $id,
        'attributes'  => $attributes,
        'tenant_id'   => tenant('id'),
      ]);

      throw $e;
    }
  }

  /**
   * 刪除會員
   */
  public function deleteMember(int $id): void
  {
    try {
      DB::beginTransaction();

      $this->memberRepository->delete($id);

      DB::commit();
    } catch (\Throwable $e) {
      DB::rollBack();

      Log::error('刪除會員失敗', [
        'message'   => $e->getMessage(),
        'member_id' => $id,
        'tenant_id' => tenant('id'),
      ]);

      throw $e;
    }
  }

  /**
   * 根據 ID 獲取會員
   */
  public function getMemberById(int $id): Member
  {
    return $this->memberRepository->findOrFail($id);
  }

  /**
   * 獲取會員列表
   */
  public function getMembers(array $filters = []): \Illuminate\Database\Eloquent\Collection
  {
    return $this->memberRepository->getMembers($filters);
  }

  /**
   * 匯出會員資料
   */
  public function exportMembers(array $filters = [])
  {
    try {
      $members = $this->memberRepository->getMembers($filters);

      $timestamp = now()->format('Y-m-d');
      $filename = "會員資料_{$timestamp}.xlsx";

      return Excel::download(new TenantMemberExport($members), $filename);
    } catch (\Throwable $e) {
      Log::error('匯出會員資料失敗', [
        'message'   => $e->getMessage(),
        'filters'   => $filters,
        'tenant_id' => tenant('id'),
      ]);

      throw $e;
    }
  }
}
