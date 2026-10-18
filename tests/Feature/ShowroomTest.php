<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowroomTest extends TestCase
{
    use RefreshDatabase;

    public function test_showroom_views_render_successfully(): void
    {
        foreach ([
            route('home'),
            route('products.index'),
            route('products.show', 'linen-light'),
            route('projects.index'),
            route('appointments.create'),
        ] as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_homepage_does_not_render_the_featured_collection_section(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('اختيارات تليق بمنزلك')
            ->assertDontSee('معرض الصور التفاعلي');
    }

    public function test_mobile_bottom_navigation_is_present_and_marks_current_page(): void
    {
        $this->get(route('products.index'))
            ->assertOk()
            ->assertSee('bottom-0 left-0 right-0 z-50 block', false)
            ->assertSee('md:hidden', false)
            ->assertSee('aria-current="page"', false)
            ->assertSee('احجز زيارة')
            ->assertSee('تواصل معنا');
    }

    public function test_image_preview_pages_handle_browser_back_once(): void
    {
        foreach ([
            route('home'),
            route('products.index'),
            route('projects.index'),
        ] as $url) {
            $this->get($url)
                ->assertOk()
                ->assertSee('@popstate.window="handlePopState()"', false)
                ->assertSee('window.imagePreviewHistory.open()', false);
        }
    }

    public function test_appointment_request_is_validated_and_saved(): void
    {
        $this->post(route('appointments.store'), [
            'client_name' => 'ليلى',
            'phone' => '0990000000',
            'address' => 'المزة',
            'address_details' => 'شارع الجلاء، بناء 12، الطابق الثالث',
            'preferred_date' => now()->addDay()->toDateString(),
            'preferred_time' => 'مساءً',
            'notes' => 'نافذتان في غرفة المعيشة',
            'model' => 'نسيم الكتّان',
        ])->assertRedirect(route('appointments.create'));

        $this->assertDatabaseHas('appointments', [
            'customer_name' => 'ليلى',
            'phone' => '0990000000',
            'address' => 'المزة',
            'address_details' => 'شارع الجلاء، بناء 12، الطابق الثالث',
            'preferred_time' => 'مساءً',
            'notes' => "الموديل المطلوب: نسيم الكتّان\nنافذتان في غرفة المعيشة",
        ]);

        $this->assertSame(
            now()->addDay()->toDateString(),
            \App\Models\Appointment::query()->firstOrFail()->preferred_date->toDateString()
        );

        $this->assertDatabaseHas('appointments', [
            'customer_name' => 'ليلى',
            'address_details' => 'شارع الجلاء، بناء 12، الطابق الثالث',
        ]);
    }

    public function test_booking_form_uses_dashboard_compatible_field_names(): void
    {
        $this->get(route('appointments.create'))
            ->assertOk()
            ->assertSee('name="client_name"', false)
            ->assertSee('name="phone"', false)
            ->assertSee('name="address"', false)
            ->assertSee('name="address_details"', false)
            ->assertSee('name="preferred_date"', false)
            ->assertSee('name="preferred_time"', false)
            ->assertSee('name="notes"', false);
    }

    public function test_legacy_booking_field_names_remain_supported(): void
    {
        $this->post(route('appointments.store'), [
            'name' => 'عميل قديم',
            'phone' => '0990000000',
            'district' => 'المزة',
            'date' => now()->addDay()->toDateString(),
        ])->assertRedirect(route('appointments.create'));

        $this->assertDatabaseHas('appointments', [
            'customer_name' => 'عميل قديم',
            'address' => 'المزة',
        ]);
    }

    public function test_appointment_request_rejects_invalid_date_and_time(): void
    {
        $this->from(route('appointments.create'))
            ->post(route('appointments.store'), [
                'client_name' => 'ليلى',
                'phone' => '0990000000',
                'address' => 'المزة',
                'preferred_date' => now()->subDay()->toDateString(),
                'preferred_time' => 'منتصف الليل',
            ])
            ->assertSessionHasErrors(['preferred_date', 'preferred_time']);

        $this->assertDatabaseCount('appointments', 0);
    }
}
