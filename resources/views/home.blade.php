@extends('layouts.app')

@section('title', 'الشياح | معرض الشياح للستائر في دمشق منذ 1988')

@section('content')
{{-- Header --}}
<section class="hero">
    <img class="hero-image" src="https://images.unsplash.com/photo-1616486338812-3dadae4b4ace?auto=format&fit=crop&w=2200&q=88" alt="غرفة معيشة أنيقة بستائر طويلة وضوء طبيعي">
    <div class="hero-content">
        <div class="hero-copy">
            <span class="eyebrow">عراقة تمتد منذ عام 1988</span>
            <h1>أصالة الخبرة تلتقي<br>بفخامة الحاضر</h1>
            <p>ننسج خيوط الأناقة لنحيل نوافذكم لوحات تنطق بالجمال.</p>
            <div class="hero-actions">
                <a class="button button-dark" href="{{ route('products.index') }}">اكتشف تشكيلاتنا <span aria-hidden="true">←</span></a>
                <a class="button button-outline" href="{{ route('appointments.create') }}">احجز زيارة منزلية</a>
            </div>
        </div>
    </div>
    <span class="hero-note">تفاصيل من دمشق، صُنعت لتبقى</span>
</section>

{{-- Cards --}}
@php($categories = [
    ['name' => 'مودرن', 'filter' => 'مودرن', 'sub' => 'خطوط هادئة ومساحات مشرقة', 'image' => 'photo-1600607687939-ce8a6c25118c', 'position' => 'center'],
    ['name' => 'كلاسيكية', 'filter' => 'كلاسيك', 'sub' => 'تفاصيل تحمل الزمن بأناقة', 'image' => 'photo-1616486029423-aaa4789e8c9a', 'position' => 'center'],
    ['name' => 'غرف النوم', 'filter' => 'مودرن', 'sub' => 'خصوصية وراحة كل مساء', 'image' => 'photo-1617104678098-de229db51175', 'position' => 'center'],
    ['name' => 'ستائر التعتيم', 'filter' => 'بلاك أوت', 'sub' => 'هدوء أعمق وضوء أقل', 'image' => 'photo-1617104678098-de229db51175', 'position' => 'center'],
])
<section
    class="section categories"
    x-data="{
        active: null,
        categories: @js(collect($categories)->map(fn ($category) => [
            'name' => $category['name'],
            'image' => 'https://images.unsplash.com/'.$category['image'].'?auto=format&fit=crop&w=2200&q=90',
        ])->values()),
        open(index) {
            if (this.active === null) window.imagePreviewHistory.open();
            this.active = index;
        },
        close() {
            if (this.active !== null && !window.imagePreviewHistory.close()) this.active = null;
        },
        handlePopState() {
            if (this.active !== null && !window.imagePreviewHistory.isOpen()) this.active = null;
        }
    }"
    @keydown.window.escape="close()"
    @popstate.window="handlePopState()"
>
    <div class="container">
        <div class="section-heading">
            <span class="eyebrow">اختيارات مدروسة</span>
            <h2>تشكيلات تناسب ذوقك</h2>
            <p>كل مساحة لها طابعها، وكل حكاية تبدأ باختيار القماش الذي يشبهها.</p>
        </div>
        <div class="category-grid">
            @foreach($categories as $index => $category)
                <button class="category-card" type="button" @click="open({{ $index }})" aria-label="عرض صورة تشكيلات {{ $category['name'] }}">
                    <img src="https://images.unsplash.com/{{ $category['image'] }}?auto=format&fit=crop&w=1000&q=82" alt="ستائر {{ $category['name'] }}" style="object-position: {{ $category['position'] }}">
                    <div class="category-copy">
                        <div>
                            <small>{{ $category['sub'] }}</small>
                            <h3>{{ $category['name'] }}</h3>
                        </div>
                        <span class="round-arrow" aria-hidden="true">←</span>
                    </div>
                </button>
            @endforeach
        </div>
    </div>
    <div
        class="lightbox"
        x-cloak
        x-show="active !== null"
        x-transition.opacity
        @click.self="close()"
        role="dialog"
        aria-modal="true"
        aria-label="معاينة صورة التشكيلة"
    >
        <button class="lightbox-close" type="button" @click="close()" aria-label="إغلاق">×</button>
        <div class="lightbox-content">
            <img :src="categories[active]?.image" :alt="categories[active]?.name">
            <div class="lightbox-caption">
                <h2 x-text="categories[active]?.name"></h2>
            </div>
        </div>
    </div>
</section>

