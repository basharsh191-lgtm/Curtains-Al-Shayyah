<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#F7F6F3">
    <meta name="description" content="تسجيل الدخول إلى لوحة إدارة معرض الشياح للستائر">
    <title>دخول الإدارة | الشياح للستائر</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen overflow-x-hidden bg-[#F7F6F3] text-[#202A35] antialiased">
    <main class="relative flex min-h-screen items-center justify-center overflow-hidden px-4 py-10 sm:px-6">
        <div class="pointer-events-none absolute -right-32 -top-36 h-80 w-80 rounded-full bg-[#EDE5D9] opacity-70 blur-3xl" aria-hidden="true"></div>
        <div class="pointer-events-none absolute -bottom-40 -left-28 h-96 w-96 rounded-full bg-[#E9EEF2] opacity-80 blur-3xl" aria-hidden="true"></div>

        <section class="relative w-full max-w-md rounded-3xl border border-[#E5E1DA] bg-white p-6 shadow-[0_18px_48px_rgba(32,42,53,0.10)] sm:p-9" aria-labelledby="login-title">
            <header class="mb-8 flex flex-col items-center text-center">
                <a class="inline-flex min-h-11 items-center justify-center" href="{{ route('home', [], false) }}" aria-label="العودة إلى الشياح للستائر">
                    @include('components.brand-logo')
                </a>
                <span class="mt-5 inline-flex items-center gap-2 rounded-full border border-[#E5E1DA] bg-[#F7F6F3] px-3 py-1.5 text-xs font-semibold text-[#79552F]">
                <h1 class="mt-6 text-2xl font-semibold tracking-tight text-[#202A35]" id="login-title">تسجيل دخول الإدارة</h1>
                <p class="mt-2 max-w-xs text-sm leading-relaxed text-[#5B6470]">أدخل بيانات حساب الإدارة للوصول إلى لوحة التحكم.</p>
            </header>

            @if(session('status'))
                <div class="mb-5 rounded-xl border border-[#B8D8C5] bg-[#EFF8F2] px-4 py-3 text-sm font-medium text-[#205C40]" role="status">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login.store', [], false) }}" class="space-y-5">
                @csrf

                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-[#344252]" for="email">البريد الإلكتروني</label>
                    <input
                        class="min-h-12 w-full rounded-xl border border-[#D8D2C8] bg-white px-4 text-base text-[#202A35] placeholder:text-[#89919A] transition focus:border-[#79552F] focus:outline-none focus:ring-2 focus:ring-[#79552F]/20"
                        id="email"
                        name="email"
                        type="email"
                        value="{{ old('email') }}"
                        autocomplete="username"
                        inputmode="email"
                        placeholder="name@example.com"
                        required
                        autofocus
                        aria-describedby="@error('email')email-error @enderror"
                    >
                    @error('email')
                        <p class="text-sm font-medium text-[#A23D3D]" id="email-error" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label class="block text-sm font-semibold text-[#344252]" for="password">كلمة المرور</label>
                    <input
                        class="min-h-12 w-full rounded-xl border border-[#D8D2C8] bg-white px-4 text-base text-[#202A35] placeholder:text-[#89919A] transition focus:border-[#79552F] focus:outline-none focus:ring-2 focus:ring-[#79552F]/20"
                        id="password"
                        name="password"
                        type="password"
                        autocomplete="current-password"
                        placeholder="أدخل كلمة المرور"
                        required
                        aria-describedby="@error('password')password-error @enderror"
                    >
                    @error('password')
                        <p class="text-sm font-medium text-[#A23D3D]" id="password-error" role="alert">{{ $message }}</p>
                    @enderror
                </div>

                <label class="flex min-h-11 cursor-pointer items-center gap-3 text-sm text-[#5B6470]">
                    <input class="h-4 w-4 rounded border-[#C7C0B5] text-[#79552F] focus:ring-2 focus:ring-[#79552F]/30" type="checkbox" name="remember" value="1">
                    تذكرني على هذا الجهاز
                </label>

                <button
                    class="inline-flex min-h-12 w-full items-center justify-center rounded-xl bg-[#202A35] px-5 text-sm font-semibold text-white shadow-sm transition hover:bg-[#344252] active:scale-[.99] focus:outline-none focus:ring-2 focus:ring-[#79552F] focus:ring-offset-2"
                    type="submit"
                >
                    دخول لوحة التحكم
                </button>
            </form>

            <footer class="mt-7 border-t border-[#EEEAE4] pt-5 text-center">
                <a class="inline-flex min-h-11 items-center justify-center text-sm font-semibold text-[#79552F] transition hover:text-[#5F4024] hover:underline focus:outline-none focus:ring-2 focus:ring-[#79552F]" href="{{ route('home', [], false) }}">
                    العودة إلى الموقع الرئيسي
                </a>
            </footer>
        </section>
    </main>
</body>
</html>
