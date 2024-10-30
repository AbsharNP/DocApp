<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appsession extends Model
{
    use HasFactory;

    protected $table = 'appsessions'; // Ensure this matches the actual table name
    protected $fillable = [
        'doctor_id',
        'dr_name',
        'session_date', // Use the correct column name
        'start_time',
        'end_time',
        'no_of_sessions',
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class, 'doctor_id');
    }

    protected $casts = [
        'session_date' => 'date',
    ];

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'session_id');
    }
}