<section class="section" id="story">
    <div class="container story-grid">
        <div class="story-photo">
            <img src="https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?auto=format&fit=crop&w=1100&q=85" alt="تفاصيل أنيقة في منزل دمشقي دافئ">
        </div>
        <div class="story-copy">
            <span class="eyebrow">حكاية عائلية منذ 1988</span>
            <h2>أبو أنس وأولاده<br>خبرة تُطرّزها الأيام</h2>
            <p>منذ عام 1988، يرافق معرض الشياح للستائر أبو أنس وأولاده بيوت دمشق في اختيار الأقمشة والستائر التي تليق بها.</p>
            <p>على مدى عقود، نسجت عائلتنا خبرتها في سوق الستائر وأقمشة المفروشات الدمشقي، وجعلت التفاني العائلي والحرفية عنايةً حقيقية بكل تفصيل؛ من انتقاء القماش وحتى آخر طيّة.</p>
            <div class="story-signature">بمحبة، أبو أنس وأولاده — الشياح</div>
        </div>
    </div>
</section>

<section class="section journey">
    <div class="container">
        <div class="section-heading">
            <span class="eyebrow">خطوتك الأولى سهلة</span>
            <h2>رحلة الاختيار</h2>
            <p>من الإلهام إلى نافذتك، نحن معك في كل تفصيل.</p>
        </div>
        <div class="steps">
            <article class="step">
                <span class="step-number">01 — اكتشف</span>
                <h3>استلهم ما يشبهك</h3>
                <p>تصفّح تشكيلاتنا ومشاريعنا، واجمع الأفكار التي تحبها.</p>
            </article>
            <article class="step">
                <span class="step-number">02 — شاركنا</span>
                <h3>نتعرف على مساحتك</h3>
                <p>نأتي إليك لأخذ القياسات ومناقشة احتياجك وألوان منزلك.</p>
            </article>
            <article class="step">
                <span class="step-number">03 — اختر</span>
                <h3>تكتمل الحكاية</h3>
                <p>ننسّق القماش والتفصيل والتركيب، ونترك لك متعة النتيجة.</p>
            </article>
        </div>
    </div>
</section>

<section class="craft">
    <div class="container craft-grid">
        <div class="craft-intro">
            <span class="eyebrow">لماذا الشياح</span>
            <h2>جمالٌ يليق بك، وعنايةٌ تدوم.</h2>
        </div>
        <article class="craft-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" aria-hidden="true">
                <path d="M12 3 4 7v5c0 5 3.4 8 8 9 4.6-1 8-4 8-9V7l-8-4Z"/>
                <path d="m8.5 12 2.2 2.2 4.8-5"/>
            </svg>
            <h3>خامات منتقاة</h3>
            <p>أقمشة تحمل جمالها مع الاستخدام اليومي.</p>
        </article>
        <article class="craft-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" aria-hidden="true">
                <path d="M4 20h16M6 17V5h12v12M9 8h6M9 11h6M9 14h3"/>
            </svg>
            <h3>تفصيل متقن</h3>
            <p>قياسات دقيقة ولمسات تنهي المشهد كما يجب.</p>
        </article>
        <article class="craft-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" aria-hidden="true">
                <path d="M12 21s-8-4.4-8-11a4.5 4.5 0 0 1 8-2.8A4.5 4.5 0 0 1 20 10c0 6.6-8 11-8 11Z"/>
            </svg>
            <h3>اهتمام شخصي</h3>
            <p>نصغي إليك ونقترح ما يناسب بيتك فعلاً.</p>
        </article>
    </div>
</section>

<section class="contact-band" id="contact">
    <div class="container contact-panel">
        <div class="contact-copy">
            <span class="eyebrow">نلتقي في دمشق</span>
            <h2>زورونا في دمشق</h2>
            <p>يسعدنا لقاؤكم في معرض الشياح للستائر. تفضلوا بزيارة موقعنا أو احجزوا موعداً منزلياً لنساعدكم في اختيار ما يناسب مساحتكم.</p>
            <span class="brand-management">بإدارة السيد أمجد الشياح</span>
            <div class="contact-actions">
                <a
                    class="button button-primary"
                    href="https://maps.app.goo.gl/7YLV4UUtMooTdT5q7"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    فتح في خرائط جوجل
                    <span aria-hidden="true">↗</span>
                </a>
                <a class="button button-secondary" href="{{ route('appointments.create') }}">
                    اطلب زيارة منزلية
                    <span aria-hidden="true">←</span>
                </a>
            </div>
        </div>

        <div class="map-frame">
            <iframe
                src="https://maps.google.com/maps?q=33.486055%2C36.298705&z=16&output=embed"
                title="موقع معرض الشياح للستائر في دمشق"
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"
                allowfullscreen
            ></iframe>
        </div>
    </div>
</section>
@endsection
