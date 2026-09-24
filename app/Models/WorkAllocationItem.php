<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkAllocationItem extends Model
{
    use HasFactory;

    protected $table = 'workallocationitem';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'IDM',
        'Ordinal',
        'Qty',
        'Weight',
        'FG',
    ];

    protected $casts = [
        'IDM' => 'integer',
        'Ordinal' => 'integer',
        'Qty' => 'integer',
        'Weight' => 'float',
        'FG' => 'integer',
    ];

    public function workAllocation()
    {
        return $this->belongsTo(WorkAllocation::class, 'IDM', 'ID');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'FG', 'Id_product');
    }

    public function completionItem()
    {
        return $this->hasOne(WorkCompletionItem::class, 'LinkID', 'IDM')
            ->where('LinkOrd', $this->Ordinal);
    }
}
