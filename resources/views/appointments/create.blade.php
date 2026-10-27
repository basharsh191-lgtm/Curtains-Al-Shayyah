@extends('layouts.app')

@section('title', 'احجز زيارة منزلية | الشياح')

@section('content')
<div
    x-data="{ isSubmitting: false, showConfirmation: @js(session()->has('appointment_confirmation')) }"
    x-init="if (@js(session()->has('appointment_confirmation.booking'))) $dispatch('appointment-booked', @js(session('appointment_confirmation.booking')))"
    @keydown.escape.window="showConfirmation = false"
>
{{-- Header --}}
<section class="page-hero">
    <div class="container">
        <span class="eyebrow">نأتي إليك في دمشق</span>
        <h1>لنقيسها على ذوقك</h1>
        <p>اطلب زيارة منزلية، وسنساعدك في اختيار القماش المناسب وأخذ القياسات بكل عناية.</p>
    </div>
</section>

{{-- Form --}}
<section class="container booking-layout">
    <aside class="booking-aside">
        <span class="eyebrow">موعد على مهل</span>
        <h2>بداية الحكاية<br>من نافذتك.</h2>
        <p>املأ التفاصيل التي تناسبك، وسنتواصل معك لتأكيد الموعد والإجابة عن أي استفسار.</p>
        <div class="booking-points">
            <div class="booking-point">
                <span>01</span>
                معاينة وقياس في منزلك
            </div>
            <div class="booking-point">
                <span>02</span>
                اقتراح أقمشة وألوان مناسبة
            </div>
            <div class="booking-point">
                <span>03</span>
                زيارة مريحة بلا التزام
            </div>
        </div>
    </aside>

    <form
        class="booking-form"
        method="POST"
        action="{{ route('appointments.store') }}"
        @submit="if (isSubmitting) { $event.preventDefault(); } else { isSubmitting = true; }"
    >
        @csrf
        @if(request('model'))
            <p class="success-message">طلب معاينة للموديل: {{ request('model') }}</p>
            <input type="hidden" name="model" value="{{ request('model') }}">
        @endif
        <div class="form-grid">
            <div class="form-field">
                <label for="client_name">الاسم الكامل</label>
                <input class="min-h-12" id="client_name" name="client_name" type="text" autocomplete="name" value="{{ old('client_name', old('name')) }}" placeholder="كيف نناديك؟" required maxlength="100">
                @error('client_name')
                    <span class="field-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-field">
                <label for="phone">رقم الهاتف</label>
                <input class="min-h-12" id="phone" name="phone" type="tel" autocomplete="tel" value="{{ old('phone') }}" placeholder="مثال: 09xx xxx xxx" required>
                @error('phone')
                    <span class="field-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-field">
                <label for="address">المدينة / المنطقة الأساسية</label>
                <select class="min-h-12" id="address" name="address" required>
                    <option value="" disabled {{ old('address', old('district')) ? '' : 'selected' }}>اختر المنطقة</option>
                    @foreach(['أبو رمانة', 'المزة', 'الشعلان', 'المالكي', 'مشروع دمر', 'كفرسوسة', 'الميدان', 'منطقة أخرى'] as $district)
                        <option value="{{ $district }}" @selected(old('address', old('district')) === $district)>{{ $district }}</option>
                    @endforeach
                </select>
                @error('address')
                    <span class="field-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-field full">
                <label for="address_details">تفصيل مكان المنزل <span style="color:var(--muted);font-weight:400">— اختياري</span></label>
                <textarea id="address_details" name="address_details" maxlength="1000" placeholder="الشارع، البناء، الطابق، أو علامة مميزة قريبة من المنزل...">{{ old('address_details') }}</textarea>
                @error('address_details')
                    <span class="field-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-field">
                <label for="preferred_date">التاريخ المناسب للزيارة</label>
                <input class="min-h-12" id="preferred_date" name="preferred_date" type="date" min="{{ now()->format('Y-m-d') }}" value="{{ old('preferred_date', old('date')) }}" required>
                @error('preferred_date')
                    <span class="field-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-field">
                <label for="preferred_time">الفترة المناسبة للزيارة <span style="color:var(--muted);font-weight:400">— اختياري</span></label>
                <select class="min-h-12" id="preferred_time" name="preferred_time">
                    <option value="">لا تفضيل</option>
                    <option value="صباحاً" @selected(old('preferred_time') === 'صباحاً')>صباحاً</option>
                    <option value="مساءً" @selected(old('preferred_time') === 'مساءً')>مساءً</option>
                </select>
                @error('preferred_time')
                    <span class="field-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-field full">
                <label for="notes">أخبرنا أكثر عن مساحتك <span style="color:var(--muted);font-weight:400">— اختياري</span></label>
                <textarea id="notes" name="notes" placeholder="عدد النوافذ، نوع الغرفة، أو أي تفاصيل تحب مشاركتها...">{{ old('notes') }}</textarea>
                @error('notes')
                    <span class="field-error">{{ $message }}</span>
                @enderror
            </div>
        </div>

        {{-- Actions --}}
        <button
            class="button button-dark form-submit"
            type="submit"
            :disabled="isSubmitting"
            :aria-disabled="isSubmitting.toString()"
            :class="{ 'cursor-wait opacity-70': isSubmitting }"
        >
            <span x-show="!isSubmitting">إرسال طلب الزيارة <span aria-hidden="true">←</span></span>
            <span x-cloak x-show="isSubmitting" role="status">جارٍ إرسال الطلب...</span>
        </button>
    </form>
