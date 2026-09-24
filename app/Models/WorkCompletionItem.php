<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkCompletionItem extends Model
{
    use HasFactory;

    protected $table = 'workcompletionitem';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'IDM',
        'Ordinal',
        'Qty',
        'Weight',
        'LinkID',
        'LinkOrd',
        'FG',
    ];

    protected $casts = [
        'IDM' => 'integer',
        'Ordinal' => 'integer',
        'Qty' => 'integer',
        'Weight' => 'float',
        'LinkID' => 'integer',
        'LinkOrd' => 'integer',
        'FG' => 'integer',
    ];

    public function workCompletion()
    {
        return $this->belongsTo(WorkCompletion::class, 'IDM', 'ID');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'FG', 'Id_product');
    }

    public function allocationItem()
    {
        return $this->belongsTo(WorkAllocationItem::class, 'LinkID', 'IDM')
            ->where('Ordinal', $this->LinkOrd);
    }
}
