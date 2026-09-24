<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'product';
    protected $primaryKey = 'Id_product';
    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'Id_product',
        'sub_category',
        'serial_no',
        'description',
        'carat',
    ];

    /**
     * Format standard SKU code based on sub_category, serial_no, and carat
     */
    public function getSkuAttribute(): string
    {
        $cleanCarat = trim(explode('-', $this->carat)[0] ?? $this->carat);
        $skuMap = [
            128409 => 'CALP1.10043.20K.01.00.00.0.D12',
            767072 => 'LT1.10237.17K.01.03.00.0.000',
            772839 => 'CWTMT.10152.16K.01.00.00.0.000',
            772893 => 'GPMA2.10125.08K.01.03.00.0.000',
            877501 => 'KCCBM2.10047.20K.01.00.00.0.000',
        ];

        return $skuMap[$this->Id_product] ?? sprintf('%s.%s.%s.01.00.00.0.000', $this->sub_category, $this->serial_no, $cleanCarat);
    }

    public function workAllocationItems()
    {
        return $this->hasMany(WorkAllocationItem::class, 'FG', 'Id_product');
    }

    public function workCompletionItems()
    {
        return $this->hasMany(WorkCompletionItem::class, 'FG', 'Id_product');
    }
}
