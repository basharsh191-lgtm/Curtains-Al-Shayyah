<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $project->exists ? 'تعديل المشروع' : 'إضافة مشروع' }} | الشياح للستائر</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="min-h-screen">
    @php($isActive = old('is_active', $project->exists ? $project->is_active : true))
    <main
        class="admin-shell min-h-screen bg-[#F7F6F3] px-4 py-8 text-[#202A35] antialiased sm:px-6"
        x-data="{
            darkMode: localStorage.getItem('shayyah-admin-theme') === 'dark',
            toggleTheme() {
                this.darkMode = !this.darkMode;
                localStorage.setItem('shayyah-admin-theme', this.darkMode ? 'dark' : 'light');
            }
        }"
        :class="{ 'admin-dark': darkMode }"
    >
        <div class="mx-auto max-w-3xl">
            <div class="flex items-center justify-between gap-4">
                <a class="text-sm font-semibold text-[#79552F] hover:underline" href="{{ route('admin.projects.index') }}">← العودة إلى معرض الأعمال</a>
                <button
                    class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-full border border-[#E5E1DA] bg-white text-[#5B6470] transition hover:bg-[#F7F6F3] focus:outline-none focus:ring-2 focus:ring-[#79552F]"
                    type="button"
                    @click="toggleTheme()"
                    :aria-label="darkMode ? 'تفعيل المظهر الفاتح' : 'تفعيل المظهر الداكن'"
                    :aria-pressed="darkMode.toString()"
                    title="تغيير مظهر لوحة التحكم"
                >
                    <svg x-show="!darkMode" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M20.5 15.5A8.5 8.5 0 0 1 8.5 3.5a8.5 8.5 0 1 0 12 12Z"/></svg>
                    <svg x-cloak x-show="darkMode" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><circle cx="12" cy="12" r="4"/><path d="M12 2v2m0 16v2M4.93 4.93l1.42 1.42m11.3 11.3 1.42 1.42M2 12h2m16 0h2M4.93 19.07l-1.42-1.42m11.3-11.3-1.42-1.42"/></svg>
                </button>
            </div>
            <section class="mt-5 rounded-2xl border border-[#E5E1DA] bg-white p-5 shadow-sm sm:p-8">
                <h1 class="text-2xl font-semibold">{{ $project->exists ? 'تعديل بيانات المشروع' : 'إضافة مشروع جديد' }}</h1>
                <p class="mt-2 text-sm text-[#5B6470]">ارفع صورة واضحة للمشروع، بصيغة JPG أو PNG أو WebP وبحجم أقصى 5MB.</p>

                @if($errors->any())
                    <div class="mt-5 rounded-xl border border-[#E8C5C5] bg-[#FBF0F0] px-4 py-3 text-sm text-[#A23D3D]" role="alert">
                        <p class="font-semibold">يرجى مراجعة البيانات المدخلة:</p>
                        <ul class="mt-2 list-inside list-disc space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form class="mt-6 space-y-5" method="POST" enctype="multipart/form-data" action="{{ $project->exists ? route('admin.projects.update', $project) : route('admin.projects.store') }}">
                    @csrf
                    @if($project->exists)
                        @method('PUT')
                    @endif

                    <div>
                        <label class="mb-2 block text-sm font-semibold" for="title">عنوان المشروع</label>
                        <input class="min-h-12 w-full rounded-xl border border-[#E5E1DA] px-4" id="title" name="title" value="{{ old('title', $project->title) }}" required maxlength="255" placeholder="مثال: فيلا مودرن في دمشق">
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold" for="category">التصنيف (اختياري)</label>
                        <input class="min-h-12 w-full rounded-xl border border-[#E5E1DA] px-4" id="category" name="category" value="{{ old('category', $project->category) }}" maxlength="100" placeholder="مثال: ستائر صالون، غرف نوم، مكاتب">
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-semibold" for="description">وصف المشروع</label>
                        <textarea class="w-full rounded-xl border border-[#E5E1DA] px-4 py-3" id="description" name="description" rows="4" maxlength="2000">{{ old('description', $project->description) }}</textarea>
                    </div>

                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-sm font-semibold" for="sort_order">ترتيب العرض</label>
                            <input class="min-h-12 w-full rounded-xl border border-[#E5E1DA] px-4" id="sort_order" name="sort_order" type="number" min="0" value="{{ old('sort_order', $project->sort_order ?? 0) }}" required>
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-semibold" for="image">الصورة الرئيسية {{ $project->exists ? '(اختياري عند التعديل)' : '' }}</label>
                            @if($project->exists)
                                <img class="mb-3 h-32 w-32 rounded-xl object-cover" src="{{ $project->image_url }}" alt="الصورة الحالية للمشروع">
                            @endif
                            <input class="block min-h-12 w-full rounded-xl border border-[#E5E1DA] bg-white p-2" id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp" @required(!$project->exists)>
                        </div>
                    </div>

                    <label class="flex min-h-12 items-center gap-3 rounded-xl border border-[#E5E1DA] px-4">
                        <input class="h-5 w-5 accent-[#79552F]" type="checkbox" name="is_active" value="1" @checked($isActive)>
                        <span class="text-sm font-semibold">إظهار المشروع في معرض الأعمال</span>
                    </label>

                    <div class="flex flex-wrap gap-3 border-t border-[#EEEAE4] pt-5">
                        <button class="inline-flex min-h-12 items-center justify-center rounded-xl bg-[#202A35] px-6 font-semibold text-white hover:bg-[#344252]" type="submit">حفظ المشروع</button>
                        <a class="inline-flex min-h-12 items-center justify-center rounded-xl border border-[#E5E1DA] px-6 font-semibold hover:bg-[#F7F6F3]" href="{{ route('admin.projects.index') }}">إلغاء</a>
                    </div>
                </form>
            </section>
        </div>
    </main>
</body>
</html>
