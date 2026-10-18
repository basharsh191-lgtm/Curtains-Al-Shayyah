<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>تفاصيل طلب الزيارة | الشياح للستائر</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="min-h-screen">
    @php
        $statusLabels = [
            'pending' => ['بانتظار التواصل', 'bg-[#FFF5E4] text-[#79552F]'],
            'confirmed' => ['تم التأكيد', 'bg-[#E9EEF2] text-[#344252]'],
            'contacted' => ['تم التأكيد', 'bg-[#E9EEF2] text-[#344252]'],
            'completed' => ['مكتمل', 'bg-[#E6F1EA] text-[#28704F]'],
            'cancelled' => ['ملغي', 'bg-[#F7EAEA] text-[#A23D3D]'],
        ];
        $status = $statusLabels[$appointment->status] ?? ['غير محدد', 'bg-[#F1F1F1] text-[#5B6470]'];
    @endphp
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
            <header class="flex items-center justify-between gap-4">
                <a class="text-sm font-semibold text-[#79552F] hover:underline" href="{{ route('admin.appointments.index') }}">← العودة إلى الطلبات</a>
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
            </header>

            @if(session('success'))
                <div class="mt-5 rounded-xl border border-[#B8D8C5] bg-[#EFF8F2] px-4 py-3 text-sm text-[#205C40]" role="status">{{ session('success') }}</div>
            @endif

            <section class="mt-5 overflow-hidden rounded-2xl border border-[#E5E1DA] bg-white shadow-sm">
                <div class="flex flex-col justify-between gap-4 border-b border-[#EEEAE4] p-5 sm:flex-row sm:items-center sm:p-7">
                    <div>
                        <p class="text-sm text-[#7A838D]">تفاصيل طلب الزيارة رقم #{{ $appointment->id }}</p>
                        <h1 class="mt-1 text-2xl font-semibold">{{ $appointment->client_name }}</h1>
                    </div>
                    <span class="inline-flex w-fit whitespace-nowrap rounded-full px-3 py-1.5 text-sm font-semibold {{ $status[1] }}">{{ $status[0] }}</span>
                </div>

                <dl class="grid gap-5 p-5 sm:grid-cols-2 sm:p-7">
                    <div>
                        <dt class="text-xs font-medium text-[#7A838D]">رقم الهاتف / واتساب</dt>
                        <dd class="mt-1 font-semibold text-[#202A35]" dir="ltr">{{ $appointment->phone }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-[#7A838D]">العنوان والمنطقة</dt>
                        <dd class="mt-1 font-semibold text-[#202A35]">{{ $appointment->address }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-xs font-medium text-[#7A838D]">تفصيل مكان المنزل</dt>
                        <dd class="mt-2 min-h-16 whitespace-pre-line rounded-xl bg-[#F7F6F3] p-4 leading-relaxed text-[#344252]">{{ $appointment->address_details ?: 'لم تتم إضافة تفاصيل إضافية للموقع.' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-[#7A838D]">التاريخ المفضّل</dt>
                        <dd class="mt-1 font-semibold text-[#202A35]">{{ $appointment->preferred_date?->translatedFormat('l، j F Y') ?? 'غير محدد' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-[#7A838D]">الفترة المفضّلة</dt>
                        <dd class="mt-1 font-semibold text-[#202A35]">{{ $appointment->preferred_time ?: 'غير محددة' }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-xs font-medium text-[#7A838D]">ملاحظات حول الطلب والستائر</dt>
                        <dd class="mt-2 min-h-20 whitespace-pre-line rounded-xl bg-[#F7F6F3] p-4 leading-relaxed text-[#344252]">{{ $appointment->notes ?: 'لا توجد ملاحظات إضافية.' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-[#7A838D]">وقت استلام الطلب</dt>
                        <dd class="mt-1 font-semibold text-[#202A35]">{{ $appointment->created_at->translatedFormat('l، j F Y — H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-[#7A838D]">آخر تحديث</dt>
                        <dd class="mt-1 font-semibold text-[#202A35]">{{ $appointment->updated_at->translatedFormat('l، j F Y — H:i') }}</dd>
                    </div>
                </dl>

                <div class="flex flex-wrap items-center gap-3 border-t border-[#EEEAE4] p-5 sm:px-7">
                    <a class="inline-flex min-h-11 items-center justify-center rounded-lg bg-[#28704F] px-4 font-semibold text-white hover:bg-[#205C40]" href="https://wa.me/{{ $appointment->whatsapp_phone }}?text={{ rawurlencode('مرحباً '.$appointment->client_name.'، نتواصل معك بخصوص طلب الزيارة المنزلية.') }}" target="_blank" rel="noopener noreferrer">مراسلة عبر واتساب</a>
                    <form method="POST" action="{{ route('admin.appointments.update', $appointment) }}" class="flex flex-wrap items-center gap-2">
                        @csrf
                        @method('PATCH')
                        <label class="sr-only" for="appointment-status">تحديث حالة الطلب</label>
                        <select class="min-h-11 rounded-lg border border-[#E5E1DA] bg-white px-3" id="appointment-status" name="status">
                            <option value="pending" @selected($appointment->status === 'pending')>بانتظار التواصل</option>
                            <option value="confirmed" @selected(in_array($appointment->status, ['confirmed', 'contacted'], true))>تم التأكيد</option>
                            <option value="completed" @selected($appointment->status === 'completed')>مكتمل</option>
                            <option value="cancelled" @selected($appointment->status === 'cancelled')>ملغي</option>
                        </select>
                        <button class="inline-flex min-h-11 items-center justify-center rounded-lg bg-[#202A35] px-4 font-semibold text-white hover:bg-[#344252]" type="submit">حفظ الحالة</button>
                    </form>
                </div>
            </section>
        </div>
    </main>
</body>
</html>
