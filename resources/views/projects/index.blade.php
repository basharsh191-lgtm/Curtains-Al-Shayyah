@extends('layouts.app')

@section('title', 'معرض الأعمال | الشياح')

@section('content')
<section class="page-hero">
    <div class="container">
        <span class="eyebrow">من بيوت دمشق بمحبة</span>
        <h1>تفاصيل تصنع الفرق</h1>
        <p>مساحات حقيقية، وأقمشة اختيرت بعناية لتكمل روح المكان وتمنحه حكاية جديدة.</p>
    </div>
</section>

<section
    class="section"
    x-data="{
        active: null,
        projects: @js($projects->map(fn ($project) => ['title' => $project->title, 'description' => $project->description, 'category' => $project->category, 'image' => $project->image_url])->values()),
        close() { this.active = null },
        move(step) { this.active = (this.active + step + this.projects.length) % this.projects.length }
    }"
    @keydown.window.escape="close()"
    @keydown.window.arrow-right="if (active !== null) move(-1)"
    @keydown.window.arrow-left="if (active !== null) move(1)"
>
    <div class="container">
        <div class="section-topline">
            <div class="section-heading">
                <span class="eyebrow">أعمال من أرض الواقع</span>
                <h2>حكايات نوافذ من دمشق</h2>
                <p>كل صورة بداية حكاية منزل اختار أن يرى التفاصيل بطريقة مختلفة.</p>
            </div>
            <a class="text-link" href="{{ route('appointments.create') }}">اصنع حكايتك معنا <span>←</span></a>
        </div>

        @if($projects->isEmpty())
            <div class="catalog-empty">
                <h3 class="text-lg font-semibold text-[#202A35]">نعمل على توثيق مشاريعنا</h3>
                <p class="mt-2">سيظهر معرض الأعمال هنا عند إضافة المشاريع الجديدة.</p>
            </div>
        @else
            <div class="projects-grid">
                @foreach($projects as $index => $project)
                    <button class="project-card" type="button" @click="active = {{ $index }}" aria-label="عرض {{ $project->title }}">
                        <img src="{{ asset('storage/' . $project->image) }}" alt="{{ $project->title }}{{ $project->category ? ' — '.$project->category : '' }}" loading="lazy">
                        <span class="project-caption">
                            <h3>{{ $project->title }}</h3>
                            @if($project->category)
                                <span>{{ $project->category }}</span>
                            @endif
                        </span>
                    </button>
                @endforeach
            </div>
        @endif
    </div>

    @if($projects->isNotEmpty())
        <div
            class="lightbox"
            x-cloak
            x-show="active !== null"
            x-transition.opacity
            @click.self="close()"
            role="dialog"
            aria-modal="true"
            aria-label="عرض صورة المشروع"
            @keydown.tab.prevent
        >
            <button class="lightbox-close" type="button" @click="close()" aria-label="إغلاق">×</button>
            <button class="lightbox-nav lightbox-prev" type="button" @click="move(-1)" aria-label="الصورة السابقة">→</button>
            <div class="lightbox-content">
                <img :src="projects[active]?.image" :alt="projects[active]?.title">
                <div class="lightbox-caption">
                    <h2 x-text="projects[active]?.title"></h2>
                    <p x-show="projects[active]?.category" x-text="projects[active]?.category"></p>
                    <p x-show="projects[active]?.description" x-text="projects[active]?.description"></p>
                </div>
            </div>
            <button class="lightbox-nav lightbox-next" type="button" @click="move(1)" aria-label="الصورة التالية">←</button>
        </div>
    @endif
</section>
@endsection
