<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>إدارة التشكيلات | الشياح للستائر</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="min-h-screen">
    <main
        class="admin-shell min-h-screen bg-[#F7F6F3] px-4 py-8 text-[#202A35] antialiased sm:px-6 lg:px-8"
        x-data="{
            darkMode: localStorage.getItem('shayyah-admin-theme') === 'dark',
            toggleTheme() {
                this.darkMode = !this.darkMode;
                localStorage.setItem('shayyah-admin-theme', this.darkMode ? 'dark' : 'light');
            }
        }"
        :class="{ 'admin-dark': darkMode }"
    >
        <div class="mx-auto max-w-7xl">
        <header class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
            <div>
                <a class="text-sm font-semibold text-[#79552F] hover:underline" href="{{ route('admin.dashboard') }}">← العودة إلى لوحة التحكم</a>
                <h1 class="mt-3 text-3xl font-semibold">إدارة تشكيلات الستائر</h1>
                <p class="mt-2 text-sm text-[#5B6470]">إضافة وتعديل المنتجات التي تظهر في الكتالوج العام.</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <button
                    class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-[#E5E1DA] bg-white text-[#5B6470] transition hover:bg-[#F7F6F3] focus:outline-none focus:ring-2 focus:ring-[#79552F]"
                    type="button"
                    @click="toggleTheme()"
                    :aria-label="darkMode ? 'تفعيل المظهر الفاتح' : 'تفعيل المظهر الداكن'"
                    :aria-pressed="darkMode.toString()"
                    title="تغيير مظهر لوحة التحكم"
                >
                    <svg x-show="!darkMode" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                        <path d="M20.5 15.5A8.5 8.5 0 0 1 8.5 3.5a8.5 8.5 0 1 0 12 12Z"/>
                    </svg>
                    <svg x-cloak x-show="darkMode" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                        <circle cx="12" cy="12" r="4"/>
                        <path d="M12 2v2m0 16v2M4.93 4.93l1.42 1.42m11.3 11.3 1.42 1.42M2 12h2m16 0h2M4.93 19.07l1.42-1.42m11.3-11.3 1.42-1.42"/>
                    </svg>
                </button>
                <a class="inline-flex min-h-11 items-center justify-center rounded-xl bg-[#202A35] px-5 font-semibold text-white hover:bg-[#344252]" href="{{ route('admin.curtains.create') }}">إضافة تشكيلة</a>
            </div>
        </header>

        @if(session('status'))
            <div class="mb-5 rounded-xl border border-[#B8D8C5] bg-[#EFF8F2] px-4 py-3 text-sm text-[#205C40]" role="status">{{ session('status') }}</div>
        @endif

        <section class="overflow-hidden rounded-2xl border border-[#E5E1DA] bg-white shadow-sm" aria-label="قائمة التشكيلات">
            @if($curtains->isEmpty())
                <div class="px-6 py-16 text-center text-[#5B6470]">
                    <p class="text-lg font-semibold text-[#202A35]">لا توجد تشكيلات حتى الآن</p>
                    <p class="mt-2">أضف أول تشكيلة لتظهر في الكتالوج.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[760px] text-right text-sm">
                        <thead class="bg-[#FAF9F7] text-[#5B6470]">
                            <tr>
                                <th class="px-5 py-4 font-semibold">التشكيلة</th>
                                <th class="px-5 py-4 font-semibold">التصنيف والخامة</th>
                                <th class="px-5 py-4 font-semibold">الترتيب</th>
                                <th class="px-5 py-4 font-semibold">الحالة</th>
                                <th class="px-5 py-4 font-semibold">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#EEEAE4]">
                            @foreach($curtains as $curtain)
                                <tr>
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-3">
                                            <img class="h-14 w-14 rounded-lg object-cover" src="{{ $curtain->image_url }}" alt="">
                                            <div>
                                                <p class="font-semibold text-[#202A35]">{{ $curtain->title }}</p>
                                                <p class="mt-1 max-w-xs truncate text-xs text-[#7A838D]">{{ $curtain->description }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 text-[#5B6470]">
                                        <p>{{ $curtain->category }}</p>
                                        <p class="mt-1 text-xs">{{ $curtain->material }} · {{ $curtain->opacity_level }}</p>
                                    </td>
                                    <td class="px-5 py-4">{{ $curtain->sort_order }}</td>
                                    <td class="px-5 py-4">
                                        <form method="POST" action="{{ route('admin.curtains.toggle', $curtain) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button class="inline-flex min-h-11 items-center gap-2 rounded-full px-2 text-xs font-semibold text-[#5B6470]" type="submit" role="switch" aria-checked="{{ $curtain->is_active ? 'true' : 'false' }}" aria-label="تفعيل عرض {{ $curtain->title }}">
                                                <span class="relative h-6 w-11 rounded-full transition-colors {{ $curtain->is_active ? 'bg-[#28704F]' : 'bg-[#A1ACB7]' }}">
                                                    <span class="absolute right-1 top-1 h-4 w-4 rounded-full bg-white shadow transition-transform {{ $curtain->is_active ? 'translate-x-0' : '-translate-x-5' }}"></span>
                                                </span>
                                                {{ $curtain->is_active ? 'مفعّلة' : 'مخفية' }}
                                            </button>
                                        </form>
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-2">
                                            <a class="inline-flex min-h-11 items-center rounded-lg border border-[#E5E1DA] px-3 font-semibold hover:bg-[#F7F6F3]" href="{{ route('admin.curtains.edit', $curtain) }}">تعديل</a>
                                            <form method="POST" action="{{ route('admin.curtains.destroy', $curtain) }}" onsubmit="return confirm('هل أنت متأكد من حذف هذه التشكيلة؟')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="inline-flex min-h-11 items-center rounded-lg px-3 font-semibold text-[#A23D3D] hover:bg-[#F7EAEA]" type="submit">حذف</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="border-t border-[#EEEAE4] px-5 py-4">{{ $curtains->links() }}</div>
            @endif
        </section>
        </div>
    </main>
</body>
</html>
