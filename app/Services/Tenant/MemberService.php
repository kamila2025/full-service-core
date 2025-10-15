<?php

namespace App\Services\Tenant;

use App\Models\Member;
use App\Repositories\MemberRepository;
use Illuminate\Support\Facades\DB;
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
    return DB::transaction(function () use ($attributes) {
      return $this->memberRepository->create($attributes);
    });
  }

  /**
   * 更新會員
   */
  public function updateMember(int $id, array $attributes): Member
  {
    return DB::transaction(function () use ($id, $attributes) {
      return $this->memberRepository->updateMember($id, $attributes);
    });
  }

  /**
   * 刪除會員
   */
  public function deleteMember(int $id): bool
  {
    return DB::transaction(function () use ($id) {
      return $this->memberRepository->deleteMember($id);
    });
  }

  /**
   * 根據 ID 獲取會員
   */
  public function getMemberById(int $id): ?Member
  {
    return $this->memberRepository->findById($id);
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
    $members = $this->memberRepository->getMembers($filters);

    // 生成檔案名稱
    $timestamp = now()->format('Y-m-d');
    $filename = "會員資料_{$timestamp}.xlsx";

    return Excel::download(new TenantMemberExport($members), $filename);
  }
}
