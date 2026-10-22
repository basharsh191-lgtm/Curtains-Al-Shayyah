<?php

namespace App\Http\Controllers;

use App\Models\Curtain;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShowroomController extends Controller
{
    private function catalogProducts(): array
    {
        return [
            [
                'slug' => 'linen-light',
                'name' => 'نسيم الكتّان',
                'category' => 'مودرن',
                'room' => 'المعيشة',
                'fabric' => 'كتّان طبيعي',
                'style' => 'مودرن',
                'image' => 'photo-1616486338812-3dadae4b4ace',
                'description' => 'انسيابية طبيعية تمنح الضوء مساحة ليرسم تفاصيل المكان.',
                'light' => 'ترشيح متوسط للضوء',
                'colors' => ['عاجي', 'رملي', 'رمادي دافئ'],
                'texture' => 'كتّان أوروبي بملمس غني ونسيج يسمح بمرور الضوء بنعومة.',
            ],
            [
                'slug' => 'royal-velvet',
                'name' => 'مخمل دمشق',
                'category' => 'كلاسيكية',
                'room' => 'المعيشة',
                'fabric' => 'مخمل فاخر',
                'style' => 'كلاسيكي',
                'image' => 'photo-1616486029423-aaa4789e8c9a',
                'description' => 'حضور مخملي عميق، وتفاصيل راقية تليق بالمساحات الواسعة.',
                'light' => 'حجب عالٍ للضوء',
                'colors' => ['زيتي', 'عنابي', 'ذهبي معتّق'],
                'texture' => 'مخمل كثيف بلمعة هادئة وانسدال متوازن.',
            ],
            [
                'slug' => 'quiet-blackout',
                'name' => 'سكون بلاك آوت',
                'category' => 'بلاك آوت',
                'room' => 'غرف النوم',
                'fabric' => 'تعتيم ثلاثي',
                'style' => 'بلاك آوت',
                'image' => 'photo-1617104678098-de229db51175',
                'description' => 'هدوء كامل وخصوصية مدروسة لغرف النوم والمساحات الخاصة.',
                'light' => 'حجب شبه كامل للضوء',
                'colors' => ['كريمي', 'رمادي', 'بني فاتح'],
                'texture' => 'طبقات عازلة ناعمة تساعد على تقليل الضوء والحرارة.',
            ],
            [
                'slug' => 'silk-classic',
                'name' => 'حرير الحكاية',
                'category' => 'كلاسيكية',
                'room' => 'المجالس',
                'fabric' => 'حرير ممزوج',
                'style' => 'كلاسيكي',
                'image' => 'photo-1600210492486-724fe5c67fb0',
                'description' => 'لمسات حريرية ورقّة كلاسيكية تصنع مشهداً لا ينسى.',
                'light' => 'ترشيح ناعم للضوء',
                'colors' => ['لؤلؤي', 'شامبين', 'وردي ترابي'],
                'texture' => 'نسيج حريري ممزوج يحافظ على البريق والرونق.',
            ],
            [
                'slug' => 'soft-bedroom',
                'name' => 'هدوء الصباح',
                'category' => 'غرف النوم',
                'room' => 'غرف النوم',
                'fabric' => 'كتّان مخلوط',
                'style' => 'مودرن',
                'image' => 'photo-1617104678098-de229db51175',
                'description' => 'ألوان هادئة وملمس ناعم لبداية يوم أكثر لطفاً.',
                'light' => 'ترشيح متوسط للضوء',
                'colors' => ['أوف وايت', 'رملي', 'أزرق ضبابي'],
                'texture' => 'نسيج قطني وكتّاني لطيف، سهل العناية ومتين.',
            ],
            [
                'slug' => 'arch-modern',
                'name' => 'قوس المدينة',
                'category' => 'مودرن',
                'room' => 'المعيشة',
                'fabric' => 'شفاف مزدوج',
                'style' => 'مودرن',
                'image' => 'photo-1600607687939-ce8a6c25118c',
                'description' => 'طبقتان من الضوء والخصوصية بخطوط عصرية هادئة.',
                'light' => 'خيارات من الشفاف إلى المتوسط',
                'colors' => ['أبيض دافئ', 'بيج حجري', 'رمادي فاتح'],
                'texture' => 'طبقة شفافة خفيفة مع ستارة جانبية بانسدال أنيق.',
            ],
        ];
    }

    public function home(): View
    {
        return view('home', ['featured' => array_slice($this->catalogProducts(), 0, 4)]);
    }

    public function products(): View
    {
        $products = Curtain::query()
            ->active()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return view('products.index', [
            'products' => $products,
            'filterItems' => $products->map(fn (Curtain $curtain) => [
                'category' => $curtain->category,
                'material' => $curtain->material,
            ])->values(),
            'categories' => Curtain::CATEGORIES,
            'materials' => Curtain::MATERIALS,
        ]);
    }

    public function show(Request $request, string $slug): View
    {
        $product = collect($this->catalogProducts())->firstWhere('slug', $slug);
        abort_if($product === null, 404);

        $request->attributes->set('analytics_product_name', $product['name']);

        return view('products.show', [
            'product' => $product,
            'related' => array_slice(array_filter($this->catalogProducts(), fn ($item) => $item['slug'] !== $slug), 0, 3),
        ]);
    }

    public function projects(): View
    {
        return view('projects.index', [
            'projects' => Project::query()
                ->active()
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get(),
        ]);
    }

    public function createAppointment(): View
    {
        return view('appointments.create');
    }
}
