<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Curtain;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CurtainController extends Controller
{
    public function index(): View
    {
        $this->authorizeOwner();

        return view('admin.curtains.index', [
            'curtains' => Curtain::query()->orderBy('sort_order')->orderBy('id')->paginate(15),
        ]);
    }

    public function create(): View
    {
        $this->authorizeOwner();

        return view('admin.curtains.form', ['curtain' => new Curtain]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeOwner();
        $validated = $request->validate($this->rules());
        $validated['image'] = $request->file('image')->store('curtains', 'public');
        $validated['is_active'] = $request->boolean('is_active');

        Curtain::query()->create($validated);

        return redirect()->route('admin.curtains.index')->with('status', 'تمت إضافة التشكيلة بنجاح.');
    }

    public function edit(Curtain $curtain): View
    {
        $this->authorizeOwner();

        return view('admin.curtains.form', compact('curtain'));
    }

    public function update(Request $request, Curtain $curtain): RedirectResponse
    {
        $this->authorizeOwner();
        $validated = $request->validate($this->rules($curtain));
        $validated['is_active'] = $request->boolean('is_active');

        if ($request->hasFile('image')) {
            $oldImage = $curtain->image;
            $validated['image'] = $request->file('image')->store('curtains', 'public');
            $curtain->update($validated);
            Storage::disk('public')->delete($oldImage);
        } else {
            $curtain->update($validated);
        }

        return redirect()->route('admin.curtains.index')->with('status', 'تم تحديث التشكيلة بنجاح.');
    }

    public function destroy(Curtain $curtain): RedirectResponse
    {
        $this->authorizeOwner();
        $image = $curtain->image;
        $curtain->delete();
        Storage::disk('public')->delete($image);

        return redirect()->route('admin.curtains.index')->with('status', 'تم حذف التشكيلة بنجاح.');
    }

    public function toggle(Curtain $curtain): RedirectResponse
    {
        $this->authorizeOwner();
        $curtain->update(['is_active' => ! $curtain->is_active]);

        return redirect()->route('admin.curtains.index')->with('status', 'تم تحديث حالة ظهور التشكيلة.');
    }

    private function rules(?Curtain $curtain = null): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', Rule::in(Curtain::CATEGORIES)],
            'material' => ['required', Rule::in(Curtain::MATERIALS)],
            'opacity_level' => ['required', Rule::in(Curtain::OPACITY_LEVELS)],
            'description' => ['nullable', 'string', 'max:1000'],
            'image' => [$curtain ? 'nullable' : 'required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
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
