<?php

namespace App\Exports\Tenant;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TenantMemberExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
  protected $members;

  public function __construct($members)
  {
    $this->members = $members;
  }

  /**
   * @return \Illuminate\Support\Collection
   */
  public function collection()
  {
    return $this->members;
  }

  /**
   * @return array
   */
  public function headings(): array
  {
    return [
      '會員名稱',
      '會員信箱',
      '會員手機',
      '會員性別',
      '會員生日',
      '會員地址',
      '會員狀態',
      '建立時間',
    ];
  }

  /**
   * @param mixed $member
   * @return array
   */
  public function map($member): array
  {
    return [
      $member->name ?? null,
      $member->email ?? null,
      $member->phone ?? null,
      $member->gender?->label(),
      $member->birthday?->format('Y-m-d'),
      $member->zipcode . $member->city . $member->district . $member->address ?? null,
      $member->status?->label(),
      $member->created_at->format('Y-m-d H:i:s'),
    ];
  }

  /**
   * @param Worksheet $sheet
   * @return array
   */
  public function styles(Worksheet $sheet)
  {
    return [
      // 標題行樣式
      1 => [
        'font' => [
          'bold' => true,
          'size' => 12,
        ],
        'fill' => [
          'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
          'startColor' => [
            'rgb' => 'E3F2FD',
          ],
        ],
        'alignment' => [
          'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
        ],
      ],
    ];
  }

  /**
   * @return array
   */
  public function columnWidths(): array
  {
    return [
      'A' => 10, // 會員名稱
      'B' => 20, // 會員信箱
      'C' => 15, // 會員手機
      'D' => 10, // 會員性別
      'E' => 15, // 會員生日
      'F' => 30, // 會員地址
      'G' => 10, // 會員狀態
      'H' => 15, // 建立時間
    ];
  }
}
