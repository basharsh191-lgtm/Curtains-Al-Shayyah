<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(): View
    {
        $this->authorizeOwner();

        return view('admin.projects.index', [
            'projects' => Project::query()->orderBy('sort_order')->orderBy('id')->paginate(15),
        ]);
    }

    public function create(): View
    {
        $this->authorizeOwner();

        return view('admin.projects.form', ['project' => new Project]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeOwner();
        $validated = $request->validate($this->rules());
        $validated['image'] = $request->file('image')->store('projects', 'public');
        $validated['is_active'] = $request->boolean('is_active');

        Project::query()->create($validated);

        return redirect()->route('admin.projects.index')->with('status', 'تمت إضافة المشروع بنجاح.');
    }

    public function edit(Project $project): View
    {
        $this->authorizeOwner();

        return view('admin.projects.form', compact('project'));
    }

    public function update(Request $request, Project $project): RedirectResponse
    {
        $this->authorizeOwner();
        $validated = $request->validate($this->rules($project));
        $validated['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            $oldImage = $project->image;
            $validated['image'] = $request->file('image')->store('projects', 'public');
            $project->update($validated);
            Storage::disk('public')->delete($oldImage);
        } else {
            $project->update($validated);
        }

        return redirect()->route('admin.projects.index')->with('status', 'تم تحديث المشروع بنجاح.');
    }

    public function destroy(Project $project): RedirectResponse
    {
        $this->authorizeOwner();
        $image = $project->image;
        $project->delete();
        Storage::disk('public')->delete($image);

        return redirect()->route('admin.projects.index')->with('status', 'تم حذف المشروع بنجاح.');
    }

    public function toggleActive(Project $project): RedirectResponse
    {
        $this->authorizeOwner();
        $project->update(['is_active' => ! $project->is_active]);

        return redirect()->route('admin.projects.index')->with('status', 'تم تحديث حالة عرض المشروع.');
    }

    private function rules(?Project $project = null): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'category' => ['nullable', 'string', 'max:100'],
            'image' => [$project ? 'nullable' : 'required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['required', 'integer', 'min:0'],
        ];
    }

    private function authorizeOwner(): void
    {
        abort_unless(
            strcasecmp((string) Auth::user()?->email, (string) config('admin.email')) === 0,
            403
        );
    }
}
