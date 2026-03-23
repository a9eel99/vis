<?php

namespace Database\Seeders;

use App\Domain\Models\InspectionQuestion;
use App\Domain\Models\InspectionSection;
use App\Domain\Models\InspectionTemplate;
use Illuminate\Database\Seeder;

class TraditionalTemplateSeeder extends Seeder
{
    public function run(): void
    {
        // =============================================
        // Traditional Chassis & Mechanical Inspection
        // فحص تقليدي - شاصي وميكانيك
        // =============================================
        $template = InspectionTemplate::create([
            'name' => 'فحص تقليدي - شاصي وميكانيك',
            'description' => 'نموذج الفحص التقليدي الشامل للشاصي والمحرك والجير والبودي مع التقييم والتثمين',
            'is_active' => true,
            'fuel_type' => null, // works for all
            'created_by' => \App\Domain\Models\User::first()?->id,
        ]);

        // =============================================
        // Section 1: الإضافات الموجودة في السيارة
        // =============================================
        $extras = InspectionSection::create([
            'template_id' => $template->id,
            'name' => 'الإضافات الموجودة في السيارة',
            'description' => 'فحص الإضافات والكماليات المتوفرة',
            'sort_order' => 1,
        ]);

        $extraItems = [
            'جير أوتوماتيك',
            'مكيف',
            'سنترلوك',
            'تحكم ستيرنج (مقود)',
            'شاشة + كاميرا',
            'كراسي مدفأة أو مبردة',
            'خرفة جلد',
            'جنطات',
            'ABS',
            'زجاج كهرباء',
            'فتحة في السقف',
            'مرايا كهربائية',
            'بالونات هواء (Airbags)',
            'ماموري كراسي',
            'بصمة / دخول ذكي',
            'بانوراما',
            'حساسات',
            'اضافات أخرى',
        ];

        foreach ($extraItems as $i => $item) {
            InspectionQuestion::create([
                'section_id' => $extras->id,
                'label' => $item,
                'type' => 'checkbox',
                'weight' => 0,
                'max_score' => 0,
                'is_critical' => false,
                'is_required' => false,
                'sort_order' => $i + 1,
            ]);
        }

        // =============================================
        // Section 2: فحص الشاصي
        // =============================================
        $chassis = InspectionSection::create([
            'template_id' => $template->id,
            'name' => 'فحص الشاصي',
            'description' => 'فحص أجزاء الشاصي الأربعة',
            'sort_order' => 2,
        ]);

        $chassisOptions = [
            ['label' => 'جيد / أصلي', 'score' => 10],
            ['label' => 'قصعات بالراسية', 'score' => 6],
            ['label' => 'قصعات بالراس أثر ضرب', 'score' => 4],
            ['label' => 'ضربة على الراس', 'score' => 2],
            ['label' => 'ضربة على الراسية', 'score' => 2],
            ['label' => 'قصعات بالراس ومشغولة', 'score' => 3],
            ['label' => 'ضربة ومشغولة', 'score' => 1],
            ['label' => 'مبدّل / ملحوم', 'score' => 0],
        ];

        $chassisParts = [
            ['label' => 'الشاصي الأمامي اليمين', 'critical' => true],
            ['label' => 'الشاصي الأمامي الشمال', 'critical' => true],
            ['label' => 'الشاصي الخلفي اليمين', 'critical' => true],
            ['label' => 'الشاصي الخلفي الشمال', 'critical' => true],
        ];

        foreach ($chassisParts as $i => $part) {
            InspectionQuestion::create([
                'section_id' => $chassis->id,
                'label' => $part['label'],
                'type' => 'dropdown',
                'options' => $chassisOptions,
                'weight' => 3.0,
                'max_score' => 10,
                'is_critical' => $part['critical'],
                'is_required' => true,
                'sort_order' => $i + 1,
            ]);
        }

        // Chassis notes
        InspectionQuestion::create([
            'section_id' => $chassis->id,
            'label' => 'ملاحظات الشاصي',
            'type' => 'text',
            'weight' => 0,
            'max_score' => 0,
            'is_required' => false,
            'sort_order' => 5,
        ]);

        // Chassis photos
        InspectionQuestion::create([
            'section_id' => $chassis->id,
            'label' => 'صور الشاصي',
            'type' => 'photo',
            'weight' => 0,
            'max_score' => 0,
            'is_required' => false,
            'sort_order' => 6,
        ]);

        // =============================================
        // Section 3: المحرك والميكانيك
        // =============================================
        $engine = InspectionSection::create([
            'template_id' => $template->id,
            'name' => 'المحرك والميكانيك',
            'description' => 'فحص المحرك والجير والبكس والاكسات',
            'sort_order' => 3,
        ]);

        // المحرك
        $engineOptions = [
            ['label' => 'ممتاز - لا مشاكل', 'score' => 10],
            ['label' => 'جيد', 'score' => 8],
            ['label' => 'خشونة بسيطة', 'score' => 6],
            ['label' => 'خشونة + دخان أزرق', 'score' => 3],
            ['label' => 'خشونة + دخان أزرق + تهريب زيت', 'score' => 1],
            ['label' => 'بحاجة صيانة كاملة', 'score' => 0],
        ];

        InspectionQuestion::create([
            'section_id' => $engine->id,
            'label' => 'المحرك',
            'type' => 'dropdown',
            'options' => $engineOptions,
            'weight' => 3.0,
            'max_score' => 10,
            'is_critical' => true,
            'is_required' => true,
            'sort_order' => 1,
        ]);

        // نسبة خشونة المحرك
        InspectionQuestion::create([
            'section_id' => $engine->id,
            'label' => 'نسبة خشونة المحرك %',
            'type' => 'number',
            'weight' => 0,
            'max_score' => 0,
            'is_required' => false,
            'sort_order' => 2,
        ]);

        // الجير
        $gearOptions = [
            ['label' => 'ممتاز', 'score' => 10],
            ['label' => 'جيد', 'score' => 8],
            ['label' => 'جيد / صيغة بسيطة', 'score' => 5],
            ['label' => 'بحاجة صيانة', 'score' => 2],
            ['label' => 'سيء', 'score' => 0],
        ];

        InspectionQuestion::create([
            'section_id' => $engine->id,
            'label' => 'الجير (ناقل الحركة)',
            'type' => 'dropdown',
            'options' => $gearOptions,
            'weight' => 2.5,
            'max_score' => 10,
            'is_critical' => true,
            'is_required' => true,
            'sort_order' => 3,
        ]);

        // البكس والاكسات
        $axleOptions = [
            ['label' => 'ممتاز', 'score' => 10],
            ['label' => 'جيد', 'score' => 8],
            ['label' => 'بحاجة صيانة', 'score' => 3],
            ['label' => 'سيء', 'score' => 0],
        ];

        InspectionQuestion::create([
            'section_id' => $engine->id,
            'label' => 'البكس والاكسات',
            'type' => 'dropdown',
            'options' => $axleOptions,
            'weight' => 2.0,
            'max_score' => 10,
            'is_critical' => false,
            'is_required' => true,
            'sort_order' => 4,
        ]);

        // تهريب زيت
        InspectionQuestion::create([
            'section_id' => $engine->id,
            'label' => 'تهريب زيت',
            'type' => 'checkbox',
            'weight' => 1.0,
            'max_score' => 10,
            'is_critical' => false,
            'is_required' => false,
            'sort_order' => 5,
        ]);

        // دخان أزرق
        InspectionQuestion::create([
            'section_id' => $engine->id,
            'label' => 'دخان أزرق من العادم',
            'type' => 'checkbox',
            'weight' => 1.0,
            'max_score' => 10,
            'is_critical' => false,
            'is_required' => false,
            'sort_order' => 6,
        ]);

        // ملاحظات المحرك
        InspectionQuestion::create([
            'section_id' => $engine->id,
            'label' => 'ملاحظات المحرك',
            'type' => 'text',
            'weight' => 0,
            'max_score' => 0,
            'is_required' => false,
            'sort_order' => 7,
        ]);

        // صور المحرك
        InspectionQuestion::create([
            'section_id' => $engine->id,
            'label' => 'صور المحرك',
            'type' => 'photo',
            'weight' => 0,
            'max_score' => 0,
            'is_required' => false,
            'sort_order' => 8,
        ]);

        // =============================================
        // Section 4: فحص البودي (الهيكل الخارجي)
        // =============================================
        $body = InspectionSection::create([
            'template_id' => $template->id,
            'name' => 'فحص البودي (الهيكل الخارجي)',
            'description' => 'فحص الضربات والكحتات والبوية والتندو',
            'sort_order' => 4,
        ]);

        $bodyParts = [
            'الصدام الأمامي',
            'الصدام الخلفي',
            'الرفرف الأمامي يمين',
            'الرفرف الأمامي شمال',
            'الرفرف الخلفي يمين',
            'الرفرف الخلفي شمال',
            'الباب الأمامي يمين',
            'الباب الأمامي شمال',
            'الباب الخلفي يمين',
            'الباب الخلفي شمال',
            'الكابوت (غطاء المحرك)',
            'الشنطة (باب الصندوق)',
            'السقف',
        ];

        $bodyOptions = [
            ['label' => 'أصلي / جيد', 'score' => 10],
            ['label' => 'كحتات بسيطة', 'score' => 8],
            ['label' => 'بوية (مصبوغ)', 'score' => 6],
            ['label' => 'غيار لون (تندو)', 'score' => 5],
            ['label' => 'ضربة ومشغولة', 'score' => 3],
            ['label' => 'ضربة ومشغولة مع المرشات', 'score' => 2],
            ['label' => 'مبدّل', 'score' => 1],
            ['label' => 'ضربة قوية', 'score' => 0],
        ];

        foreach ($bodyParts as $i => $part) {
            InspectionQuestion::create([
                'section_id' => $body->id,
                'label' => $part,
                'type' => 'dropdown',
                'options' => $bodyOptions,
                'weight' => 1.0,
                'max_score' => 10,
                'is_critical' => false,
                'is_required' => true,
                'sort_order' => $i + 1,
            ]);
        }

        // ملاحظات البودي
        InspectionQuestion::create([
            'section_id' => $body->id,
            'label' => 'ملاحظات البودي',
            'description' => 'ضربات، كحتات، صدأ، مشاكل أخرى...',
            'type' => 'text',
            'weight' => 0,
            'max_score' => 0,
            'is_required' => false,
            'sort_order' => 14,
        ]);

        // صور البودي
        InspectionQuestion::create([
            'section_id' => $body->id,
            'label' => 'صور البودي',
            'type' => 'photo',
            'weight' => 0,
            'max_score' => 0,
            'is_required' => false,
            'sort_order' => 15,
        ]);

        // =============================================
        // Section 5: القيمة التقديرية
        // =============================================
        $valuation = InspectionSection::create([
            'template_id' => $template->id,
            'name' => 'القيمة التقديرية',
            'description' => 'تقدير قيمة المركبة',
            'sort_order' => 5,
        ]);

        InspectionQuestion::create([
            'section_id' => $valuation->id,
            'label' => 'قيمة الهيكل (دينار)',
            'type' => 'number',
            'weight' => 0,
            'max_score' => 0,
            'is_required' => false,
            'sort_order' => 1,
        ]);

        InspectionQuestion::create([
            'section_id' => $valuation->id,
            'label' => 'قيمة الطبعة (دينار)',
            'type' => 'number',
            'weight' => 0,
            'max_score' => 0,
            'is_required' => false,
            'sort_order' => 2,
        ]);

        InspectionQuestion::create([
            'section_id' => $valuation->id,
            'label' => 'مجموع القيمة التقديرية (دينار)',
            'type' => 'number',
            'weight' => 0,
            'max_score' => 0,
            'is_required' => false,
            'sort_order' => 3,
        ]);

        InspectionQuestion::create([
            'section_id' => $valuation->id,
            'label' => 'ملاحظات التقدير',
            'type' => 'text',
            'weight' => 0,
            'max_score' => 0,
            'is_required' => false,
            'sort_order' => 4,
        ]);

        $this->command->info("✅ Traditional inspection template created with {$template->questions()->count()} questions in 5 sections.");
    }
}
