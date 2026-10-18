<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#FFFFFF">
    <meta name="description" content="معرض الشياح للستائر — لصاحبه أبو أنس وأولاده. خبرة دمشقية عريقة منذ عام 1988، ننسج خيوط الأناقة لنحيل نوافذكم لوحات تنطق بالجمال.">
    <title>@yield('title', 'الشياح | أصالة الخبرة منذ 1988')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://images.unsplash.com">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="overflow-x-hidden">
{{-- Header --}}
<header class="site-header" x-data="{ menuOpen: false }">
    <div class="container header-inner">
        <a class="brand" href="{{ route('home') }}" aria-label="الشياح للستائر - الصفحة الرئيسية">
            @include('components.brand-logo')
        </a>
        <nav class="main-nav" aria-label="التنقل الرئيسي">
            <a class="{{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">الرئيسية</a>
            <a class="{{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}">تشكيلات الستائر</a>
            <a class="{{ request()->routeIs('projects.*') ? 'active' : '' }}" href="{{ route('projects.index') }}">معرض الأعمال</a>
            <a href="{{ route('home') }}#story">من نحن</a>
            <a href="{{ route('home') }}#contact">تواصل معنا</a>
        </nav>
        <a class="header-action" href="{{ route('appointments.create') }}">احجز زيارة منزلية <span aria-hidden="true">↙</span></a>
        <button class="menu-toggle min-h-11 min-w-11" type="button" @click="menuOpen = !menuOpen" :aria-expanded="menuOpen.toString()" aria-label="فتح قائمة التنقل">
            <svg width="25" height="25" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 7h18M3 12h18M3 17h18"/></svg>
        </button>
    </div>
    <nav class="mobile-nav max-h-[calc(100dvh-5.5rem)] overflow-y-auto" x-cloak x-show="menuOpen" x-transition aria-label="التنقل عبر الهاتف">
        <a class="flex min-h-11 items-center" href="{{ route('home') }}" @click="menuOpen = false">الرئيسية</a>
        <a class="flex min-h-11 items-center" href="{{ route('products.index') }}" @click="menuOpen = false">تشكيلات الستائر</a>
        <a class="flex min-h-11 items-center" href="{{ route('projects.index') }}" @click="menuOpen = false">معرض الأعمال</a>
        <a class="flex min-h-11 items-center" href="{{ route('home') }}#story" @click="menuOpen = false">من نحن</a>
        <a class="flex min-h-11 items-center" href="{{ route('home') }}#contact" @click="menuOpen = false">تواصل معنا</a>
        <a class="flex min-h-11 items-center" href="{{ route('appointments.create') }}" @click="menuOpen = false">احجز زيارة منزلية</a>
    </nav>
</header>

<main class="main-content min-w-0">
    @yield('content')
</main>

{{-- Mobile primary navigation --}}
<nav
    class="bottom-nav fixed bottom-0 left-0 right-0 z-50 block border-t border-[#E5E1DA] bg-white shadow-[0_-2px_10px_rgba(0,0,0,0.05)] md:hidden"
    aria-label="التنقل السفلي"
>
    <div class="mx-auto grid max-w-lg grid-cols-5">
        <a
            class="bottom-nav-item"
            href="{{ route('home') }}"
            x-data="{ currentHash: window.location.hash }"
            @hashchange.window="currentHash = window.location.hash"
            :class="{ 'active': {{ request()->routeIs('home') ? 'true' : 'false' }} && currentHash === '' }"
            :aria-current="{{ request()->routeIs('home') ? 'true' : 'false' }} && currentHash === '' ? 'page' : null"
        >
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                <path d="m3 10 9-7 9 7v10a1 1 0 0 1-1 1h-5v-7H9v7H4a1 1 0 0 1-1-1V10Z"/>
            </svg>
            <span>الرئيسية</span>
        </a>
        <a
            class="bottom-nav-item {{ request()->routeIs('products.*') ? 'active' : '' }}"
            href="{{ route('products.index') }}"
            @if(request()->routeIs('products.*')) aria-current="page" @endif
        >
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                <path d="M4 4h16M6 4v16m12-16v16M4 20h16M9 8h6m-6 4h6"/>
            </svg>
            <span>التشكيلات</span>
        </a>
        <a
            class="bottom-nav-item {{ request()->routeIs('projects.*') ? 'active' : '' }}"
            href="{{ route('projects.index') }}"
            @if(request()->routeIs('projects.*')) aria-current="page" @endif
        >
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                <rect x="3" y="3" width="18" height="18" rx="2"/>
                <circle cx="8.5" cy="8.5" r="1.5"/>
                <path d="m21 15-5-5L5 21"/>
            </svg>
            <span>أعمالنا</span>
        </a>
        <a
            class="bottom-nav-item {{ request()->routeIs('appointments.*') ? 'active' : '' }}"
            href="{{ route('appointments.create') }}"
            @if(request()->routeIs('appointments.*')) aria-current="page" @endif
        >
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                <rect x="3" y="5" width="18" height="16" rx="2"/>
                <path d="M16 3v4M8 3v4M3 11h18"/>
            </svg>
            <span>احجز زيارة</span>
        </a>
        <a
            class="bottom-nav-item"
            href="{{ route('home') }}#contact"
            x-data="{ currentHash: window.location.hash }"
            @hashchange.window="currentHash = window.location.hash"
            :class="{ 'active': currentHash === '#contact' }"
            :aria-current="currentHash === '#contact' ? 'page' : null"
        >
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                <path d="M21 11.5a8.5 8.5 0 0 1-12.6 7.5L3 20.5l1.4-4.7A8.5 8.5 0 1 1 21 11.5Z"/>
                <path d="M8 11h8m-8 4h5"/>
            </svg>
            <span>تواصل معنا</span>
        </a>
    </div>
