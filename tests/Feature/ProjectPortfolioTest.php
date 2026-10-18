<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProjectPortfolioTest extends TestCase
{
    use RefreshDatabase;

    public function test_portfolio_only_displays_active_projects_in_sort_order(): void
    {
        Storage::fake('public');
        $later = $this->createProject(['title' => 'مشروع لاحق', 'sort_order' => 4]);
        $first = $this->createProject(['title' => 'المشروع الأول', 'sort_order' => 1]);
        $this->createProject(['title' => 'مشروع مخفي', 'is_active' => false]);

        $this->get(route('projects.index'))
            ->assertOk()
            ->assertSee('المشروع الأول')
            ->assertSee('مشروع لاحق')
            ->assertDontSee('مشروع مخفي')
            ->assertSee($first->image_url)
            ->assertSee('lightbox');

        $response = $this->get(route('projects.index'));
        $this->assertLessThan(
            strpos($response->getContent(), $later->title),
            strpos($response->getContent(), $first->title)
        );
    }

    public function test_owner_can_create_update_toggle_and_delete_projects(): void
    {
        Storage::fake('public');
        $this->actingAs($this->owner());

        $this->post(route('admin.projects.store'), [
            'title' => 'فيلا مودرن في دمشق',
            'description' => 'تنفيذ ستائر الصالون وغرف النوم.',
            'category' => 'ستائر صالون',
            'image' => $this->fakeProjectImage(),
            'is_active' => '1',
            'sort_order' => 3,
        ])->assertRedirect(route('admin.projects.index'));

        $project = Project::query()->firstOrFail();
        $oldImage = $project->image;
        Storage::disk('public')->assertExists($project->image);

        $this->put(route('admin.projects.update', $project), [
            'title' => 'فيلا مودرن محدثة',
            'description' => 'وصف محدث.',
            'category' => 'غرف نوم',
            'image' => $this->fakeProjectImage('updated.png'),
            'sort_order' => 1,
        ])->assertRedirect(route('admin.projects.index'));

        $project->refresh();
        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'title' => 'فيلا مودرن محدثة',
            'category' => 'غرف نوم',
            'is_active' => false,
        ]);
        Storage::disk('public')->assertMissing($oldImage);
        Storage::disk('public')->assertExists($project->image);

        $this->patch(route('admin.projects.toggle-active', $project))->assertRedirect(route('admin.projects.index'));
        $this->assertDatabaseHas('projects', ['id' => $project->id, 'is_active' => true]);

        $image = $project->image;
        $this->delete(route('admin.projects.destroy', $project))->assertRedirect(route('admin.projects.index'));
        $this->assertDatabaseMissing('projects', ['id' => $project->id]);
        Storage::disk('public')->assertMissing($image);
    }

    public function test_project_administration_is_restricted_to_the_owner(): void
    {
        $this->get(route('admin.projects.index'))->assertRedirect(route('admin.login'));

        $this->actingAs(User::factory()->create(['email' => 'staff@example.com']))
            ->get(route('admin.projects.index'))
            ->assertForbidden();
    }

    public function test_project_creation_requires_a_valid_image(): void
    {
        Storage::fake('public');
        $this->actingAs($this->owner())
            ->from(route('admin.projects.create'))
            ->post(route('admin.projects.store'), [
                'title' => 'مشروع بلا صورة',
                'description' => 'وصف',
                'category' => 'مكاتب',
                'sort_order' => 0,
            ])
            ->assertSessionHasErrors('image');

        $this->assertDatabaseCount('projects', 0);
    }

    private function owner(): User
    {
        return User::factory()->create(['email' => config('admin.email')]);
    }

    private function createProject(array $overrides = []): Project
    {
        return Project::query()->create(array_merge([
            'title' => 'مشروع تجريبي',
            'description' => 'وصف المشروع',
            'category' => 'صالون',
            'image' => 'projects/project.png',
            'is_active' => true,
            'sort_order' => 0,
        ], $overrides));
    }

    private function fakeProjectImage(string $name = 'project.png'): UploadedFile
    {
        return UploadedFile::fake()->createWithContent(
            $name,
            base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/a6kAAAAASUVORK5CYII=')
        );
    }
}
