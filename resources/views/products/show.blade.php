@extends('layouts.app')

@section('title', $product['name'].' | الشياح')

@section('content')
<div class="container">
    <nav class="breadcrumb" aria-label="مسار التنقل">
        <a href="{{ route('home') }}">الرئيسية</a>
        <span aria-hidden="true">／</span>
        <a href="{{ route('products.index') }}">التشكيلات</a>
        <span aria-hidden="true">／</span>
        <span>{{ $product['name'] }}</span>
    </nav>

    {{-- Cards --}}
    <section class="detail-grid" x-data="{ photo: 0, color: 0, photos: [
        'https://images.unsplash.com/{{ $product['image'] }}?auto=format&fit=crop&w=1300&q=88',
        'https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?auto=format&fit=crop&w=1300&q=88',
        'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?auto=format&fit=crop&w=1300&q=88'
    ], colors: @js($product['colors']), shades: ['#e9e2d6', '#c9b69c', '#77736d'] }">
        <div>
            <div class="gallery-main">
                <img :src="photos[photo]" alt="{{ $product['name'] }} — تفاصيل القماش">
            </div>
            <div class="gallery-thumbs">
                <template x-for="(image, index) in photos" :key="image">
                    <button
                        type="button"
                        :class="{ 'active': photo === index }"
                        @click="photo = index"
                        :aria-label="'عرض الصورة ' + (index + 1)"
                    >
                        <img :src="image" alt="">
                    </button>
                </template>
            </div>
        </div>

        <div class="detail-copy">
            <span class="eyebrow">{{ $product['category'] }} — {{ $product['fabric'] }}</span>
            <h1>{{ $product['name'] }}</h1>
            <p class="detail-lead">{{ $product['description'] }}</p>
            <div class="detail-divider"></div>
            <span class="detail-label">الألوان المتوفرة</span>
            <div class="swatches">
                <template x-for="(name, index) in colors" :key="name">
                    <button
                        class="swatch min-h-11 min-w-11"
                        type="button"
                        :style="{ backgroundColor: shades[index % shades.length] }"
                        :class="{ 'active': color === index }"
                        @click="color = index"
                        :aria-label="name"
                        :title="name"
                    ></button>
                </template>
                <span class="swatch-note" x-text="colors[color]"></span>
            </div>
            <div class="detail-divider"></div>
            <div class="spec-list">
                <div class="spec-item">
                    <small>حجب الضوء</small>
                    <strong>{{ $product['light'] }}</strong>
                </div>
                <div class="spec-item">
                    <small>نوع القماش</small>
                    <strong>{{ $product['fabric'] }}</strong>
                </div>
                <div class="spec-item">
                    <small>الملمس</small>
                    <strong>{{ $product['texture'] }}</strong>
                </div>
                <div class="spec-item">
                    <small>العناية</small>
                    <strong>تنظيف لطيف حسب نوع النسيج</strong>
                </div>
            </div>

            {{-- Actions --}}
            <div class="detail-actions">
                <a class="button button-dark" href="{{ route('appointments.create', ['model' => $product['name']]) }}">طلب معاينة لهذا الموديل <span>←</span></a>
                <a class="button button-light" href="https://wa.me/963937892622?text={{ rawurlencode('مرحباً، أود الاستفسار عن موديل '.$product['name']) }}" target="_blank" rel="noopener">استفسار مباشر عبر واتساب <span>↗</span></a>
            </div>
        </div>
    </section>

    {{-- Cards --}}
    <section class="related">
        <div class="section-topline">
            <div class="section-heading">
                <span class="eyebrow">قد يعجبك أيضاً</span>
                <h2>تشكيلات قريبة من ذوقك</h2>
            </div>
            <a class="text-link" href="{{ route('products.index') }}">كل التشكيلات ←</a>
        </div>
        <div class="product-grid">
            @foreach($related as $productItem)
                <article class="product-card">
                    <a class="product-media" href="{{ route('products.show', $productItem['slug']) }}">
                        <img src="https://images.unsplash.com/{{ $productItem['image'] }}?auto=format&fit=crop&w=800&q=80" alt="{{ $productItem['name'] }}">
                        <span class="product-badge">{{ $productItem['fabric'] }}</span>
                    </a>
                    <div class="product-meta">
                        <div>
                            <h3>{{ $productItem['name'] }}</h3>
                            <p>{{ $productItem['description'] }}</p>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </section>
</div>
@endsection
