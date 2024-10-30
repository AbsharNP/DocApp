<?php

namespace App\Http\Controllers;

use App\Http\Requests\AppSessionRequest;
use App\Models\Appointment;
use App\Models\Appsession;
use App\Models\Doctor;
use Carbon\Carbon;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class DoctorController extends Controller
{

    public function getDoctorByEmail($email)
    {
        $doctor = Doctor::where('email', $email)->first();
        
        if ($doctor) {
            return response()->json(['id' => $doctor->id], 200);
        } else {
            return response()->json(['message' => 'Doctor not found'], 404);
        }
    }

    public function Sessionstore(AppSessionRequest $request)
    {
        $session = Appsession::create([
            'doctor_id' => $request->doctor_id,
            'dr_name' => $request->dr_name, 
            'session_date' => $request->session_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'no_of_sessions' => $request->no_of_sessions,
        ]);

        return response()->json(['message' => 'Session created successfully', 'session' => $session], 201);
    }

  

    public function getSessions($doctorId)
    {
        
        $sessions = Appsession::where('doctor_id', $doctorId)
                    ->where('session_date', '>=', now()) 
                    ->orderBy('session_date', 'asc')   
                    ->limit(7)  
                    ->get();

        return response()->json($sessions);
    }
    public function getAppointmentsByDoctorId($doctorId)
    {
        $appointments = Appointment::select('appointments.*', 'appsessions.session_date as session_date') 
            ->join('appsessions', 'appointments.session_id', '=', 'appsessions.id')
            ->where('appsessions.doctor_id', $doctorId)
            ->get();
    
        return response()->json($appointments);
    }


   
    
    public function getTodaysAppointmentsByDoctorId($doctorId) 
    {
        $today = now()->format('Y-m-d');
    
        $appointments = Appointment::select('appointments.*', 'appsessions.session_date as session_date') 
            ->join('appsessions', 'appointments.session_id', '=', 'appsessions.id')
            ->where('appsessions.doctor_id', $doctorId)
            ->whereDate('appsessions.session_date', $today) 
            ->get();
    
        return response()->json($appointments);
    } 
    

    

    public function submitPrescription(Request $request)
    {
        $request->validate([
            'appointmentId' => 'required|exists:appointments,id', 
            'prescription' => 'required|string',
            'treatment_status' => 'required|integer',
            'age' => 'required|integer',
        ]);

        $appointment = Appointment::find($request->appointmentId);

        $appointment->prescription = $request->prescription;
        $appointment->age = $request->age;
        $appointment->treatment_status = $request->treatment_status;
        $appointment->save(); 

        return response()->json(['message' => 'Prescription submitted successfully'], 200);
    }

    public function show($id)
    {
        $appointment = Appointment::with('appsession')->findOrFail($id);

        $data = [
            'name' => $appointment->name,
            'session_date' => optional($appointment->appsession)->session_date, 
            'treatment_status' => $appointment->treatment_status,
            'prescription' => $appointment->prescription,
        ];

        return response()->json($data);
    }

   

}
