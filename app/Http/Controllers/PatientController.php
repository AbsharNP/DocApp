<?php

namespace App\Http\Controllers;

use App\Http\Requests\AppointmentRequest;
use App\Models\Appointment;
use App\Models\Appsession;
use App\Models\Doctor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class PatientController extends Controller
{
    public function getSessionsByDoctorId($doctorId)
    {
        $validator = Validator::make(['doctorId' => $doctorId], [
            'doctorId' => 'required|exists:doctors,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Invalid doctor ID.'], 400);
        }

        $doctor = Doctor::find($doctorId);
        
        $sessions = Appsession::where('doctor_id', $doctorId)->get();

        if ($sessions->isEmpty()) {
            return response()->json(['doctor' => $doctor, 'sessions' => [], 'message' => 'No sessions found for this doctor.'], 404);
        }

        return response()->json([
            'doctor' => $doctor,
            'sessions' => $sessions->map(function ($session) {
                return [
                    'id' => $session->id,
                    'session_date' => $session->session_date ? $session->session_date->format('Y-m-d') : null,
                    'start_time' => $session->start_time,
                    'end_time' => $session->end_time,
                    'no_of_sessions' => $session->no_of_sessions,
                ];
            }),
        ]);
    }


    public function appStore(Request $request)
    {
        // Validate incoming request
        $request->validate([
            'session_id' => 'required|exists:appsessions,id', // Make sure this corresponds to your session table
            'dr_name' => 'required|string|max:255',
            'user_id' => [
                'required',
                'exists:users,id', // Assuming you have a users table
                function ($attribute, $value, $fail) use ($request) {
                    // Check if the user has already booked this session
                    $exists = Appointment::where('user_id', $value)
                        ->where('session_id', $request->session_id)
                        ->exists();
    
                    if ($exists) {
                        $fail('You have already taken an appointment for this session.');
                    }
                },
            ],
        ]);
    
        // Create the appointment
        $appointment = Appointment::create([
            'user_id' => $request->user_id, // Get the authenticated user's ID
            'name' => $request->name,
            'session_id' => $request->session_id,
            'doctor_id' => $request->doctor_id, // You'll need to pass this from the frontend or fetch it
            'dr_name' => $request->dr_name,
            'treatment_status' => 0, // Default treatment status
        ]);
    
        // Return a response (you can customize this as needed)
        return response()->json([
            'message' => 'Appointment created successfully',
            'appointment' => $appointment,
        ], 201);
    }
    

    public function myAppointments(Request $request)
{
    // Assuming the user ID is passed in the request body
    $userId = $request->input('user_id');

    if (!$userId) {
        return response()->json(['error' => 'User ID not provided.'], 400);
    }

    $appointments = Appointment::with(['appsession.doctor'])
        ->where('user_id', $userId) 
        ->get();

    return response()->json($appointments);
}

    public function destroyApp($id)
    {
        $appointment = Appointment::find($id);

        if (!$appointment) {
            return response()->json(['message' => 'Appointment not found'], Response::HTTP_NOT_FOUND);
        }

        $appointment->delete();

        return response()->json(['message' => 'Appointment deleted successfully'], Response::HTTP_OK);
    }

    public function getDoneAppointments(Request $request)
{
    $userId = $request->query('user_id'); 

    $appointments = Appointment::where('user_id', $userId)
                               ->where('treatment_status', 1) 
                               ->get();

    if ($appointments->isEmpty()) {
        return response()->json(['message' => 'No completed appointments found'], 404);
    }

    return response()->json($appointments, 200);
}

    public function getAppointmentById($id)
    {
        $appointment = Appointment::find($id);

        if (!$appointment) {
            return response()->json(['message' => 'Appointment not found'], 404);
        }

        return response()->json($appointment, 200);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
    
        // Validate the request
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'profile_pic' => 'nullable|image|max:10048',
            'password' => 'nullable|string|min:6',
        ]);
    
        $user->name = $request->name;
        $user->email = $request->email;
    
        if ($request->password) {
            $user->password = Hash::make($request->password);
        }
    
        if ($request->hasFile('profile_pic')) {
            $filePath = $request->file('profile_pic')->store('profile_pics', 'public');
            $user->profile_pic = $filePath;
        }
    
        $user->save();

        DB::table('appointments')
        ->where('user_id', $user->id)
        ->update(['name' => $user->name]);
    
        return response()->json(['message' => 'Profile updated successfully']);
    }
    public function getProfile()
    {
        $user = Auth::user();
        
        return response()->json($user);
    }

    public function getProfilePic($userId)
    {
        // Find the user by ID
        $user = User::find($userId);

        if ($user && $user->profile_pic) {
            // Return the profile picture URL
            return response()->json([
                'profile_pic' => Storage::url($user->profile_pic)
            ]);
        }

        // If no profile picture found, return a default picture
        return response()->json([
            'profile_pic' => url('/images/default-avatar.jpg')
        ]);
    }
}
