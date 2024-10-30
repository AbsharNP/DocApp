<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'name',
        'session_id',
        'treatment_status',
        'prescription',
        'dr_name',
        'doctor_id',
    ];

    public function appsession()
    {
        return $this->belongsTo(Appsession::class, 'session_id');
    }

    
}
