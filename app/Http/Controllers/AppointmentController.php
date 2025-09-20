<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AppointmentController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the appointment booking form.
     */
    public function create()
    {
        $user = Auth::user();
        $appointmentTypes = ['donation', 'screening', 'consultation', 'follow_up'];
        
        // Get available time slots (you can customize these)
        $timeSlots = [
            '09:00', '09:30', '10:00', '10:30', '11:00', '11:30',
            '13:00', '13:30', '14:00', '14:30', '15:00', '15:30',
            '16:00', '16:30', '17:00', '17:30'
        ];
        
        return view('appointments.create', compact('appointmentTypes', 'timeSlots'));
    }

    /**
     * Store a new appointment.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'appointment_type' => 'required|string|max:50',
            'appointment_date' => 'required|date|after:today',
            'time_slot' => 'required|string|max:20',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Check if the time slot is available
        $existingAppointment = Appointment::where('appointment_date', $request->appointment_date)
            ->where('time_slot', $request->time_slot)
            ->where('status', '!=', 'cancelled')
            ->first();

        if ($existingAppointment) {
            return back()->withErrors(['time_slot' => 'This time slot is already booked. Please choose another time.'])->withInput();
        }

        try {
            $appointment = Appointment::create([
                'user_id' => Auth::id(),
                'appointment_type' => $request->appointment_type,
                'appointment_date' => $request->appointment_date,
                'time_slot' => $request->time_slot,
                'status' => 'pending',
                'notes' => $request->notes,
            ]);

            return redirect('/user/dashboard')->with('status', 'Your appointment has been submitted and is awaiting admin approval.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to book appointment. Please try again.'])->withInput();
        }
    }

    /**
     * Show appointment details.
     */
    public function show($id)
    {
        $user = Auth::user();
        $appointment = $user->appointments()->findOrFail($id);
        
        return view('appointments.show', compact('appointment'));
    }

    /**
     * Show the edit form for an appointment.
     */
    public function edit($id)
    {
        $user = Auth::user();
        $appointment = $user->appointments()->where('status', 'pending')->findOrFail($id);
        
        $appointmentTypes = ['donation', 'screening', 'consultation', 'follow_up'];
        $timeSlots = [
            '09:00', '09:30', '10:00', '10:30', '11:00', '11:30',
            '13:00', '13:30', '14:00', '14:30', '15:00', '15:30',
            '16:00', '16:30', '17:00', '17:30'
        ];
        
        return view('user.appointment-edit', compact('appointment', 'appointmentTypes', 'timeSlots'));
    }

    /**
     * Update an appointment.
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $appointment = $user->appointments()->where('status', 'pending')->findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'appointment_type' => 'required|string|max:50',
            'appointment_date' => 'required|date|after:today',
            'time_slot' => 'required|string|max:20',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Check if the new time slot is available (excluding current appointment)
        $existingAppointment = Appointment::where('appointment_date', $request->appointment_date)
            ->where('time_slot', $request->time_slot)
            ->where('status', '!=', 'cancelled')
            ->where('id', '!=', $id)
            ->first();

        if ($existingAppointment) {
            return back()->withErrors(['time_slot' => 'This time slot is already booked. Please choose another time.'])->withInput();
        }

        try {
            $appointment->update($request->only([
                'appointment_type', 'appointment_date', 'time_slot', 'notes'
            ]));

            return redirect('/user/my-appointments')->with('status', 'Appointment updated successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to update appointment. Please try again.'])->withInput();
        }
    }

    /**
     * Cancel an appointment.
     */
    public function cancel($id)
    {
        $user = Auth::user();
        $appointment = $user->appointments()->whereIn('status', ['pending', 'confirmed'])->findOrFail($id);
        
        try {
            $appointment->update(['status' => 'cancelled']);
            return redirect('/user/my-appointments')->with('status', 'Appointment cancelled successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to cancel appointment. Please try again.']);
        }
    }

    /**
     * Reschedule an appointment.
     */
    public function reschedule(Request $request, $id)
    {
        $user = Auth::user();
        $appointment = $user->appointments()->whereIn('status', ['pending', 'confirmed'])->findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'new_date' => 'required|date|after:today',
            'new_time' => 'required|string|max:20',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        // Check if the new time slot is available
        $existingAppointment = Appointment::where('appointment_date', $request->new_date)
            ->where('time_slot', $request->new_time)
            ->where('status', '!=', 'cancelled')
            ->where('id', '!=', $id)
            ->first();

        if ($existingAppointment) {
            return back()->withErrors(['new_time' => 'This time slot is already booked. Please choose another time.']);
        }

        try {
            $appointment->update([
                'appointment_date' => $request->new_date,
                'time_slot' => $request->new_time,
                'status' => 'pending', // Reset to pending for admin approval
            ]);

            return redirect('/user/my-appointments')->with('status', 'Appointment rescheduled successfully!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to reschedule appointment. Please try again.']);
        }
    }

    /**
     * Get available time slots for a specific date.
     */
    public function getAvailableSlots(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required|date|after:today',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Invalid date'], 400);
        }

        $allTimeSlots = [
            '09:00', '09:30', '10:00', '10:30', '11:00', '11:30',
            '13:00', '13:30', '14:00', '14:30', '15:00', '15:30',
            '16:00', '16:30', '17:00', '17:30'
        ];

        $bookedSlots = Appointment::where('appointment_date', $request->date)
            ->where('status', '!=', 'cancelled')
            ->pluck('time_slot')
            ->toArray();

        $availableSlots = array_diff($allTimeSlots, $bookedSlots);

        return response()->json([
            'date' => $request->date,
            'available_slots' => array_values($availableSlots),
            'booked_slots' => $bookedSlots
        ]);
    }

    /**
     * Get appointment statistics for the authenticated user.
     */
    public function getStats()
    {
        $user = Auth::user();
        
        $stats = [
            'total_appointments' => $user->appointments()->count(),
            'pending_appointments' => $user->appointments()->pending()->count(),
            'confirmed_appointments' => $user->appointments()->confirmed()->count(),
            'completed_appointments' => $user->appointments()->completed()->count(),
            'cancelled_appointments' => $user->appointments()->cancelled()->count(),
            'upcoming_appointments' => $user->appointments()->upcoming()->count(),
            'today_appointments' => $user->appointments()->today()->count(),
        ];
        
        return response()->json($stats);
    }

    /**
     * Show appointment calendar view.
     */
    public function calendar()
    {
        $user = Auth::user();
        $appointments = $user->appointments()
            ->where('appointment_date', '>=', now()->startOfMonth())
            ->where('appointment_date', '<=', now()->endOfMonth())
            ->get()
            ->groupBy('appointment_date');
        
        return view('appointments.calendar', compact('appointments'));
    }

    /**
     * Get appointments for a specific month (AJAX).
     */
    public function getMonthAppointments(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'year' => 'required|integer|min:2020|max:2030',
            'month' => 'required|integer|min:1|max:12',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Invalid date parameters'], 400);
        }

        $user = Auth::user();
        $startDate = \Carbon\Carbon::create($request->year, $request->month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $appointments = $user->appointments()
            ->whereBetween('appointment_date', [$startDate, $endDate])
            ->get()
            ->groupBy('appointment_date');

        return response()->json($appointments);
    }
}
