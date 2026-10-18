<?php

namespace Tests\Feature;

use App\Models\Curtain;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CurtainCatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalog_only_displays_active_curtains(): void
    {
        Storage::fake('public');
        $active = $this->createCurtain(['title' => 'نسيم الكتان']);
        $this->createCurtain(['title' => 'مخمل مخفي', 'is_active' => false]);

        $this->get(route('products.index'))
            ->assertOk()
            ->assertSee($active->title)
            ->assertDontSee('مخمل مخفي')
            ->assertSee('كل الخامات');
    }

    public function test_catalog_image_opens_a_preview_instead_of_navigating_to_appointment(): void
    {
        Storage::fake('public');
        $this->createCurtain(['title' => 'نسيم الكتان']);

        $this->get(route('products.index'))
            ->assertOk()
            ->assertSee('aria-label="عرض صورة نسيم الكتان"', false)
            ->assertSee('معاينة صورة التشكيلة')
            ->assertSee('احجز زيارة منزلية لهذا الموديل')
            ->assertDontSee('اطلب معاينة لهذا الموديل');
    }

    public function test_owner_can_create_edit_toggle_and_delete_a_curtain(): void
    {
        Storage::fake('public');
        $this->actingAs($this->owner());

        $this->post(route('admin.curtains.store'), [
            'title' => 'نسيم الكتان',
            'category' => 'مودرن',
            'material' => 'كتان',
            'opacity_level' => 'شفاف',
            'description' => 'نسيج طبيعي يمرر الضوء.',
            'image' => $this->fakeCurtainImage(),
            'is_active' => '1',
            'sort_order' => 2,
        ])->assertRedirect(route('admin.curtains.index'));

        $curtain = Curtain::query()->firstOrFail();
        Storage::disk('public')->assertExists($curtain->image);

        $this->put(route('admin.curtains.update', $curtain), [
            'title' => 'نسيم الكتان الجديد',
            'category' => 'كلاسيك',
            'material' => 'مخمل',
            'opacity_level' => 'نصف تعتيم',
            'description' => 'وصف محدث.',
            'sort_order' => 1,
        ])->assertRedirect(route('admin.curtains.index'));

        $this->assertDatabaseHas('curtains', [
            'id' => $curtain->id,
            'title' => 'نسيم الكتان الجديد',
            'category' => 'كلاسيك',
            'is_active' => false,
        ]);

        $this->patch(route('admin.curtains.toggle', $curtain))->assertRedirect(route('admin.curtains.index'));
        $this->assertDatabaseHas('curtains', ['id' => $curtain->id, 'is_active' => true]);

        $this->delete(route('admin.curtains.destroy', $curtain))->assertRedirect(route('admin.curtains.index'));
        $this->assertDatabaseMissing('curtains', ['id' => $curtain->id]);
        Storage::disk('public')->assertMissing($curtain->image);
    }

    public function test_curtain_management_is_restricted_to_the_owner(): void
    {
        $this->get(route('admin.curtains.index'))->assertRedirect(route('admin.login'));

        $this->actingAs(User::factory()->create(['email' => 'staff@example.com']))
            ->get(route('admin.curtains.index'))
            ->assertForbidden();
    }

    public function test_dark_mode_toggle_is_available_across_admin_screens(): void
    {
        $owner = $this->owner();
        $this->actingAs($owner);

        foreach ([
            route('admin.dashboard'),
            route('admin.curtains.index'),
            route('admin.curtains.create'),
        ] as $url) {
            $this->get($url)
                ->assertOk()
                ->assertSee('shayyah-admin-theme')
                ->assertSee('admin-dark')
                ->assertSee('toggleTheme()');
        }
    }

    public function test_invalid_curtain_categories_are_rejected(): void
    {
        Storage::fake('public');
        $this->actingAs($this->owner())
            ->from(route('admin.curtains.create'))
            ->post(route('admin.curtains.store'), [
                'title' => 'تشكيلة غير صحيحة',
                'category' => 'تصنيف غير معروف',
                'material' => 'كتان',
                'opacity_level' => 'شفاف',
                'image' => $this->fakeCurtainImage(),
                'sort_order' => 0,
            ])
            ->assertSessionHasErrors('category');

        $this->assertDatabaseCount('curtains', 0);
    }

    private function owner(): User
    {
        return User::factory()->create(['email' => config('admin.email')]);
    }

    private function createCurtain(array $overrides = []): Curtain
    {
        return Curtain::query()->create(array_merge([
            'title' => 'ستارة اختبار',
            'category' => 'مودرن',
            'material' => 'كتان',
            'opacity_level' => 'شفاف',
            'description' => 'وصف تجريبي',
            'image' => 'curtains/test.jpg',
            'is_active' => true,
            'sort_order' => 0,
        ], $overrides));
    }

    private function fakeCurtainImage(): UploadedFile
    {
        return UploadedFile::fake()->createWithContent(
            'curtain.png',
            base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/a6kAAAAASUVORK5CYII=')
        );
    }
}
