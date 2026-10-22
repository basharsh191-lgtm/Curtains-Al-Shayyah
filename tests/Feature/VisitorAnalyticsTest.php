<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\User;
use App\Models\VisitorVisit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class VisitorAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_page_views_are_recorded_with_unique_visitor_and_product_data(): void
    {
        $visitorId = (string) Str::uuid();

        $this->withCookie('site_visitor', $visitorId)->get(route('home'))->assertOk();
        $this->withCookie('site_visitor', $visitorId)->get(route('products.show', 'linen-light'))->assertOk();
        $this->withCookie('site_visitor', $visitorId)->get(route('products.show', 'linen-light'))->assertOk();
        $this->withCookie('site_visitor', (string) Str::uuid())
            ->get(route('products.show', 'royal-velvet'))
            ->assertOk();

        $this->assertDatabaseCount('visitor_visits', 4);
        $this->assertSame(2, VisitorVisit::query()->distinct()->count('visitor_hash'));
        $this->assertDatabaseHas('visitor_visits', [
            'visitor_hash' => hash('sha256', $visitorId),
            'product_slug' => 'linen-light',
            'product_name' => 'نسيم الكتّان',
        ]);
        $this->assertSame(
            2,
            VisitorVisit::query()->where('product_slug', 'linen-light')->count()
        );
    }

    public function test_dashboard_shows_unique_visitors_appointment_trends_and_top_viewed_curtain(): void
    {
        $this->actingAs(User::factory()->create(['email' => config('admin.email')]));

        $this->recordVisit('visitor-one', null, null, now()->startOfMonth()->addDay());
        $this->recordVisit('visitor-one', 'linen-light', 'نسيم الكتّان', now()->startOfMonth()->addDay());
        $this->recordVisit('visitor-two', 'linen-light', 'نسيم الكتّان', now()->startOfMonth()->addDays(2));
        $this->recordVisit('visitor-three', 'royal-velvet', 'مخمل دمشق', now()->subMonth()->startOfMonth()->addDay());

        $this->recordAppointment(now()->startOfMonth()->addDay());
        $this->recordAppointment(now()->subMonth()->startOfMonth()->addDay());

        $this->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('إجمالي الزيارات الفريدة')
            ->assertSee('إجمالي طلبات المعاينة')
            ->assertSee('نسيم الكتّان')
            ->assertSee('هذا الشهر: 2 · الشهر الماضي: 1', false);

        $this->assertDatabaseCount('visitor_visits', 4);
    }

    private function recordVisit(string $visitorId, ?string $slug, ?string $name, $createdAt): void
    {
        $visit = VisitorVisit::query()->create([
            'visitor_hash' => hash('sha256', $visitorId),
            'product_slug' => $slug,
            'product_name' => $name,
        ]);

        $visit->forceFill([
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ])->save();
    }

    private function recordAppointment($createdAt): void
    {
        $appointment = Appointment::query()->create([
            'client_name' => 'عميل تجريبي',
            'phone' => '0991234567',
            'address' => 'دمشق',
            'preferred_date' => now()->addDay()->toDateString(),
            'preferred_time' => 'صباحاً',
            'status' => 'pending',
        ]);

        $appointment->forceFill([
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ])->save();
    }
}
