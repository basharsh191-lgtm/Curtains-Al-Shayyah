<?php

namespace Tests\Feature;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAppointmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_filter_appointments_by_status(): void
    {
        $this->actingAs($this->owner());
        Appointment::query()->create($this->appointmentData(['client_name' => 'طلب جديد']));
        Appointment::query()->create($this->appointmentData(['client_name' => 'طلب مؤكد', 'status' => 'confirmed']));
        Appointment::query()->create($this->appointmentData(['client_name' => 'طلب مكتمل', 'status' => 'completed']));

        $this->get(route('admin.appointments.index', ['status' => 'pending']))
            ->assertOk()
            ->assertSee('طلب جديد')
            ->assertDontSee('طلب مؤكد')
            ->assertDontSee('طلب مكتمل');

        $this->get(route('admin.appointments.index', ['status' => 'confirmed']))
            ->assertOk()
            ->assertSee('طلب مؤكد')
            ->assertDontSee('طلب جديد')
            ->assertDontSee('طلب مكتمل');
    }

    public function test_owner_can_update_appointment_status(): void
    {
        $this->actingAs($this->owner());
        $appointment = Appointment::query()->create($this->appointmentData());

        $this->patch(route('admin.appointments.update', $appointment), ['status' => 'confirmed'])
            ->assertRedirect();

        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => 'confirmed',
        ]);
    }

    public function test_owner_can_view_full_appointment_details(): void
    {
        $this->actingAs($this->owner());
        $appointment = Appointment::query()->create($this->appointmentData([
            'client_name' => 'عميل التفاصيل',
            'address' => 'المزة، دمشق',
            'address_details' => 'شارع الجلاء، بناء 12، الطابق الثالث',
            'notes' => 'ثلاث نوافذ في غرفة الجلوس',
        ]));

        $this->get(route('admin.appointments.show', $appointment))
            ->assertOk()
            ->assertSee('عميل التفاصيل')
            ->assertSee('المزة، دمشق')
            ->assertSee('شارع الجلاء، بناء 12، الطابق الثالث')
            ->assertSee('ثلاث نوافذ في غرفة الجلوس')
            ->assertSee('صباحاً')
            ->assertSee('مراسلة عبر واتساب');
    }

    public function test_guests_and_non_owners_cannot_manage_appointments(): void
    {
        $this->get(route('admin.appointments.index'))->assertRedirect(route('admin.login'));

        $this->actingAs(User::factory()->create(['email' => 'staff@example.com']))
            ->get(route('admin.appointments.index'))
            ->assertForbidden();
    }

    public function test_client_name_alias_and_local_whatsapp_number_are_available(): void
    {
        $appointment = Appointment::query()->create($this->appointmentData([
            'client_name' => 'العميل',
            'phone' => '0991234567',
        ]));

        $this->assertSame('العميل', $appointment->client_name);
        $this->assertSame('963991234567', $appointment->whatsapp_phone);
        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'customer_name' => 'العميل',
        ]);
    }

    private function owner(): User
    {
        return User::factory()->create(['email' => config('admin.email')]);
    }

    private function appointmentData(array $overrides = []): array
    {
        return array_merge([
            'client_name' => 'عميل تجريبي',
            'phone' => '+963 991234567',
            'address' => 'المزة، دمشق',
            'preferred_date' => now()->addDay()->toDateString(),
            'preferred_time' => 'صباحاً',
            'notes' => 'تفاصيل الطلب',
            'status' => 'pending',
        ], $overrides);
    }
}
