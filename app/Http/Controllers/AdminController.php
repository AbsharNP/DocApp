<?php

namespace App\Http\Controllers;

use App\Http\Requests\DoctorRequest;
use App\Models\Appointment;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function depStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:departments,name',
        ]);

        $department = Department::create([
            'name' => $request->name,
        ]);

        return response()->json($department, 201); 
    }

    public function depIndex()
    {
        return response()->json(Department::all());
    }

    public function depDestroy($id)
    {
        $department = Department::findOrFail($id);
        $department->delete(); 
        return response()->json(null, 204); 
    }


    public function docStore(DoctorRequest $request)
    {
        
        
        $doc='doctor';

        $doctor = Doctor::create([
            'name' => $request->name,
            'email' => $request->email,
            'doc_id' => $request->doc_id,
            'phone_number' => $request->phone_number,
            'department_id' => $request->department,
            'password' => bcrypt($request->password), 
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'user_type' => $doc,
            'password' => Hash::make($request->password), 
        ]);

        return response()->json([
            'message' => 'Doctor registered successfully',
            'doctor' => $doctor,
            'user' => $user, 
        ], 201);   
    
    }

    public function docIndex()
    {
        
        $doctors = Doctor::with('department')->get();

        return response()->json($doctors);
    }

    public function docDestroy(int $id)
    {
        $doctor = Doctor::find($id);

        if (!$doctor) {
            return response()->json(['message' => 'Doctor not found.'], 404);
        }

        $doctor->delete();

        return response()->json(['message' => 'Doctor soft-deleted successfully.']);
    }

    public function getAppointmentStats()
{
    $totalAppointments = Appointment::count();
    
    $completedAppointments = Appointment::where('treatment_status', 1)->count();
    
    $notCompletedAppointments = Appointment::where('treatment_status', 0)->count();
    
    return response()->json([
        'total' => $totalAppointments,
        'completed' => $completedAppointments,
        'not_completed' => $notCompletedAppointments,
    ]);
}

    
}
