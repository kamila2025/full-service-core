<?php

namespace App\Enums\Tenant\Product;

enum ProductInventoryManagementEnum: string
{
    case 無庫存管理 = 'none';
    case 庫存管理 = 'store';
}
