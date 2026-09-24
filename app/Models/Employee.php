<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $table = 'employee';
    protected $primaryKey = 'Id_employee';
    public $incrementing = false;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'Id_employee',
        'entry_date',
        'nama',
        'rank',
        'gender',
    ];

    public function workAllocations()
    {
        return $this->hasMany(WorkAllocation::class, 'Employee', 'Id_employee');
    }

    public function workCompletions()
    {
        return $this->hasMany(WorkCompletion::class, 'Employee', 'Id_employee');
    }
}
