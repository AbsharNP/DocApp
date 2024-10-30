<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Doctor extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'email', 'doc_id', 'department_id', 'phone_number'];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
