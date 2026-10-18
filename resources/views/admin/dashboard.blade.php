<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#F7F6F3">
    <meta name="description" content="لوحة إدارة معرض الشياح للستائر">
    <title>لوحة التحكم | الشياح للستائر</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="min-h-screen overflow-x-hidden bg-[#F7F6F3] text-[#202A35] antialiased">
    @php
        $appointmentRows = $appointments ?? collect();
        $pendingAppointments = $stats['pending_appointments'] ?? $appointmentRows->where('status', 'pending')->count();
        $dashboardStats = [
            [
                'label' => 'طلبات الزيارات المنزلية',
                'value' => $stats['appointments'] ?? $appointmentRows->count(),
                'hint' => $pendingAppointments.' طلبات بانتظار المتابعة',
                'icon' => 'calendar',
                'tone' => 'amber',
            ],
            [
                'label' => 'تشكيلات الستائر',
                'value' => $stats['collections'] ?? 0,
                'hint' => 'إجمالي التشكيلات المسجلة',
                'icon' => 'layers',
                'tone' => 'slate',
            ],
            [
                'label' => 'مشاريع معرض الأعمال',
                'value' => $stats['projects'] ?? 0,
                'hint' => 'أعمال موثقة في المعرض',
                'icon' => 'image',
                'tone' => 'green',
            ],
            [
                'label' => 'رسائل واستفسارات معلّقة',
                'value' => $stats['pending_messages'] ?? 0,
                'hint' => 'تحتاج إلى متابعة',
                'icon' => 'message',
                'tone' => 'rose',
            ],
        ];
        $userName = auth()->user()?->name ?? 'أمجد الشياح';
        $userEmail = auth()->user()?->email ?? 'amjad@gmail.com';
    @endphp

    <div
        class="admin-shell min-h-screen"
        x-data="{
            sidebarOpen: false,
            darkMode: localStorage.getItem('shayyah-admin-theme') === 'dark',
            toggleTheme() {
                this.darkMode = !this.darkMode;
                localStorage.setItem('shayyah-admin-theme', this.darkMode ? 'dark' : 'light');
            }
        }"
        :class="{ 'admin-dark': darkMode }"
        @keydown.escape.window="sidebarOpen = false"
    >
        {{-- Mobile drawer backdrop --}}
        <div
            class="fixed inset-0 z-40 bg-[#17212B]/45 backdrop-blur-sm md:hidden"
            x-cloak
            x-show="sidebarOpen"
            x-transition.opacity
            @click="sidebarOpen = false"
            aria-hidden="true"
        ></div>

        {{-- Sidebar --}}
        <aside
            class="fixed inset-y-0 right-0 z-50 flex w-[min(19rem,86vw)] flex-col border-l border-[#E5E1DA] bg-white shadow-xl transition-transform duration-300 md:translate-x-0 md:shadow-none"
            :class="sidebarOpen ? 'translate-x-0' : 'translate-x-full'"
            aria-label="قائمة لوحة التحكم"
        >
            <div class="flex min-h-24 items-center justify-between border-b border-[#EEEAE4] px-5">
                <a class="inline-flex min-h-11 items-center" href="{{ route('home', [], false) }}" aria-label="الشياح للستائر - الموقع الرئيسي">
                    @include('components.brand-logo')
                </a>
                <button
                    class="inline-flex h-11 w-11 items-center justify-center rounded-full border border-[#E5E1DA] text-[#5B6470] transition hover:bg-[#F7F6F3] focus:outline-none focus:ring-2 focus:ring-[#79552F] md:hidden"
                    type="button"
                    @click="sidebarOpen = false"
                    aria-label="إغلاق القائمة"
                >
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path d="m6 6 12 12M18 6 6 18"/>
                    </svg>
                </button>
            </div>

            <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-5 text-sm" aria-label="التنقل الإداري">
                <a
                    class="flex min-h-12 items-center gap-3 rounded-xl bg-[#F3EFE8] px-3 font-semibold text-[#79552F]"
                    href="#overview"
                    @click="sidebarOpen = false"
                    aria-current="page"
                >
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                        <rect x="3" y="3" width="8" height="8" rx="2"/><rect x="13" y="3" width="8" height="5" rx="2"/>
                        <rect x="13" y="10" width="8" height="11" rx="2"/><rect x="3" y="13" width="8" height="8" rx="2"/>
                    </svg>
                    نظرة عامة
                </a>
                <a
                    class="flex min-h-12 items-center gap-3 rounded-xl px-3 text-[#5B6470] transition hover:bg-[#F7F6F3] hover:text-[#202A35]"
                    href="{{ route('admin.appointments.index', [], false) }}"
                    @click="sidebarOpen = false"
                >
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                        <rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 11h18"/>
                    </svg>
                    طلبات الزيارات
                    @if($pendingAppointments > 0)
                        <span class="mr-auto inline-flex min-w-6 items-center justify-center rounded-full bg-[#F6EBD9] px-2 py-0.5 text-xs font-bold text-[#79552F]">
                            {{ $pendingAppointments }}
                        </span>
                    @endif
                </a>
                <a
                    class="flex min-h-12 items-center gap-3 rounded-xl px-3 text-[#5B6470] transition hover:bg-[#F7F6F3] hover:text-[#202A35]"
                    href="{{ route('admin.curtains.index', [], false) }}"
                    @click="sidebarOpen = false"
                >
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                        <path d="m12 3 9 5-9 5-9-5 9-5Z"/><path d="m3 12 9 5 9-5M3 16l9 5 9-5"/>
                    </svg>
                    إدارة التشكيلات
                </a>
                <a
                    class="flex min-h-12 items-center gap-3 rounded-xl px-3 text-[#5B6470] transition hover:bg-[#F7F6F3] hover:text-[#202A35]"
                    href="{{ route('admin.projects.index', [], false) }}"
                    @click="sidebarOpen = false"
                >
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                        <rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/>
                        <path d="m21 15-5-5L5 21"/>
                    </svg>
                    معرض الأعمال
                </a>
                <a
                    class="flex min-h-12 items-center gap-3 rounded-xl px-3 text-[#5B6470] transition hover:bg-[#F7F6F3] hover:text-[#202A35]"
                    href="#quick-actions"
                    @click="sidebarOpen = false"
                >
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                        <path d="M12 5v14M5 12h14"/>
                    </svg>
                    إدارة المحتوى
                </a>
            </nav>

            <div class="border-t border-[#EEEAE4] p-4">
                <a
                    class="flex min-h-12 items-center justify-center gap-2 rounded-xl border border-[#E5E1DA] px-3 text-sm font-semibold text-[#202A35] transition hover:bg-[#F7F6F3] focus:outline-none focus:ring-2 focus:ring-[#79552F]"
                    href="{{ route('home', [], false) }}"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                        <path d="M14 3h7v7M10 14 21 3"/><path d="M19 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h6"/>
                    </svg>
                    فتح الموقع الرئيسي
                </a>
                <form class="mt-2" method="POST" action="{{ route('admin.logout', [], false) }}">
                    @csrf
                    <button
                        class="flex min-h-12 w-full items-center justify-center gap-2 rounded-xl px-3 text-sm font-semibold text-[#A23D3D] transition hover:bg-[#FBF0F0] focus:outline-none focus:ring-2 focus:ring-[#A23D3D]"
                        type="submit"
                    >
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <path d="M10 17l5-5-5-5M15 12H3"/><path d="M12 3h6a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-6"/>
                        </svg>
                        تسجيل الخروج
                    </button>
                </form>
            </div>
        </aside>

        {{-- Main content --}}
        <div class="min-h-screen md:mr-[19rem]">
            {{-- Topbar --}}
            <header class="sticky top-0 z-30 border-b border-[#E5E1DA] bg-[#FFFFFFF2] backdrop-blur-xl">
                <div class="flex min-h-[76px] items-center justify-between gap-3 px-4 sm:px-6 lg:px-9">
                    <div class="flex min-w-0 items-center gap-3">
                        <button
                            class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-full border border-[#E5E1DA] bg-white text-[#202A35] transition hover:bg-[#F7F6F3] focus:outline-none focus:ring-2 focus:ring-[#79552F] md:hidden"
                            type="button"
                            @click="sidebarOpen = true"
                            aria-label="فتح قائمة التنقل"
                            :aria-expanded="sidebarOpen.toString()"
                        >
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>
                        <div class="min-w-0">
                            <p class="truncate text-xs font-medium text-[#7A838D] sm:text-sm">لوحة تحكم صاحب المعرض</p>
                            <h1 class="truncate text-lg font-semibold leading-tight text-[#202A35] sm:text-xl">أهلاً، {{ $userName }}</h1>
                        </div>
                    </div>

                    <div class="flex shrink-0 items-center gap-2 sm:gap-3">
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
                        <a
                            class="hidden min-h-11 items-center gap-2 rounded-full px-3 text-sm font-semibold text-[#5B6470] transition hover:bg-[#F3EFE8] hover:text-[#202A35] sm:inline-flex"
                            href="{{ route('home', [], false) }}"
                            target="_blank"
                            rel="noopener noreferrer"
                        >
                            <span>زيارة الموقع</span>
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path d="M14 3h7v7M10 14 21 3"/><path d="M19 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h6"/>
                            </svg>
                        </a>
                        <a
                            class="relative inline-flex h-11 w-11 items-center justify-center rounded-full border border-[#E5E1DA] bg-white text-[#5B6470] transition hover:bg-[#F7F6F3] focus:outline-none focus:ring-2 focus:ring-[#79552F]"
                            href="#appointments"
                            aria-label="الطلبات بانتظار المتابعة"
                        >
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                                <path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4"/>
                            </svg>
                            @if($pendingAppointments > 0)
                                <span class="absolute right-0 top-0 inline-flex h-4 min-w-4 translate-x-1/4 -translate-y-1/4 items-center justify-center rounded-full bg-[#A23D3D] px-1 text-[10px] font-bold text-white">
                                    {{ $pendingAppointments }}
                                </span>
                            @endif
                        </a>
                        <div class="hidden items-center gap-3 border-r border-[#E5E1DA] pr-3 sm:flex">
                            <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-[#EDE5D9] text-sm font-bold text-[#79552F]" aria-hidden="true">
                                {{ mb_substr($userName, 0, 1) }}
                            </span>
                            <div class="max-w-44">
                                <p class="truncate text-sm font-semibold text-[#202A35]">{{ $userName }}</p>
                                <p class="truncate text-xs text-[#7A838D]">{{ $userEmail }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <main class="mx-auto max-w-[1440px] px-4 py-6 sm:px-6 sm:py-8 lg:px-9" id="overview">
                {{-- Page introduction --}}
                <section class="mb-7 flex flex-col justify-between gap-4 sm:mb-8 sm:flex-row sm:items-end">
                    <div>
                        <span class="inline-flex items-center gap-2 text-xs font-bold tracking-wide text-[#79552F]">
                            <span class="h-px w-6 bg-[#B58A5A]"></span>
                            إدارة معرض الشياح
                        </span>
                        <h2 class="mt-2 text-2xl font-semibold leading-tight text-[#202A35] sm:text-3xl">ملخص المعرض</h2>
                        <p class="mt-2 text-sm text-[#5B6470] sm:text-base">تابع طلبات الزيارة ومحتوى المعرض من مكان واحد.</p>
                    </div>
                    <p class="text-xs font-medium text-[#7A838D] sm:text-sm">
                        {{ now()->translatedFormat('l، j F Y') }}
                    </p>
                </section>

                {{-- Overview cards --}}
                <section class="mb-8 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4" aria-label="إحصائيات المعرض">
                    @foreach($dashboardStats as $stat)
                        @php
                            $toneClasses = [
                                'amber' => 'bg-[#F6EBD9] text-[#79552F]',
                                'slate' => 'bg-[#E9EEF2] text-[#344252]',
                                'green' => 'bg-[#E6F1EA] text-[#28704F]',
                                'rose' => 'bg-[#F7EAEA] text-[#A23D3D]',
                            ];
                        @endphp
                        <article class="rounded-2xl border border-[#E5E1DA] bg-white p-5 shadow-[0_8px_24px_rgba(32,42,53,0.04)] transition hover:-translate-y-0.5 hover:shadow-[0_14px_32px_rgba(32,42,53,0.08)] sm:p-6">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-sm font-medium text-[#5B6470]">{{ $stat['label'] }}</p>
                                    <p class="mt-3 text-3xl font-semibold tracking-tight text-[#202A35]">{{ number_format((int) $stat['value']) }}</p>
                                </div>
                                <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-xl {{ $toneClasses[$stat['tone']] }}">
                                    @if($stat['icon'] === 'calendar')
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 11h18"/></svg>
                                    @elseif($stat['icon'] === 'layers')
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="m12 3 9 5-9 5-9-5 9-5Z"/><path d="m3 12 9 5 9-5M3 16l9 5 9-5"/></svg>
                                    @elseif($stat['icon'] === 'image')
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/></svg>
                                    @else
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M21 11.5a8.4 8.4 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.4 8.4 0 0 1-3.8-.9L3 21l1.9-5.7a8.4 8.4 0 0 1-.9-3.8A8.5 8.5 0 0 1 8.7 3.9a8.4 8.4 0 0 1 3.8-.9h.5a8.5 8.5 0 0 1 8 8v.5Z"/></svg>
                                    @endif
                                </span>
                            </div>
                            <p class="mt-4 border-t border-[#F0EDE8] pt-3 text-xs leading-relaxed text-[#7A838D]">{{ $stat['hint'] }}</p>
                        </article>
                    @endforeach
                </section>

                {{-- Quick management actions --}}
                <section class="mb-8" id="quick-actions" aria-labelledby="quick-actions-title">
                    <div class="mb-4 flex items-end justify-between gap-3">
                        <div>
                            <h2 class="text-lg font-semibold text-[#202A35]" id="quick-actions-title">إجراءات سريعة</h2>
                            <p class="mt-1 text-sm text-[#7A838D]">اختصارات لإدارة محتوى المعرض ومواعيده.</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 gap-3 md:grid-cols-3">
                            <a class="group flex min-h-[88px] items-center gap-4 rounded-2xl border border-[#E5E1DA] bg-white p-4 shadow-sm transition hover:border-[#D7C5AC] hover:shadow-md focus:outline-none focus:ring-2 focus:ring-[#79552F]" href="{{ route('admin.curtains.create', [], false) }}">
                                <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#F3EFE8] text-[#79552F] transition group-hover:bg-[#EDE5D9]">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
                                </span>
                                <span class="min-w-0 flex-1">
                                    <span class="block text-sm font-semibold text-[#202A35]">إضافة تشكيلة ستائر جديدة</span>
                                    <span class="mt-1 block text-xs text-[#7A838D]">أضف موديلًا إلى كتالوج المعرض</span>
                                </span>
                            </a>

                            <a class="group flex min-h-[88px] items-center gap-4 rounded-2xl border border-[#E5E1DA] bg-white p-4 shadow-sm transition hover:border-[#D7C5AC] hover:shadow-md focus:outline-none focus:ring-2 focus:ring-[#79552F]" href="{{ route('admin.projects.create', [], false) }}">
                                <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#E9EEF2] text-[#344252] transition group-hover:bg-[#DCE4EA]">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="m21 15-5-5L5 21"/></svg>
                                </span>
                                <span class="min-w-0 flex-1">
                                    <span class="block text-sm font-semibold text-[#202A35]">إضافة مشروع لمعرض الأعمال</span>
                                    <span class="mt-1 block text-xs text-[#7A838D]">وثّق مشروعاً جديداً وصوره</span>
                                </span>
                            </a>

                        @if(\Illuminate\Support\Facades\Route::has('admin.appointments.schedule.edit'))
                            <a class="group flex min-h-[88px] items-center gap-4 rounded-2xl border border-[#E5E1DA] bg-white p-4 shadow-sm transition hover:border-[#D7C5AC] hover:shadow-md focus:outline-none focus:ring-2 focus:ring-[#79552F]" href="{{ route('admin.appointments.schedule.edit', [], false) }}">
                        @else
                            <div class="flex min-h-[88px] items-center gap-4 rounded-2xl border border-dashed border-[#D8D2C8] bg-white p-4 opacity-80" aria-label="تعديل مواعيد الزيارات - يتطلب إعداد مسار الإدارة">
                        @endif
                                <span class="inline-flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#E6F1EA] text-[#28704F] transition group-hover:bg-[#D7E9DD]">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 11h18M12 14v4m-2-2h4"/></svg>
                                </span>
                                <span class="min-w-0 flex-1">
                                    <span class="block text-sm font-semibold text-[#202A35]">تعديل المواعيد المتاحة للزيارات</span>
                                    <span class="mt-1 block text-xs text-[#7A838D]">حدّث أوقات استقبال طلبات الزيارة</span>
                                </span>
                                @if(!\Illuminate\Support\Facades\Route::has('admin.appointments.schedule.edit'))
                                    <span class="rounded-full bg-[#F7F6F3] px-2.5 py-1 text-[10px] font-semibold text-[#7A838D]">قريباً</span>
                                @endif
                        @if(\Illuminate\Support\Facades\Route::has('admin.appointments.schedule.edit'))
                            </a>
                        @else
                            </div>
                        @endif
                    </div>
                </section>

                {{-- Appointment management --}}
                <section class="overflow-hidden rounded-2xl border border-[#E5E1DA] bg-white shadow-[0_8px_24px_rgba(32,42,53,0.04)]" id="appointments" aria-labelledby="appointments-title">
                    <div class="flex flex-col gap-3 border-b border-[#EEEAE4] px-5 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                        <div>
                            <h2 class="text-lg font-semibold text-[#202A35]" id="appointments-title">إدارة حجز الزيارات المنزلية</h2>
                            <p class="mt-1 text-sm text-[#7A838D]">أحدث الطلبات الواردة من زوار الموقع.</p>
                        </div>
                        <div class="flex flex-wrap items-center gap-3">
                            <a class="inline-flex min-h-11 items-center rounded-lg border border-[#E5E1DA] px-3 text-sm font-semibold text-[#344252] transition hover:bg-[#F7F6F3]" href="{{ route('admin.appointments.index') }}">عرض كل الطلبات</a>
                            <span class="inline-flex w-fit items-center gap-2 rounded-full bg-[#F7F6F3] px-3 py-1.5 text-xs font-semibold text-[#5B6470]">
                                <span class="h-1.5 w-1.5 rounded-full bg-[#B58A5A]"></span>
                                {{ $stats['appointments'] ?? $appointmentRows->count() }} طلب
                            </span>
                        </div>
                    </div>

                    @if(session('success'))
                        <div class="mx-5 mt-5 rounded-xl border border-[#B8D8C5] bg-[#EFF8F2] px-4 py-3 text-sm font-medium text-[#205C40] sm:mx-6" role="status">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mx-5 mt-5 rounded-xl border border-[#E8C5C5] bg-[#FBF0F0] px-4 py-3 text-sm font-medium text-[#873232] sm:mx-6" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if($appointmentRows->isEmpty())
                        <div class="px-6 py-14 text-center">
                            <span class="mx-auto inline-flex h-12 w-12 items-center justify-center rounded-full bg-[#F3EFE8] text-[#79552F]">
                                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
                                    <rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 11h18"/>
                                </svg>
                            </span>
                            <h3 class="mt-4 text-base font-semibold text-[#202A35]">لا توجد طلبات زيارات حالياً</h3>
                            <p class="mt-1 text-sm text-[#7A838D]">ستظهر الطلبات الجديدة هنا عند وصولها.</p>
                        </div>
                    @else
                        {{-- Mobile request cards --}}
                        <div class="divide-y divide-[#EEEAE4] lg:hidden">
                            @foreach($appointmentRows as $appointment)
                                @php
                                    $appointmentStatus = [
                                        'pending' => ['label' => 'بانتظار التواصل', 'class' => 'bg-[#FFF5E4] text-[#79552F]'],
                                        'confirmed' => ['label' => 'تم التأكيد', 'class' => 'bg-[#E9EEF2] text-[#344252]'],
                                        'contacted' => ['label' => 'تم التأكيد', 'class' => 'bg-[#E9EEF2] text-[#344252]'],
                                        'completed' => ['label' => 'مكتمل', 'class' => 'bg-[#E6F1EA] text-[#28704F]'],
                                        'cancelled' => ['label' => 'ملغي', 'class' => 'bg-[#F7EAEA] text-[#A23D3D]'],
                                    ][$appointment->status] ?? ['label' => 'غير محدد', 'class' => 'bg-[#F1F1F1] text-[#5B6470]'];
                                @endphp
                                <article class="space-y-4 p-5">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <h3 class="truncate font-semibold text-[#202A35]">{{ $appointment->client_name }}</h3>
                                            <a class="mt-1 inline-flex min-h-11 items-center text-sm font-medium text-[#79552F] underline-offset-4 hover:underline" href="tel:{{ $appointment->phone }}">
                                                {{ $appointment->phone }}
                                            </a>
                                        </div>
                                        <span class="inline-flex shrink-0 items-center rounded-full px-3 py-1.5 text-xs font-semibold {{ $appointmentStatus['class'] }}">
                                            {{ $appointmentStatus['label'] }}
                                        </span>
                                    </div>
                                    <dl class="grid grid-cols-1 gap-3 text-sm sm:grid-cols-2">
                                        <div>
                                            <dt class="text-xs font-medium text-[#7A838D]">العنوان</dt>
                                            <dd class="mt-1 break-words text-[#344252]">{{ $appointment->address }}</dd>
                                        </div>
                                        <div>
                                            <dt class="text-xs font-medium text-[#7A838D]">التاريخ المفضل</dt>
                                            <dd class="mt-1 text-[#344252]">
                                                {{ $appointment->preferred_date?->translatedFormat('j F Y') ?? 'غير محدد' }}
                                            </dd>
                                        </div>
                                        <div>
                                            <dt class="text-xs font-medium text-[#7A838D]">الفترة المناسبة</dt>
                                            <dd class="mt-1 text-[#344252]">{{ $appointment->preferred_time ?: 'غير محددة' }}</dd>
                                        </div>
                                        <div class="sm:col-span-2">
                                            <dt class="text-xs font-medium text-[#7A838D]">تفصيل مكان المنزل</dt>
                                            <dd class="mt-1 whitespace-pre-line break-words text-[#344252]">{{ $appointment->address_details ?: 'لم تتم إضافة تفاصيل إضافية للموقع.' }}</dd>
                                        </div>
                                    </dl>
                                    @if($appointment->notes)
                                        <p class="rounded-lg bg-[#F7F6F3] p-3 text-sm leading-relaxed text-[#5B6470]">{{ $appointment->notes }}</p>
                                    @endif
                                    <div class="flex flex-wrap gap-2 border-t border-[#F0EDE8] pt-3">
                                        <a class="inline-flex min-h-11 items-center justify-center gap-2 rounded-lg border border-[#E5E1DA] px-4 text-sm font-semibold text-[#344252] transition hover:bg-[#F7F6F3]" href="{{ route('admin.appointments.show', $appointment) }}">
                                            عرض التفاصيل
                                        </a>
                                        <a class="inline-flex min-h-11 items-center justify-center gap-2 rounded-lg bg-[#28704F] px-4 text-sm font-semibold text-white transition hover:bg-[#205C40] focus:outline-none focus:ring-2 focus:ring-[#28704F] focus:ring-offset-2" href="https://wa.me/{{ $appointment->whatsapp_phone }}?text={{ rawurlencode('مرحباً '.$appointment->client_name.'، نتواصل معك بخصوص طلب الزيارة المنزلية.') }}" target="_blank" rel="noopener noreferrer">
                                            تواصل عبر واتساب
                                        </a>
                                        <form method="POST" action="{{ route('admin.appointments.update', $appointment, false) }}" class="flex min-h-11 items-center gap-2">
                                            @csrf
                                            @method('PATCH')
                                            <label class="sr-only" for="appointment-status-mobile-{{ $appointment->id }}">حالة الطلب</label>
                                            <select class="min-h-11 max-w-40 rounded-lg border border-[#E5E1DA] bg-white px-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#79552F]" id="appointment-status-mobile-{{ $appointment->id }}" name="status">
                                                <option value="pending" @selected($appointment->status === 'pending')>بانتظار التواصل</option>
                                                <option value="confirmed" @selected(in_array($appointment->status, ['confirmed', 'contacted'], true))>تم التأكيد</option>
                                                <option value="completed" @selected($appointment->status === 'completed')>مكتمل</option>
                                                <option value="cancelled" @selected($appointment->status === 'cancelled')>ملغي</option>
                                            </select>
                                            <button class="inline-flex min-h-11 items-center justify-center rounded-lg border border-[#E5E1DA] px-3 text-sm font-semibold text-[#344252] transition hover:bg-[#F7F6F3] focus:outline-none focus:ring-2 focus:ring-[#79552F]" type="submit">حفظ</button>
                                        </form>
                                    </div>
                                </article>
                            @endforeach
                        </div>

                        {{-- Desktop appointment table --}}
                        <div class="hidden overflow-x-auto lg:block">
                            <table class="w-full min-w-[880px] border-collapse text-right text-sm">
                                <thead class="bg-[#FAF9F7] text-xs font-semibold text-[#6C7681]">
                                    <tr>
                                        <th class="px-5 py-4 font-semibold" scope="col">العميل</th>
                                        <th class="px-5 py-4 font-semibold" scope="col">العنوان</th>
                                        <th class="px-5 py-4 font-semibold" scope="col">التاريخ المفضل</th>
                                        <th class="px-5 py-4 font-semibold" scope="col">الحالة</th>
                                        <th class="px-5 py-4 font-semibold" scope="col">الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-[#EEEAE4]">
                                    @foreach($appointmentRows as $appointment)
                                        @php
                                            $appointmentStatus = [
                                                'pending' => ['label' => 'بانتظار التواصل', 'class' => 'bg-[#FFF5E4] text-[#79552F]'],
                                                'confirmed' => ['label' => 'تم التأكيد', 'class' => 'bg-[#E9EEF2] text-[#344252]'],
                                                'contacted' => ['label' => 'تم التأكيد', 'class' => 'bg-[#E9EEF2] text-[#344252]'],
                                                'completed' => ['label' => 'مكتمل', 'class' => 'bg-[#E6F1EA] text-[#28704F]'],
                                                'cancelled' => ['label' => 'ملغي', 'class' => 'bg-[#F7EAEA] text-[#A23D3D]'],
                                            ][$appointment->status] ?? ['label' => 'غير محدد', 'class' => 'bg-[#F1F1F1] text-[#5B6470]'];
                                        @endphp
                                        <tr class="align-middle transition hover:bg-[#FCFBF9]">
                                            <td class="px-5 py-4">
                                                <p class="font-semibold text-[#202A35]">{{ $appointment->client_name }}</p>
                                                <a class="mt-1 inline-flex min-h-11 items-center text-xs font-medium text-[#79552F] hover:underline" href="tel:{{ $appointment->phone }}">{{ $appointment->phone }}</a>
                                            </td>
                                            <td class="max-w-56 px-5 py-4">
                                                <span class="line-clamp-2 text-[#5B6470]">{{ $appointment->address }}</span>
                                                @if($appointment->address_details)
                                                    <span class="mt-1 block whitespace-pre-line text-xs leading-relaxed text-[#7A838D]">{{ $appointment->address_details }}</span>
                                                @endif
                                            </td>
                                            <td class="whitespace-nowrap px-5 py-4 text-[#5B6470]">
                                                {{ $appointment->preferred_date?->translatedFormat('j F Y') ?? 'غير محدد' }}
                                                <span class="mt-1 block text-xs text-[#7A838D]">{{ $appointment->preferred_time ?: 'الفترة غير محددة' }}</span>
                                            </td>
                                            <td class="px-5 py-4">
                                                <span class="inline-flex whitespace-nowrap rounded-full px-3 py-1.5 text-xs font-semibold {{ $appointmentStatus['class'] }}">
                                                    {{ $appointmentStatus['label'] }}
                                                </span>
                                            </td>
                                            <td class="px-5 py-4">
                                                <div class="flex min-w-44 flex-wrap items-center gap-2">
                                                    <a class="inline-flex min-h-11 items-center justify-center rounded-lg border border-[#E5E1DA] px-3 text-xs font-semibold text-[#344252] transition hover:bg-[#F7F6F3]" href="{{ route('admin.appointments.show', $appointment) }}">التفاصيل</a>
                                                    <a class="inline-flex min-h-11 items-center justify-center rounded-lg border border-[#E5E1DA] px-3 text-xs font-semibold text-[#344252] transition hover:bg-[#F7F6F3] focus:outline-none focus:ring-2 focus:ring-[#79552F]" href="https://wa.me/{{ $appointment->whatsapp_phone }}?text={{ rawurlencode('مرحباً '.$appointment->client_name.'، نتواصل معك بخصوص طلب الزيارة المنزلية.') }}" target="_blank" rel="noopener noreferrer">
                                                        واتساب
                                                    </a>
                                                    <form method="POST" action="{{ route('admin.appointments.update', $appointment, false) }}" class="flex items-center gap-2">
                                                        @csrf
                                                        @method('PATCH')
                                                        <label class="sr-only" for="appointment-status-{{ $appointment->id }}">حالة الطلب</label>
                                                        <select class="min-h-11 max-w-40 rounded-lg border border-[#E5E1DA] bg-white px-3 text-xs focus:outline-none focus:ring-2 focus:ring-[#79552F]" id="appointment-status-{{ $appointment->id }}" name="status">
                                                            <option value="pending" @selected($appointment->status === 'pending')>بانتظار التواصل</option>
                                                            <option value="confirmed" @selected(in_array($appointment->status, ['confirmed', 'contacted'], true))>تم التأكيد</option>
                                                            <option value="completed" @selected($appointment->status === 'completed')>مكتمل</option>
                                                            <option value="cancelled" @selected($appointment->status === 'cancelled')>ملغي</option>
                                                        </select>
                                                        <button class="inline-flex min-h-11 items-center justify-center rounded-lg bg-[#202A35] px-3 text-xs font-semibold text-white transition hover:bg-[#344252] focus:outline-none focus:ring-2 focus:ring-[#79552F] focus:ring-offset-2" type="submit">حفظ</button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if(method_exists($appointmentRows, 'links'))
                            <div class="border-t border-[#EEEAE4] px-5 py-4 sm:px-6">
                                {{ $appointmentRows->links() }}
                            </div>
                        @endif
                    @endif
                </section>

                {{-- Footer --}}
                <footer class="mt-8 flex flex-col gap-2 pb-4 text-xs text-[#7A838D] sm:flex-row sm:items-center sm:justify-between">
                    <span>الشياح للستائر <span class="mx-1 text-[#B58A5A]">·</span> بإدارة السيد أمجد الشياح</span>
                    <a class="inline-flex min-h-11 items-center font-semibold text-[#79552F] hover:underline" href="{{ route('home', [], false) }}">العودة إلى الموقع الرئيسي</a>
                </footer>
            </main>
        </div>
    </div>
</body>
</html>
