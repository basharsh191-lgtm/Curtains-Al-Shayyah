<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Curtain;
use App\Models\Project;
use App\Models\VisitorVisit;
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

        $monthStart = now()->startOfMonth();
        $previousMonthStart = $monthStart->copy()->subMonth();
        $now = now();

        $topViewedProduct = VisitorVisit::query()
            ->whereNotNull('product_slug')
            ->selectRaw('product_slug, MAX(product_name) as product_name, COUNT(*) as views')
            ->groupBy('product_slug')
            ->orderByDesc('views')
            ->first();

        return view('admin.dashboard', [
            'appointments' => $appointments,
            'stats' => [
                'appointments' => Appointment::query()->count(),
                'pending_appointments' => Appointment::query()->where('status', 'pending')->count(),
                'collections' => Curtain::query()->count(),
                'projects' => Project::query()->count(),
                'pending_messages' => 0,
            ],
            'analytics' => [
                'visits' => [
                    'total' => VisitorVisit::query()->distinct()->count('visitor_hash'),
                    'this_month' => VisitorVisit::query()
                        ->where('created_at', '>=', $monthStart)
                        ->where('created_at', '<=', $now)
                        ->distinct()
                        ->count('visitor_hash'),
                    'last_month' => VisitorVisit::query()
                        ->where('created_at', '>=', $previousMonthStart)
                        ->where('created_at', '<', $monthStart)
                        ->distinct()
                        ->count('visitor_hash'),
                ],
                'appointments' => [
                    'this_month' => Appointment::query()
                        ->whereBetween('created_at', [$monthStart, $now])
                        ->count(),
                    'last_month' => Appointment::query()
                        ->where('created_at', '>=', $previousMonthStart)
                        ->where('created_at', '<', $monthStart)
                        ->count(),
                ],
                'most_viewed_product' => $topViewedProduct,
            ],
        ]);
    }
}
