<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorizeOwner();
        $status = $request->query('status');

        if ($status !== null && ! in_array($status, Appointment::STATUSES, true)) {
            abort(404);
        }

        $appointments = Appointment::query()
            ->withStatus($status)
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.appointments.index', [
            'appointments' => $appointments,
            'selectedStatus' => $status,
            'statusCounts' => Appointment::query()
                ->selectRaw('status, COUNT(*) as total')
                ->groupBy('status')
                ->pluck('total', 'status'),
        ]);
    }

    public function show(Appointment $appointment): View
    {
        $this->authorizeOwner();

        return view('admin.appointments.show', compact('appointment'));
    }

    public function update(Request $request, Appointment $appointment): RedirectResponse
    {
        $this->authorizeOwner();
        $validated = $request->validate([
            'status' => ['required', Rule::in(Appointment::STATUSES)],
        ]);

        $appointment->update($validated);

        return back()->with('success', 'تم تحديث حالة الطلب بنجاح.');
    }

    private function authorizeOwner(): void
    {
        abort_unless(
            strcasecmp((string) Auth::user()?->email, (string) config('admin.email')) === 0,
            403
        );
    }
}
