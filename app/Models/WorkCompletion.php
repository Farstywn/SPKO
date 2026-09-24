<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkCompletion extends Model
{
    use HasFactory;

    protected $table = 'workcompletion';
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
        'WorkAllocation',
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
        return $this->hasMany(WorkCompletionItem::class, 'IDM', 'ID')->orderBy('Ordinal');
    }

    public function workAllocation()
    {
        return $this->belongsTo(WorkAllocation::class, 'WorkAllocation', 'SW');
    }
}
