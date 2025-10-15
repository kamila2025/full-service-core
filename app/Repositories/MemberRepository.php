<?php

namespace App\Repositories;

use App\Models\Member;

class MemberRepository extends Repository
{
  protected $fieldSearchable = [
    'name' => 'like',
    'phone',
    'email',
  ];

  /**
   * Specify Model class name
   *
   * @return string
   */
  public function model(): string
  {
    return Member::class;
  }

  public function getMembers(array $attributes = [])
  {
    //$this->applyCriteria();

    $this->model = $this->model->when($attributes['name'] ?? false, function ($query) use ($attributes) {
      $query->where('name', 'like', '%' . $attributes['name'] . '%');
    });

    $this->model = $this->model->when($attributes['phone'] ?? false, function ($query) use ($attributes) {
      $query->where('phone', $attributes['phone']);
    });

    $this->model = $this->model->when($attributes['email'] ?? false, function ($query) use ($attributes) {
      $query->where('email', $attributes['email']);
    });

    return $this->model
      ->with('lineUser')
      ->latest()
      ->get();
  }

  /**
   * 根據 ID 查找會員
   */
  public function findById(int $id): ?Member
  {
    return $this->model->find($id);
  }

  /**
   * 創建會員
   */
  public function createMember(array $attributes): Member
  {
    return $this->model->create($attributes);
  }

  /**
   * 更新會員
   */
  public function updateMember(int $id, array $attributes): Member
  {
    $member = $this->findById($id);

    if (!$member) throw new \Exception('會員不存在');

    $member->update($attributes);

    return $member->fresh();
  }

  /**
   * 刪除會員
   */
  public function deleteMember(int $id): bool
  {
    $member = $this->findById($id);

    if (!$member) throw new \Exception('會員不存在');

    return $member->delete();
  }
}