</section>

<template x-if="showConfirmation">
    <div
        class="fixed inset-0 z-[100] flex items-center justify-center overflow-y-auto bg-[#202A35]/60 px-4 py-6 backdrop-blur-sm"
        x-transition.opacity
        @click.self="showConfirmation = false"
        x-init="$nextTick(() => { $refs.confirmation.scrollIntoView({ behavior: 'smooth', block: 'center' }); $refs.confirmation.focus(); })"
    >
        @php
            $confirmation = session('appointment_confirmation', []);
            $confirmationName = $confirmation['client_name'] ?? '';
            $confirmationPhone = $confirmation['phone'] ?? '';
            $confirmationDate = $confirmation['preferred_date'] ?? '';
            $whatsappMessage = 'مرحباً بمعرض الشياح، تم إرسال طلب زيارة منزلية باسم '.$confirmationName
                .'، رقم الهاتف '.$confirmationPhone
                .'، والتاريخ المفضل '.$confirmationDate.'. أود متابعة الطلب.';
        @endphp
        <section
            x-ref="confirmation"
            tabindex="-1"
            role="dialog"
            aria-modal="true"
            aria-labelledby="appointment-confirmation-title"
            class="my-auto w-full max-w-xl rounded-3xl border border-[#D8C19F] bg-[#FCFAF6] p-6 text-center shadow-[0_24px_80px_rgba(20,24,29,0.3)] outline-none sm:p-10"
        >
            <span class="mx-auto inline-flex h-16 w-16 items-center justify-center rounded-full border border-[#E2D1B8] bg-[#F3EBDF] text-3xl text-[#79552F]" aria-hidden="true">✓</span>
            <p class="mt-6 text-xs font-bold tracking-[0.18em] text-[#927047]">معرض الشياح للستائر</p>
            <h2 class="mt-3 text-2xl font-semibold leading-relaxed text-[#202A35] sm:text-3xl" id="appointment-confirmation-title">
                تم استلام طلبكم بنجاح ✨
            </h2>
            <p class="mx-auto mt-4 max-w-lg text-sm leading-8 text-[#5B6470] sm:text-base">
                شكراً لثقتكم بمعرض الشياح. طلب المعاينة المنزلية قيد المراجعة حالياً، وسيقوم فريقنا بالتواصل معكم في أقرب وقت لتأكيد الموعد النهائي.
            </p>

            <dl class="mt-6 grid gap-3 rounded-2xl border border-[#E9E1D5] bg-white/80 p-4 text-right text-sm sm:grid-cols-3">
                <div>
                    <dt class="text-xs text-[#7A838D]">الاسم</dt>
                    <dd class="mt-1 break-words font-semibold text-[#344252]">{{ $confirmationName }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-[#7A838D]">رقم الهاتف</dt>
                    <dd class="mt-1 break-words font-semibold text-[#344252]" dir="ltr">{{ $confirmationPhone }}</dd>
                </div>
                <div>
                    <dt class="text-xs text-[#7A838D]">التاريخ المفضل</dt>
                    <dd class="mt-1 font-semibold text-[#344252]" dir="ltr">{{ $confirmationDate }}</dd>
                </div>
            </dl>

            <div class="mt-7 grid gap-3 sm:grid-cols-2">
                <a
                    class="inline-flex min-h-12 items-center justify-center rounded-xl bg-[#79552F] px-5 py-3 text-sm font-semibold text-white transition hover:bg-[#604326] focus:outline-none focus:ring-2 focus:ring-[#79552F] focus:ring-offset-2"
                    href="https://wa.me/963969227248?text={{ rawurlencode($whatsappMessage) }}"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    متابعة الطلب عبر واتساب
                </a>
                <a
                    class="inline-flex min-h-12 items-center justify-center rounded-xl border border-[#D8C19F] bg-white px-5 py-3 text-sm font-semibold text-[#5B4A35] transition hover:bg-[#F6F0E7] focus:outline-none focus:ring-2 focus:ring-[#79552F] focus:ring-offset-2"
                    href="{{ route('home') }}"
                >
                    العودة للرئيسية
                </a>
            </div>
        </section>
    </div>
</template>
</div>
@endsection
