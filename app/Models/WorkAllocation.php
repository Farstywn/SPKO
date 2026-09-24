<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkAllocation extends Model
{
    use HasFactory;

    protected $table = 'workallocation';
    protected $primaryKey = 'ID';
    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'ID',
        'Remarks',
        'Employee',
        'TransDate',
        'Process',
        'SW',
    ];

    protected $casts = [
        'TransDate' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'Employee', 'Id_employee');
    }

    public function items()
    {
        return $this->hasMany(WorkAllocationItem::class, 'IDM', 'ID')->orderBy('Ordinal');
    }

    public function workCompletion()
    {
        return $this->hasOne(WorkCompletion::class, 'WorkAllocation', 'SW');
    }
}
