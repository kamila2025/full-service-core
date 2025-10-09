<?php

namespace App\Exports\Admin;

use App\Models\Tenant;
use App\Enums\Admin\Tenant\TenantStatusEnum;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TenantExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
  protected $tenants;

  public function __construct($tenants)
  {
    $this->tenants = $tenants;
  }

  /**
   * @return \Illuminate\Support\Collection
   */
  public function collection()
  {
    return $this->tenants;
  }

  /**
   * @return array
   */
  public function headings(): array
  {
    return [
      '租戶ID',
      '租戶名稱',
      '到期時間',
      '管理人員',
      '租戶狀態',
      '建立時間',
    ];
  }

  /**
   * @param mixed $tenant
   * @return array
   */
  public function map($tenant): array
  {
    return [
      $tenant->id,
      $tenant->name,
      $tenant->expire_date,
      $tenant->user->name ?? '',
      TenantStatusEnum::from($tenant->status)->label(),
      $tenant->created_at->format('Y-m-d H:i:s'),
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
      'A' => 15, // 租戶ID
      'B' => 20, // 租戶名稱
      'C' => 20, // 到期時間
      'D' => 15, // 管理人員
      'E' => 15, // 租戶狀態
      'F' => 20, // 建立時間
    ];
  }
}