</nav>

{{-- Footer --}}
<footer class="site-footer">
    <div class="container">
        <div class="footer-main">
            <div class="footer-brand flex flex-col gap-4">
                <a class="brand mb-3 block" href="{{ route('home') }}" aria-label="الشياح للستائر - الصفحة الرئيسية">
                    @include('components.brand-logo')
                </a>
                <p class="mt-2">من دمشق، ننسج خيوط الأناقة بخبرة عائلية تمتد منذ عام 1988، لنحيل نوافذكم لوحات تنطق بالجمال.</p>
            </div>
            <div>
                <h3 class="footer-heading">اكتشف الشياح</h3>
                <div class="footer-links">
                    <a class="min-h-11" href="{{ route('products.index') }}">تشكيلات الستائر</a>
                    <a class="min-h-11" href="{{ route('projects.index') }}">معرض الأعمال</a>
                    <a class="min-h-11" href="{{ route('home') }}#story">حكايتنا</a>
                    <a class="min-h-11" href="{{ route('appointments.create') }}">زيارة منزلية</a>
                </div>
            </div>
            <div>
                <h3 class="footer-heading">يسعدنا لقاؤكم</h3>
                <div class="footer-links">
                    <span>دمشق، سوريا — الموقع قريباً</span>
                    <a class="min-h-11" href="tel:+963937892622">+963 937 892 622</a>
                    <a class="min-h-11" href="https://wa.me/963937892622?text={{ rawurlencode('مرحباً، أود الاستفسار عن تشكيلات الستائر المتوفرة لديكم') }}" target="_blank" rel="noopener">تواصلوا معنا عبر واتساب</a>
                </div>
            </div>
        </div>
        <div class="footer-bottom flex flex-col md:flex-row items-center justify-between">
            <div class="footer-credit">
                <span>تصميم وتطوير: <a href="https://github.com/basharsh191-lgtm" target="_blank" rel="noopener noreferrer">بشار الشياح</a></span>
                <span><span aria-hidden="true">| </span>للتواصل: <a href="https://wa.me/963969227248" target="_blank" rel="noopener noreferrer">واتساب</a></span>
            </div>
            <span>معرض الشياح للستائر — لصاحبه أبو أنس وأولاده © 1988</span>
            <span>صُنع بحب في دمشق</span>
        </div>
    </div>
</footer>

{{-- Floating action --}}
<a class="whatsapp-float" href="https://wa.me/963937892622?text={{ rawurlencode('مرحباً، أود الاستفسار عن تشكيلات الستائر المتوفرة لديكم') }}" target="_blank" rel="noopener" aria-label="تواصل معنا عبر واتساب">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
        <path d="M20.5 11.6a8.5 8.5 0 0 1-12.6 7.5L3 20.5l1.4-4.7A8.5 8.5 0 1 1 20.5 11.6Z"/>
        <path d="M8.2 8.1c.2-.5.5-.5.8-.5h.4c.2 0 .4.1.5.4l.7 1.7c.1.2 0 .4-.1.6l-.5.6c-.2.2-.2.4 0 .6.4.7 1 1.3 1.7 1.7.2.1.4.1.6-.1l.6-.7c.2-.2.4-.2.7-.1l1.5.7c.3.1.4.3.4.6-.1.8-.5 1.3-1.2 1.6-.6.3-1.3.3-2.1 0-1-.3-2.1-1-3.1-2-1-1-1.7-2.1-2-3.1-.3-.8-.3-1.5 0-2.1.3-.7.8-1.1 1.6-1.2Z"/>
    </svg>
</a>

</body>
</html>
