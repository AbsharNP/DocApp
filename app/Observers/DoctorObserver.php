<?php

namespace App\Observers;

use App\Models\Doctor;

class DoctorObserver
{
    public function creating(Doctor $doctor)
    {
        // Generate the next doc_id (e.g., DOC001, DOC002, etc.)
        $latestDoctor = Doctor::withTrashed()->latest('id')->first();
        $nextDocId = $latestDoctor ? (int) substr($latestDoctor->doc_id, 3) + 1 : 1;
        $doctor->doc_id = 'DOC' . str_pad($nextDocId, 3, '0', STR_PAD_LEFT);
    }
}
