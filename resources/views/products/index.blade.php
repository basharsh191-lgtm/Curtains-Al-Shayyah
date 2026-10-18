@extends('layouts.app')

@section('title', 'تشكيلات الستائر | الشياح')

@section('content')
<section class="page-hero">
    <div class="container">
        <span class="eyebrow">نسيج من اختيارك</span>
        <h1>تشكيلات الستائر</h1>
        <p>اكتشف خامات وألواناً صُممت لتنسجم مع تفاصيل بيتك، من اللمسة الشفافة إلى التعتيم الكامل.</p>
    </div>
</section>

<section
    class="section catalog"
    x-data="{
        active: null,
        category: @js(in_array(request('category'), $categories, true) ? request('category') : 'الكل'),
        material: @js(in_array(request('material'), $materials, true) ? request('material') : 'الكل'),
        items: @js($filterItems),
        galleryItems: @js($products->map(fn ($product) => [
            'title' => $product->title,
            'description' => $product->description,
            'category' => $product->category,
            'material' => $product->material,
            'image' => $product->image_url,
            'appointment' => route('appointments.create', ['model' => $product->title]),
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
        },
        matches(categoryValue, materialValue) {
            return (this.category === 'الكل' || this.category === categoryValue)
                && (this.material === 'الكل' || this.material === materialValue);
        },
        filteredCount() {
            return this.items.filter(item => this.matches(item.category, item.material)).length;
        }
    }"
    @keydown.window.escape="close()"
    @popstate.window="handlePopState()"
>
    <div class="container">
        <div class="filters">
            <div class="filter-group" aria-label="التصفية حسب التصنيف">
                <button class="filter-chip min-h-11" type="button" @click="category = 'الكل'" :class="{ 'selected': category === 'الكل' }">الكل</button>
                @foreach($categories as $categoryOption)
                    <button class="filter-chip min-h-11" type="button" @click="category = @js($categoryOption)" :class="{ 'selected': category === @js($categoryOption) }">{{ $categoryOption }}</button>
                @endforeach
            </div>
            <div class="filter-group">
                <label class="sr-only" for="material-filter">الخامة</label>
                <select id="material-filter" class="filter-select min-h-11" x-model="material">
                    <option value="الكل">كل الخامات</option>
                    @foreach($materials as $materialOption)
                        <option value="{{ $materialOption }}">{{ $materialOption }}</option>
                    @endforeach
                </select>
            </div>
            <span class="results-count" aria-live="polite"><span x-text="filteredCount()"></span> تشكيلات</span>
        </div>

        <div class="product-grid catalog-grid">
            @foreach($products as $product)
                <article
                    class="product-card"
                    x-show="matches(@js($product->category), @js($product->material))"
                >
                    <button class="product-media" type="button" @click="open({{ $loop->index }})" aria-label="عرض صورة {{ $product->title }}">
                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->title }}" loading="lazy">
                        <span class="product-badge">{{ $product->material }}</span>
                        <span class="product-hover-link">عرض الصورة <span>←</span></span>
                    </button>
                    <div class="product-meta">
                        <div>
                            <h3>{{ $product->title }}</h3>
                            <p>{{ $product->description }}</p>
                            <p class="mt-1 text-xs text-[#7A838D]">{{ $product->opacity_level }}</p>
                        </div>
                        <span class="product-index">{{ $product->category }}</span>
                    </div>
                </article>
            @endforeach
            <div class="catalog-empty" x-show="filteredCount() === 0" x-cloak>
                لا توجد تشكيلات بهذا الاختيار. جرّب تصفية أخرى.
            </div>
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
            <img :src="galleryItems[active]?.image" :alt="galleryItems[active]?.title">
            <div class="lightbox-caption">
                <h2 x-text="galleryItems[active]?.title"></h2>
                <p x-text="[galleryItems[active]?.category, galleryItems[active]?.material].filter(Boolean).join(' — ')"></p>
                <p x-show="galleryItems[active]?.description" x-text="galleryItems[active]?.description"></p>
                <a class="button button-secondary mt-4" :href="galleryItems[active]?.appointment">احجز زيارة منزلية لهذا الموديل</a>
            </div>
        </div>
    </div>
</section>
@endsection
