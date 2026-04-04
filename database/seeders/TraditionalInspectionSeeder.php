<?php

namespace Database\Seeders;

use App\Domain\Models\InspectionTemplate;
use App\Domain\Models\InspectionSection;
use App\Domain\Models\InspectionQuestion;
use Illuminate\Database\Seeder;

class TraditionalInspectionSeeder extends Seeder
{
    public function run(): void
    {
        $template = InspectionTemplate::create([
            'name' => 'الفحص التقليدي',
            'description' => 'نموذج تثمين ومعاينة مركبة - فحص شاسي + بودي + محرك + ميكانيك',
            'is_active' => true,
            'version' => 1,
            'created_by' => \App\Domain\Models\User::first()?->id,
        ]);

        $chassisOpts = [
            ['label' => 'جيد',            'score' => 10],
            ['label' => 'ضربة على الراس',  'score' => 4],
            ['label' => 'ضربة على الراسية','score' => 4],
            ['label' => 'قصعات بالراس',   'score' => 6],
            ['label' => 'قصعات بالراسية', 'score' => 6],
            ['label' => 'أثر ضرب',        'score' => 5],
            ['label' => 'مشدود',          'score' => 5],
            ['label' => 'معدول',          'score' => 3],
            ['label' => 'مقصوص',          'score' => 1],
            ['label' => 'ملحوم',          'score' => 1],
            ['label' => 'صدأ',            'score' => 2],
            ['label' => 'غير أصلي',       'score' => 0],
        ];
        $conditionOpts = [
            ['label' => 'ممتاز',     'score' => 10],
            ['label' => 'جيد',       'score' => 8],
            ['label' => 'مقبول',     'score' => 6],
            ['label' => 'ضعيف',      'score' => 4],
            ['label' => 'سيء',       'score' => 2],
            ['label' => 'معطل',      'score' => 0],
            ['label' => 'غير موجود', 'score' => 0],
        ];
        $bodyOpts = [
            ['label' => 'أصلي',     'score' => 10],
            ['label' => 'مشغول',    'score' => 7],
            ['label' => 'غيار لون', 'score' => 6],
            ['label' => 'ضربة',     'score' => 4],
            ['label' => 'كحتات',    'score' => 7],
            ['label' => 'صدأ',      'score' => 3],
            ['label' => 'معجون',    'score' => 5],
            ['label' => 'دهان',     'score' => 6],
        ];
        $yesNoOpts = [
            ['label' => 'يعمل',      'score' => 10],
            ['label' => 'لا يعمل',   'score' => 0],
            ['label' => 'غير موجود', 'score' => 0],
        ];

        // ===== Section 1: فحص الشاسي =====
        $s1 = InspectionSection::create([
            'template_id' => $template->id,
            'name' => 'فحص الشاسي',
            'description' => 'فحص أرضية المركبة والشاسي من جميع الجهات',
            'sort_order' => 0,
        ]);

        $chassisQuestions = [
            ['label' => 'الشاسي الأمامي اليمين', 'is_critical' => true],
            ['label' => 'الشاسي الأمامي الشمال', 'is_critical' => true],
            ['label' => 'الشاسي الخلفي اليمين', 'is_critical' => true],
            ['label' => 'الشاسي الخلفي الشمال', 'is_critical' => true],
            ['label' => 'الصدام الأمامي (الشاسي)', 'is_critical' => false],
            ['label' => 'الصدام الخلفي (الشاسي)', 'is_critical' => false],
            ['label' => 'قاعدة المحرك', 'is_critical' => true],
            ['label' => 'أرضية السيارة', 'is_critical' => true],
        ];

        foreach ($chassisQuestions as $i => $q) {
            InspectionQuestion::create([
                'section_id' => $s1->id,
                'label' => $q['label'],
                'type' => 'dropdown',
                'options' => $chassisOpts,
                'weight' => 1.50,
                'max_score' => 10,
                'is_critical' => $q['is_critical'],
                'is_required' => true,
                'sort_order' => $i,
            ]);
        }

        InspectionQuestion::create([
            'section_id' => $s1->id,
            'label' => 'ملاحظات الشاسي',
            'type' => 'text',
            'weight' => 0,
            'max_score' => 0,
            'is_critical' => false,
            'is_required' => false,
            'sort_order' => count($chassisQuestions),
        ]);

        InspectionQuestion::create([
            'section_id' => $s1->id,
            'label' => 'صورة الشاسي',
            'type' => 'photo',
            'weight' => 0,
            'max_score' => 0,
            'is_critical' => false,
            'is_required' => false,
            'sort_order' => count($chassisQuestions) + 1,
        ]);

        // ===== Section 2: فحص البودي =====
        $s2 = InspectionSection::create([
            'template_id' => $template->id,
            'name' => 'فحص البودي (الهيكل الخارجي)',
            'description' => 'فحص جميع أجزاء الهيكل الخارجي للمركبة',
            'sort_order' => 1,
        ]);

        $bodyParts = [
            'الصدام الأمامي', 'الكبوت (غطاء المحرك)', 'الرفرف الأمامي اليمين', 'الرفرف الأمامي الشمال',
            'الباب الأمامي اليمين', 'الباب الأمامي الشمال', 'الباب الخلفي اليمين', 'الباب الخلفي الشمال',
            'الرفرف الخلفي اليمين', 'الرفرف الخلفي الشمال', 'الصدام الخلفي', 'صندوق (شنطة) الخلف',
            'السقف (التنده)', 'العمود الأمامي (A-Pillar)', 'العمود الأوسط (B-Pillar)', 'العمود الخلفي (C-Pillar)',
        ];

        foreach ($bodyParts as $i => $part) {
            InspectionQuestion::create([
                'section_id' => $s2->id,
                'label' => $part,
                'type' => 'dropdown',
                'options' => $bodyOpts,
                'weight' => 1.00,
                'max_score' => 10,
                'is_critical' => false,
                'is_required' => true,
                'sort_order' => $i,
            ]);
        }

        InspectionQuestion::create([
            'section_id' => $s2->id,
            'label' => 'ملاحظات البودي',
            'description' => 'ضربات، كحتات، صدأ، معجون، دهان... إلخ',
            'type' => 'text',
            'weight' => 0,
            'max_score' => 0,
            'is_critical' => false,
            'is_required' => false,
            'sort_order' => count($bodyParts),
        ]);

        InspectionQuestion::create([
            'section_id' => $s2->id,
            'label' => 'صور البودي',
            'type' => 'photo',
            'weight' => 0,
            'max_score' => 0,
            'is_critical' => false,
            'is_required' => false,
            'sort_order' => count($bodyParts) + 1,
        ]);

        // ===== Section 3: المحرك والميكانيك =====
        $s3 = InspectionSection::create([
            'template_id' => $template->id,
            'name' => 'المحرك والميكانيك',
            'description' => 'فحص المحرك والجير والبكس والأكسات',
            'sort_order' => 2,
        ]);

        $mechQuestions = [
            ['label' => 'المحرك (صوت)', 'critical' => true],
            ['label' => 'المحرك (تسريب زيت)', 'critical' => true],
            ['label' => 'المحرك (تسريب ماء)', 'critical' => true],
            ['label' => 'المحرك (دخان العادم)', 'critical' => false],
            ['label' => 'الجير (ناقل الحركة)', 'critical' => true],
            ['label' => 'البكس / الأكسات', 'critical' => true],
            ['label' => 'الدسك والفتيس', 'critical' => false],
            ['label' => 'طرمبة الماء', 'critical' => false],
            ['label' => 'الرديتر', 'critical' => false],
            ['label' => 'كمبروسر المكيف', 'critical' => false],
            ['label' => 'الدينمو', 'critical' => false],
            ['label' => 'السلف (المارش)', 'critical' => false],
        ];

        foreach ($mechQuestions as $i => $q) {
            InspectionQuestion::create([
                'section_id' => $s3->id,
                'label' => $q['label'],
                'type' => 'dropdown',
                'options' => $conditionOpts,
                'weight' => 1.50,
                'max_score' => 10,
                'is_critical' => $q['critical'],
                'is_required' => true,
                'sort_order' => $i,
            ]);
        }

        InspectionQuestion::create([
            'section_id' => $s3->id,
            'label' => 'ملاحظات الميكانيك',
            'type' => 'text',
            'weight' => 0,
            'max_score' => 0,
            'is_critical' => false,
            'is_required' => false,
            'sort_order' => count($mechQuestions),
        ]);

        // ===== Section 4: الكهرباء والإلكترونيات =====
        $s4 = InspectionSection::create([
            'template_id' => $template->id,
            'name' => 'الكهرباء والإلكترونيات',
            'description' => 'فحص الأنظمة الكهربائية والإلكترونية',
            'sort_order' => 3,
        ]);

        $elecQuestions = [
            'الإضاءة الأمامية', 'الإضاءة الخلفية', 'الإشارات (الغمازات)',
            'اللمبات الداخلية', 'مساحات الزجاج', 'الزجاج الأمامي',
            'الزجاج الخلفي', 'النوافذ الكهربائية', 'المرايا الكهربائية',
            'الشاشة / الراديو', 'المكيف', 'السنتر لوك',
        ];

        foreach ($elecQuestions as $i => $q) {
            InspectionQuestion::create([
                'section_id' => $s4->id,
                'label' => $q,
                'type' => 'dropdown',
                'options' => $yesNoOpts,
                'weight' => 0.75,
                'max_score' => 10,
                'is_critical' => false,
                'is_required' => true,
                'sort_order' => $i,
            ]);
        }

        // ===== Section 5: الإطارات والفرامل =====
        $s5 = InspectionSection::create([
            'template_id' => $template->id,
            'name' => 'الإطارات والفرامل',
            'description' => 'فحص الإطارات والفرامل والعجلات',
            'sort_order' => 4,
        ]);

        $tireOpts = [
            ['label' => 'ممتاز',         'score' => 10],
            ['label' => 'جيد',           'score' => 8],
            ['label' => 'متوسط',         'score' => 6],
            ['label' => 'ضعيف',          'score' => 3],
            ['label' => 'بحاجة تبديل',   'score' => 0],
        ];
        $tireQuestions = [
            'الإطار الأمامي اليمين', 'الإطار الأمامي الشمال',
            'الإطار الخلفي اليمين', 'الإطار الخلفي الشمال',
            'الإطار الاحتياطي',
            'الفرامل الأمامية', 'الفرامل الخلفية',
            'فرامل اليد', 'نظام ABS',
        ];

        foreach ($tireQuestions as $i => $q) {
            InspectionQuestion::create([
                'section_id' => $s5->id,
                'label' => $q,
                'type' => 'dropdown',
                'options' => in_array($q, ['الفرامل الأمامية','الفرامل الخلفية','فرامل اليد','نظام ABS']) ? $conditionOpts : $tireOpts,
                'weight' => 1.00,
                'max_score' => 10,
                'is_critical' => in_array($q, ['الفرامل الأمامية','الفرامل الخلفية']),
                'is_required' => true,
                'sort_order' => $i,
            ]);
        }

        // ===== Section 6: الداخلية =====
        $s6 = InspectionSection::create([
            'template_id' => $template->id,
            'name' => 'الداخلية',
            'description' => 'فحص المقصورة الداخلية والمقاعد',
            'sort_order' => 5,
        ]);

        $interiorQuestions = [
            'الطبلون (لوحة القيادة)', 'المقاعد', 'أحزمة الأمان',
            'المقود (الطارة)', 'فرشة السقف', 'عداد المسافة (الكيلومترات)',
            'أكياس الهواء (Airbags)', 'ملاحظات الداخلية',
        ];

        foreach ($interiorQuestions as $i => $q) {
            $isText = $q === 'ملاحظات الداخلية';
            $isKm = $q === 'عداد المسافة (الكيلومترات)';
            InspectionQuestion::create([
                'section_id' => $s6->id,
                'label' => $q,
                'type' => $isText ? 'text' : ($isKm ? 'number' : 'dropdown'),
                'options' => ($isText || $isKm) ? null : $conditionOpts,
                'weight' => ($isText || $isKm) ? 0 : 0.75,
                'max_score' => ($isText || $isKm) ? 0 : 10,
                'is_critical' => false,
                'is_required' => !$isText,
                'sort_order' => $i,
            ]);
        }

        // ===== Section 7: ملاحظات عامة =====
        $s7 = InspectionSection::create([
            'template_id' => $template->id,
            'name' => 'ملاحظات عامة وتوقيع الفاحص',
            'description' => 'ملاحظات إضافية وتوصيات الفاحص',
            'sort_order' => 6,
        ]);

        InspectionQuestion::create([
            'section_id' => $s7->id,
            'label' => 'ملاحظات عامة',
            'description' => 'أي ملاحظات إضافية على المركبة',
            'type' => 'text',
            'weight' => 0,
            'max_score' => 0,
            'is_critical' => false,
            'is_required' => false,
            'sort_order' => 0,
        ]);

        InspectionQuestion::create([
            'section_id' => $s7->id,
            'label' => 'التوصية النهائية',
            'type' => 'dropdown',
            'options' => [
                ['label' => 'يُنصح بالشراء',              'score' => 10],
                ['label' => 'يُنصح بالشراء مع ملاحظات',   'score' => 7],
                ['label' => 'لا يُنصح بالشراء',            'score' => 0],
                ['label' => 'بحاجة فحص إضافي',             'score' => 5],
            ],
            'weight' => 0,
            'max_score' => 0,
            'is_critical' => false,
            'is_required' => false,
            'sort_order' => 1,
        ]);

        InspectionQuestion::create([
            'section_id' => $s7->id,
            'label' => 'صور إضافية',
            'type' => 'photo',
            'weight' => 0,
            'max_score' => 0,
            'is_critical' => false,
            'is_required' => false,
            'sort_order' => 2,
        ]);

        echo "✅ Template 'الفحص التقليدي' created with 7 sections and " . $template->questions()->count() . " questions.\n";
    }
}