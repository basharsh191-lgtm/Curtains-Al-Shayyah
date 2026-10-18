<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Curtain;
use App\Models\Project;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class AdminDashboardController extends Controller
{
    public function __invoke(): View
    {
        abort_unless(
            strcasecmp((string) Auth::user()?->email, (string) config('admin.email')) === 0,
            403
        );

        $appointments = Appointment::query()
            ->latest()
            ->paginate(10);

        return view('admin.dashboard', [
            'appointments' => $appointments,
            'stats' => [
                'appointments' => Appointment::query()->count(),
                'pending_appointments' => Appointment::query()->where('status', 'pending')->count(),
                'collections' => Curtain::query()->count(),
                'projects' => Project::query()->count(),
                'pending_messages' => 0,
            ],
        ]);
    }
}
