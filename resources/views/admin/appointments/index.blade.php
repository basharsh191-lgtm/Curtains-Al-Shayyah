<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>طلبات الزيارات | الشياح للستائر</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="min-h-screen">
    @php
        $filters = [
            '' => ['label' => 'كل الطلبات', 'count' => $statusCounts->sum()],
            'pending' => ['label' => 'بانتظار التواصل', 'count' => $statusCounts->get('pending', 0)],
            'confirmed' => ['label' => 'تم التأكيد', 'count' => $statusCounts->get('confirmed', 0) + $statusCounts->get('contacted', 0)],
            'completed' => ['label' => 'مكتملة', 'count' => $statusCounts->get('completed', 0)],
            'cancelled' => ['label' => 'ملغاة', 'count' => $statusCounts->get('cancelled', 0)],
        ];
        $statusLabels = [
            'pending' => ['بانتظار التواصل', 'bg-[#FFF5E4] text-[#79552F]'],
            'confirmed' => ['تم التأكيد', 'bg-[#E9EEF2] text-[#344252]'],
            'contacted' => ['تم التأكيد', 'bg-[#E9EEF2] text-[#344252]'],
            'completed' => ['مكتمل', 'bg-[#E6F1EA] text-[#28704F]'],
            'cancelled' => ['ملغي', 'bg-[#F7EAEA] text-[#A23D3D]'],
        ];
    @endphp
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
            <header class="mb-7 flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
                <div>
                    <a class="text-sm font-semibold text-[#79552F] hover:underline" href="{{ route('admin.dashboard') }}">← العودة إلى لوحة التحكم</a>
                    <h1 class="mt-3 text-3xl font-semibold">طلبات الزيارات المنزلية</h1>
                    <p class="mt-2 text-sm text-[#5B6470]">تابع الطلبات، تواصل مع العملاء، وحدّث حالة كل زيارة.</p>
                </div>
                <button
                    class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-[#E5E1DA] bg-white text-[#5B6470] transition hover:bg-[#F7F6F3] focus:outline-none focus:ring-2 focus:ring-[#79552F]"
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
                <div class="mb-5 rounded-xl border border-[#B8D8C5] bg-[#EFF8F2] px-4 py-3 text-sm text-[#205C40]" role="status">{{ session('success') }}</div>
            @endif

            <nav class="mb-5 flex flex-wrap gap-2" aria-label="فلترة الطلبات حسب الحالة">
                @foreach($filters as $filterValue => $filter)
                    <a
                        class="inline-flex min-h-11 items-center gap-2 rounded-full border px-4 text-sm font-semibold transition {{ (string) $selectedStatus === $filterValue ? 'border-[#D7C5AC] bg-[#F3EFE8] text-[#79552F]' : 'border-[#E5E1DA] bg-white text-[#5B6470] hover:bg-[#F7F6F3]' }}"
                        href="{{ route('admin.appointments.index', $filterValue !== '' ? ['status' => $filterValue] : []) }}"
                        @if((string) $selectedStatus === $filterValue) aria-current="page" @endif
                    >
                        {{ $filter['label'] }}
                        <span class="rounded-full bg-[#F7F6F3] px-2 py-0.5 text-xs">{{ $filter['count'] }}</span>
                    </a>
                @endforeach
            </nav>

            <section class="overflow-hidden rounded-2xl border border-[#E5E1DA] bg-white shadow-sm" aria-label="قائمة طلبات الزيارات">
                @if($appointments->isEmpty())
                    <div class="px-6 py-16 text-center text-[#5B6470]">
                        <p class="text-lg font-semibold text-[#202A35]">لا توجد طلبات في هذا التصنيف</p>
                        <p class="mt-2">ستظهر هنا الطلبات المطابقة عند وصولها.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[1000px] text-right text-sm">
                            <thead class="bg-[#FAF9F7] text-[#5B6470]">
                                <tr>
                                    <th class="px-5 py-4 font-semibold">العميل</th>
                                    <th class="px-5 py-4 font-semibold">العنوان والتفاصيل</th>
                                    <th class="px-5 py-4 font-semibold">الموعد المفضّل</th>
                                    <th class="px-5 py-4 font-semibold">الحالة</th>
                                    <th class="px-5 py-4 font-semibold">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#EEEAE4]">
                                @foreach($appointments as $appointment)
                                    @php($status = $statusLabels[$appointment->status] ?? ['غير محدد', 'bg-[#F1F1F1] text-[#5B6470]'])
                                    <tr class="align-top transition hover:bg-[#FCFBF9]">
                                        <td class="px-5 py-4">
                                            <p class="font-semibold text-[#202A35]">{{ $appointment->client_name }}</p>
                                            <a class="mt-1 inline-flex min-h-11 items-center text-xs font-medium text-[#79552F] hover:underline" href="tel:{{ $appointment->phone }}">{{ $appointment->phone }}</a>
                                            <p class="text-xs text-[#7A838D]">وصل {{ $appointment->created_at->translatedFormat('j F Y، H:i') }}</p>
                                        </td>
                                        <td class="max-w-sm px-5 py-4">
                                            <p class="text-[#344252]">{{ $appointment->address }}</p>
                                            @if($appointment->address_details)
                                                <p class="mt-1 whitespace-pre-line text-xs leading-relaxed text-[#5B6470]">{{ $appointment->address_details }}</p>
                                            @endif
                                            @if($appointment->notes)
                                                <p class="mt-2 whitespace-pre-line text-xs leading-relaxed text-[#7A838D]">{{ $appointment->notes }}</p>
                                            @endif
                                        </td>
                                        <td class="whitespace-nowrap px-5 py-4 text-[#5B6470]">
                                            {{ $appointment->preferred_date?->translatedFormat('j F Y') ?? 'غير محدد' }}
                                            <span class="mt-1 block text-xs text-[#7A838D]">{{ $appointment->preferred_time ?: 'الفترة غير محددة' }}</span>
                                        </td>
                                        <td class="px-5 py-4">
                                            <span class="inline-flex whitespace-nowrap rounded-full px-3 py-1.5 text-xs font-semibold {{ $status[1] }}">{{ $status[0] }}</span>
                                        </td>
                                        <td class="px-5 py-4">
                                            <div class="flex min-w-[290px] flex-wrap items-center gap-2">
                                                <a class="inline-flex min-h-11 items-center justify-center rounded-lg border border-[#E5E1DA] px-3 text-xs font-semibold text-[#344252] transition hover:bg-[#F7F6F3]" href="{{ route('admin.appointments.show', $appointment) }}">التفاصيل</a>
                                                <a class="inline-flex min-h-11 items-center justify-center rounded-lg bg-[#28704F] px-3 text-xs font-semibold text-white transition hover:bg-[#205C40]" href="https://wa.me/{{ $appointment->whatsapp_phone }}?text={{ rawurlencode('مرحباً '.$appointment->client_name.'، نتواصل معك بخصوص طلب الزيارة المنزلية.') }}" target="_blank" rel="noopener noreferrer">واتساب</a>
                                                <form method="POST" action="{{ route('admin.appointments.update', $appointment) }}" class="flex items-center gap-2">
                                                    @csrf
                                                    @method('PATCH')
                                                    <label class="sr-only" for="appointment-status-{{ $appointment->id }}">تحديث حالة الطلب</label>
                                                    <select class="min-h-11 max-w-40 rounded-lg border border-[#E5E1DA] bg-white px-3 text-xs" id="appointment-status-{{ $appointment->id }}" name="status">
                                                        <option value="pending" @selected($appointment->status === 'pending')>بانتظار التواصل</option>
                                                        <option value="confirmed" @selected(in_array($appointment->status, ['confirmed', 'contacted'], true))>تم التأكيد</option>
                                                        <option value="completed" @selected($appointment->status === 'completed')>مكتمل</option>
                                                        <option value="cancelled" @selected($appointment->status === 'cancelled')>ملغي</option>
                                                    </select>
                                                    <button class="inline-flex min-h-11 items-center justify-center rounded-lg bg-[#202A35] px-3 text-xs font-semibold text-white hover:bg-[#344252]" type="submit">حفظ</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="border-t border-[#EEEAE4] px-5 py-4">{{ $appointments->links() }}</div>
                @endif
            </section>
        </div>
    </main>
</body>
</html>
