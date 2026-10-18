<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_login_page_is_available(): void
    {
        $this->get(route('admin.login'))
            ->assertOk()
            ->assertSee('تسجيل دخول الإدارة')
            ->assertSee('بإدارة أمجد الشياح')
            ->assertSee('action="/admin/login"', false);
    }

    public function test_guests_are_redirected_to_admin_login(): void
    {
        $this->get(route('admin.dashboard'))
            ->assertRedirect('/admin/login');
    }

    public function test_owner_can_login_and_view_dashboard(): void
    {
        $owner = User::factory()->create([
            'name' => 'أمجد الشياح',
            'email' => 'amjad@gmail.com',
            'password' => 'correct-password',
        ]);

        $this->post(route('admin.login.store'), [
            'email' => 'amjad@gmail.com',
            'password' => 'correct-password',
            'remember' => '1',
        ])->assertRedirect('/admin');

        $this->assertAuthenticatedAs($owner);
        $this->get(route('admin.dashboard'))->assertOk();
    }

    public function test_non_owner_account_cannot_access_admin(): void
    {
        User::factory()->create([
            'email' => 'staff@example.com',
            'password' => 'correct-password',
        ]);

        $this->post(route('admin.login.store'), [
            'email' => 'staff@example.com',
            'password' => 'correct-password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_owner_can_logout_and_is_redirected_to_login(): void
    {
        $owner = User::factory()->create([
            'email' => 'amjad@gmail.com',
        ]);

        $this->actingAs($owner)
            ->post(route('admin.logout'))
            ->assertRedirect('/admin/login');

        $this->assertGuest();
    }

    public function test_admin_account_can_be_created_securely_from_the_console(): void
    {
        $this->artisan('admin:create')
            ->expectsQuestion('اسم صاحب المعرض', 'أمجد الشياح')
            ->expectsQuestion('كلمة المرور (12 حرفاً على الأقل)', 'secure-test-password')
            ->expectsQuestion('تأكيد كلمة المرور', 'secure-test-password')
            ->expectsOutputToContain('تم إعداد حساب الإدارة: amjad@gmail.com')
            ->assertExitCode(0);

        $this->assertDatabaseHas('users', [
            'email' => 'amjad@gmail.com',
            'name' => 'أمجد الشياح',
        ]);
    }
}
