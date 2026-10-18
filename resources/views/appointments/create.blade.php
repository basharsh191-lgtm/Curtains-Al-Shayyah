@extends('layouts.app')

@section('title', 'احجز زيارة منزلية | الشياح')

@section('content')
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

    <form class="booking-form" method="POST" action="{{ route('appointments.store') }}">
        @csrf
        @if(session('success'))
            <div class="success-message" role="status">{{ session('success') }}</div>
        @endif
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
        <button class="button button-dark form-submit" type="submit">إرسال طلب الزيارة <span>←</span></button>
    </form>
</section>
@endsection
