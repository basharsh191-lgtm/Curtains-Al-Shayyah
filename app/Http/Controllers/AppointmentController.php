<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'client_name' => $request->input('client_name', $request->input('name')),
            'address' => $request->input('address', $request->input('district')),
            'preferred_date' => $request->input('preferred_date', $request->input('date')),
        ]);

        $validated = $request->validate([
            'client_name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:30', 'regex:/^[0-9+\-\s()]{7,30}$/'],
            'address' => ['required', 'string', 'max:255'],
            'address_details' => ['nullable', 'string', 'max:1000'],
            'preferred_date' => ['required', 'date', 'after_or_equal:today'],
            'preferred_time' => ['nullable', 'in:صباحاً,مساءً'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'model' => ['nullable', 'string', 'max:150'],
        ], [
            'client_name.required' => 'يرجى كتابة الاسم الكامل.',
            'client_name.max' => 'يجب ألا يتجاوز الاسم 100 حرف.',
            'phone.required' => 'يرجى إدخال رقم الهاتف.',
            'phone.max' => 'يرجى إدخال رقم هاتف صحيح.',
            'phone.regex' => 'يرجى إدخال رقم هاتف صحيح.',
            'address.required' => 'يرجى اختيار المنطقة في دمشق.',
            'address.max' => 'يجب ألا يتجاوز العنوان 255 حرفاً.',
            'address_details.max' => 'يجب ألا يتجاوز تفصيل العنوان 1000 حرف.',
            'preferred_date.required' => 'يرجى اختيار التاريخ المناسب للزيارة.',
            'preferred_date.date' => 'يرجى إدخال تاريخ صحيح.',
            'preferred_date.after_or_equal' => 'يجب أن يكون موعد الزيارة اليوم أو بعده.',
            'preferred_time.in' => 'يرجى اختيار الفترة الصباحية أو المسائية.',
            'notes.max' => 'يجب ألا تتجاوز الملاحظات 1000 حرف.',
        ]);

        $notes = $validated['notes'] ?? null;
        if (! empty($validated['model'])) {
            $notes = trim('الموديل المطلوب: '.$validated['model']."\n".($notes ?? ''));
        }

        Appointment::query()->create([
            'client_name' => $validated['client_name'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'address_details' => $validated['address_details'] ?? null,
            'preferred_date' => $validated['preferred_date'],
            'preferred_time' => $validated['preferred_time'] ?? null,
            'notes' => $notes,
            'status' => 'pending',
        ]);

        return redirect()->route('appointments.create')->with('appointment_confirmation', [
            'client_name' => $validated['client_name'],
            'phone' => $validated['phone'],
            'preferred_date' => $validated['preferred_date'],
        ]);
    }
}
