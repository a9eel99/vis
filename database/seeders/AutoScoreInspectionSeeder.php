<?php

namespace Database\Seeders;

use App\Domain\Enums\InspectionStatus;
use App\Domain\Models\Customer;
use App\Domain\Models\Inspection;
use App\Domain\Models\InspectionQuestion;
use App\Domain\Models\InspectionResult;
use App\Domain\Models\InspectionSection;
use App\Domain\Models\InspectionTemplate;
use App\Domain\Models\User;
use App\Domain\Models\Vehicle;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AutoScoreInspectionSeeder extends Seeder
{
    public function run(): void
    {
        $admin     = User::whereHas('roles', fn($q) => $q->where('name', 'Super Admin'))->first()
                   ?? User::first();
        $inspector = User::whereHas('roles', fn($q) => $q->where('name', 'Inspector'))->first()
                   ?? $admin;

        // ── 1. Create template ─────────────────────────────────────────────
        $template = InspectionTemplate::create([
            'name'        => 'الفحص الشامل PREMIUM',
            'description' => 'فحص شامل 200+ نقطة يغطي الهيكل الخارجي، الشاصي، المحرك، الكهربائيات، التكييف، المكابح، السلامة وفحص الطريق.',
            'is_active'   => true,
            'scoring_mode'=> 'scored',
            'fuel_type'   => null,
            'price'       => 25.00,
            'created_by'  => $admin->id,
        ]);

        // ══════════════════════════════════════════════════════════════════
        // القسم 1: الهيكل الخارجي
        // ══════════════════════════════════════════════════════════════════
        $exterior = InspectionSection::create([
            'template_id' => $template->id,
            'name'        => 'الهيكل الخارجي',
            'description' => 'فحص شامل لجميع أجزاء الهيكل الخارجي باستخدام الفحص النظري المتخصص وقياس سماكة الدهان والذكاء الصناعي.',
            'sort_order'  => 1,
        ]);

        $bodyOptions = [
            ['label' => 'أصلي / جيد', 'score' => 10],
            ['label' => 'كحتات بسيطة', 'score' => 8],
            ['label' => 'بوية (مصبوغ)', 'score' => 6],
            ['label' => 'غيار لون (تندو)', 'score' => 5],
            ['label' => 'ضربة ومشغولة', 'score' => 3],
            ['label' => 'ضربة قوية', 'score' => 1],
            ['label' => 'مبدّل', 'score' => 0],
        ];

        $exteriorParts = [
            ['label' => 'الصدام الأمامي',          'critical' => false, 'w' => 1.0],
            ['label' => 'الصدام الخلفي',           'critical' => false, 'w' => 1.0],
            ['label' => 'الرفرف الأمامي يمين',     'critical' => false, 'w' => 1.0],
            ['label' => 'الرفرف الأمامي شمال',     'critical' => false, 'w' => 1.0],
            ['label' => 'الرفرف الخلفي يمين',      'critical' => false, 'w' => 1.0],
            ['label' => 'الرفرف الخلفي شمال',      'critical' => false, 'w' => 1.0],
            ['label' => 'الباب الأمامي يمين',      'critical' => false, 'w' => 1.5],
            ['label' => 'الباب الأمامي شمال',      'critical' => false, 'w' => 1.5],
            ['label' => 'الباب الخلفي يمين',       'critical' => false, 'w' => 1.5],
            ['label' => 'الباب الخلفي شمال',       'critical' => false, 'w' => 1.5],
            ['label' => 'الكابوت (غطاء المحرك)',   'critical' => false, 'w' => 1.0],
            ['label' => 'السقف',                    'critical' => false, 'w' => 1.5],
            ['label' => 'الشنطة (باب الصندوق)',    'critical' => false, 'w' => 1.0],
            ['label' => 'الزجاج الأمامي',          'critical' => false, 'w' => 1.0],
            ['label' => 'الزجاج الخلفي',           'critical' => false, 'w' => 0.5],
            ['label' => 'المرايا الخارجية',        'critical' => false, 'w' => 0.5],
        ];

        foreach ($exteriorParts as $i => $part) {
            InspectionQuestion::create([
                'section_id'  => $exterior->id,
                'label'       => $part['label'],
                'type'        => 'dropdown',
                'options'     => $bodyOptions,
                'weight'      => $part['w'],
                'max_score'   => 10,
                'is_critical' => $part['critical'],
                'is_required' => true,
                'sort_order'  => $i + 1,
            ]);
        }
        InspectionQuestion::create(['section_id' => $exterior->id, 'label' => 'ملاحظات الهيكل الخارجي', 'type' => 'text', 'weight' => 0, 'max_score' => 0, 'sort_order' => 17]);
        InspectionQuestion::create(['section_id' => $exterior->id, 'label' => 'صور الهيكل الخارجي', 'type' => 'photo', 'weight' => 0, 'max_score' => 0, 'sort_order' => 18]);

        // ══════════════════════════════════════════════════════════════════
        // القسم 2: الشاصي والهيكل
        // ══════════════════════════════════════════════════════════════════
        $chassis = InspectionSection::create([
            'template_id' => $template->id,
            'name'        => 'الشاصي والهيكل',
            'description' => 'فحص الشاصي والهيكل الداخلي بنظام قياس الانحرافات بالليزر ونظام CCM العالمي.',
            'sort_order'  => 2,
        ]);

        $chassisOptions = [
            ['label' => 'جيد / أصلي',              'score' => 10],
            ['label' => 'قصعات بالراسية',          'score' => 7],
            ['label' => 'قصعات بالراس + أثر ضرب', 'score' => 5],
            ['label' => 'ضربة متوسطة لا تؤثر',    'score' => 3],
            ['label' => 'ضربة على الراس',          'score' => 2],
            ['label' => 'ضربة شديدة',              'score' => 1],
            ['label' => 'مبدّل / ملحوم',           'score' => 0],
        ];

        $chassisParts = [
            ['label' => 'الشاصي الأمامي اليمين',   'critical' => true,  'w' => 3.0],
            ['label' => 'الشاصي الأمامي الشمال',   'critical' => true,  'w' => 3.0],
            ['label' => 'الشاصي الخلفي اليمين',    'critical' => true,  'w' => 3.0],
            ['label' => 'الشاصي الخلفي الشمال',    'critical' => true,  'w' => 3.0],
            ['label' => 'الهيكل الأمامي',          'critical' => true,  'w' => 2.0],
            ['label' => 'الهيكل الخلفي',           'critical' => false, 'w' => 2.0],
            ['label' => 'الهيكل السفلي',           'critical' => false, 'w' => 1.5],
            ['label' => 'الهيكل العلوي (السقف)',   'critical' => false, 'w' => 1.0],
            ['label' => 'هيكل الجنب اليمين',       'critical' => false, 'w' => 1.5],
            ['label' => 'هيكل الجنب الشمال',       'critical' => false, 'w' => 1.5],
        ];

        foreach ($chassisParts as $i => $part) {
            InspectionQuestion::create([
                'section_id'  => $chassis->id,
                'label'       => $part['label'],
                'type'        => 'dropdown',
                'options'     => $chassisOptions,
                'weight'      => $part['w'],
                'max_score'   => 10,
                'is_critical' => $part['critical'],
                'is_required' => true,
                'sort_order'  => $i + 1,
            ]);
        }

        // وجود الصدأ
        InspectionQuestion::create([
            'section_id'  => $chassis->id,
            'label'       => 'وجود الصدأ',
            'type'        => 'checkbox',
            'weight'      => 1.0,
            'max_score'   => 10,
            'is_critical' => false,
            'sort_order'  => 11,
        ]);
        InspectionQuestion::create(['section_id' => $chassis->id, 'label' => 'ملاحظات الشاصي', 'type' => 'text', 'weight' => 0, 'max_score' => 0, 'sort_order' => 12]);
        InspectionQuestion::create(['section_id' => $chassis->id, 'label' => 'صور الشاصي', 'type' => 'photo', 'weight' => 0, 'max_score' => 0, 'sort_order' => 13]);

        // ══════════════════════════════════════════════════════════════════
        // القسم 3: المحرك وناقل الحركة
        // ══════════════════════════════════════════════════════════════════
        $engine = InspectionSection::create([
            'template_id' => $template->id,
            'name'        => 'المحرك وناقل الحركة',
            'description' => 'فحص شامل للمحرك والجير والأكسات بتقنية Bosch الألمانية وأجهزة الفحص الإلكتروني.',
            'sort_order'  => 3,
        ]);

        $engineOptions = [
            ['label' => 'ممتاز - لا مشاكل',                    'score' => 10],
            ['label' => 'جيد',                                   'score' => 8],
            ['label' => 'خشونة بسيطة',                          'score' => 5],
            ['label' => 'خشونة + دخان',                         'score' => 3],
            ['label' => 'فك مسبق + إصلاح',                     'score' => 2],
            ['label' => 'كسر / لحام / مشاكل خطيرة',            'score' => 0],
        ];

        InspectionQuestion::create([
            'section_id' => $engine->id, 'label' => 'كفاءة وأداء المحرك',
            'type' => 'dropdown', 'options' => $engineOptions,
            'weight' => 3.0, 'max_score' => 10, 'is_critical' => true, 'is_required' => true, 'sort_order' => 1,
        ]);

        $gearOptions = [
            ['label' => 'ممتاز', 'score' => 10],
            ['label' => 'جيد', 'score' => 8],
            ['label' => 'جيد / صيغة بسيطة', 'score' => 5],
            ['label' => 'بحاجة صيانة', 'score' => 2],
            ['label' => 'سيء', 'score' => 0],
        ];

        InspectionQuestion::create([
            'section_id' => $engine->id, 'label' => 'الجير (ناقل الحركة)',
            'type' => 'dropdown', 'options' => $gearOptions,
            'weight' => 2.5, 'max_score' => 10, 'is_critical' => true, 'is_required' => true, 'sort_order' => 2,
        ]);

        $yesNoOptions = [
            ['label' => 'سليم / لا يوجد', 'score' => 10],
            ['label' => 'يوجد', 'score' => 0],
        ];

        InspectionQuestion::create([
            'section_id' => $engine->id, 'label' => 'تهريب زيت',
            'type' => 'dropdown', 'options' => $yesNoOptions,
            'weight' => 1.5, 'max_score' => 10, 'is_critical' => false, 'sort_order' => 3,
        ]);

        InspectionQuestion::create([
            'section_id' => $engine->id, 'label' => 'دخان من العادم',
            'type' => 'dropdown', 'options' => $yesNoOptions,
            'weight' => 1.5, 'max_score' => 10, 'is_critical' => false, 'sort_order' => 4,
        ]);

        $axleOptions = [
            ['label' => 'ممتاز', 'score' => 10],
            ['label' => 'جيد', 'score' => 8],
            ['label' => 'بحاجة صيانة', 'score' => 3],
            ['label' => 'سيء', 'score' => 0],
        ];

        InspectionQuestion::create([
            'section_id' => $engine->id, 'label' => 'البكس والأكسات',
            'type' => 'dropdown', 'options' => $axleOptions,
            'weight' => 2.0, 'max_score' => 10, 'is_critical' => false, 'sort_order' => 5,
        ]);

        InspectionQuestion::create(['section_id' => $engine->id, 'label' => 'نسبة خشونة المحرك %', 'type' => 'number', 'weight' => 0, 'max_score' => 0, 'sort_order' => 6]);
        InspectionQuestion::create(['section_id' => $engine->id, 'label' => 'اختبار الأعطال بالأجهزة الإلكترونية', 'type' => 'checkbox', 'weight' => 1.0, 'max_score' => 10, 'sort_order' => 7]);
        InspectionQuestion::create(['section_id' => $engine->id, 'label' => 'ملاحظات المحرك', 'type' => 'text', 'weight' => 0, 'max_score' => 0, 'sort_order' => 8]);
        InspectionQuestion::create(['section_id' => $engine->id, 'label' => 'صور المحرك', 'type' => 'photo', 'weight' => 0, 'max_score' => 0, 'sort_order' => 9]);

        // ══════════════════════════════════════════════════════════════════
        // القسم 4: نظام التوجيه والتعليق
        // ══════════════════════════════════════════════════════════════════
        $steering = InspectionSection::create([
            'template_id' => $template->id,
            'name'        => 'نظام التوجيه والتعليق',
            'description' => 'فحص نظام التوجيه والتعليق بأجهزة Bosch ADAS المتخصصة.',
            'sort_order'  => 4,
        ]);

        $condOptions = [
            ['label' => 'ممتاز', 'score' => 10],
            ['label' => 'جيد', 'score' => 8],
            ['label' => 'بحاجة صيانة', 'score' => 3],
            ['label' => 'سيء / ضرب', 'score' => 0],
        ];

        $steeringParts = [
            ['label' => 'الانحراف في الميزان الأمامي', 'w' => 1.5, 'critical' => false],
            ['label' => 'الانحراف في الميزان الخلفي',  'w' => 1.5, 'critical' => false],
            ['label' => 'الصنوبرصات الأمامية',         'w' => 1.5, 'critical' => false],
            ['label' => 'الصنوبرصات الخلفية',          'w' => 1.5, 'critical' => false],
            ['label' => 'الدنجل الأمامي',               'w' => 2.0, 'critical' => false],
            ['label' => 'الدنجل الخلفي',                'w' => 2.0, 'critical' => false],
            ['label' => 'بيل العجلات الأمامية',         'w' => 1.5, 'critical' => false],
            ['label' => 'بيل العجلات الخلفية',          'w' => 1.5, 'critical' => false],
            ['label' => 'الأكسات الأمامية / الخلفية',  'w' => 1.5, 'critical' => false],
            ['label' => 'مجموعة التوجيه (الستيرنج)',   'w' => 2.0, 'critical' => true],
            ['label' => 'قواعد المحرك والجير',         'w' => 1.0, 'critical' => false],
        ];

        foreach ($steeringParts as $i => $p) {
            InspectionQuestion::create([
                'section_id' => $steering->id, 'label' => $p['label'],
                'type' => 'dropdown', 'options' => $condOptions,
                'weight' => $p['w'], 'max_score' => 10,
                'is_critical' => $p['critical'], 'sort_order' => $i + 1,
            ]);
        }
        InspectionQuestion::create(['section_id' => $steering->id, 'label' => 'ملاحظات التوجيه', 'type' => 'text', 'weight' => 0, 'max_score' => 0, 'sort_order' => 12]);
        InspectionQuestion::create(['section_id' => $steering->id, 'label' => 'صور التوجيه', 'type' => 'photo', 'weight' => 0, 'max_score' => 0, 'sort_order' => 13]);

        // ══════════════════════════════════════════════════════════════════
        // القسم 5: المجموعة الكهربائية
        // ══════════════════════════════════════════════════════════════════
        $electrical = InspectionSection::create([
            'template_id' => $template->id,
            'name'        => 'المجموعة الكهربائية',
            'description' => 'فحص جميع الأنظمة الكهربائية بأجهزة Bosch المعتمدة.',
            'sort_order'  => 5,
        ]);

        $elecOptions = [
            ['label' => 'يعمل بشكل ممتاز', 'score' => 10],
            ['label' => 'يعمل / ملاحظة بسيطة', 'score' => 7],
            ['label' => 'يعمل جزئياً', 'score' => 4],
            ['label' => 'لا يعمل', 'score' => 0],
        ];

        $elecParts = [
            'الإضاءة الأمامية (كشافات)',
            'الإضاءة الخلفية',
            'بطارية التشغيل 12 فولت',
            'الشاشة / الراديو الأصلي',
            'السماعات',
            'اختبار الزامور',
            'المساحات الأمامية',
            'المساحة الخلفية',
            'إختبار عمل النوافذ',
            'إختبار عمل فتحة السقف',
            'إختبار عمل الكراسي الكهربائية',
            'إختبار عمل ريموت فتح الأبواب',
            'المرايا الكهربائية',
            'اختبار الديفروست الخلفي',
        ];

        foreach ($elecParts as $i => $part) {
            InspectionQuestion::create([
                'section_id' => $electrical->id, 'label' => $part,
                'type' => 'dropdown', 'options' => $elecOptions,
                'weight' => 0.5, 'max_score' => 10,
                'is_critical' => false, 'sort_order' => $i + 1,
            ]);
        }
        InspectionQuestion::create(['section_id' => $electrical->id, 'label' => 'ملاحظات الكهرباء', 'type' => 'text', 'weight' => 0, 'max_score' => 0, 'sort_order' => 15]);

        // ══════════════════════════════════════════════════════════════════
        // القسم 6: نظام التكييف
        // ══════════════════════════════════════════════════════════════════
        $ac = InspectionSection::create([
            'template_id' => $template->id,
            'name'        => 'نظام التكييف',
            'description' => 'فحص نظام التكييف والتبريد وأنظمة تبريد المحرك.',
            'sort_order'  => 6,
        ]);

        $acParts = [
            ['label' => 'التبريد (AC)',                 'w' => 2.0],
            ['label' => 'التدفئة',                      'w' => 1.0],
            ['label' => 'التحويلات',                    'w' => 0.5],
            ['label' => 'فحص تهريب غاز التكييف',       'w' => 1.5],
            ['label' => 'فحص نظام تبريد المحرك',       'w' => 1.5],
            ['label' => 'فحص تهريب سائل تبريد المحرك', 'w' => 1.5],
            ['label' => 'فحص مراوح تبريد المحرك',      'w' => 1.0],
        ];

        foreach ($acParts as $i => $p) {
            InspectionQuestion::create([
                'section_id' => $ac->id, 'label' => $p['label'],
                'type' => 'dropdown', 'options' => $condOptions,
                'weight' => $p['w'], 'max_score' => 10,
                'is_critical' => false, 'sort_order' => $i + 1,
            ]);
        }
        InspectionQuestion::create(['section_id' => $ac->id, 'label' => 'صور نظام التكييف', 'type' => 'photo', 'weight' => 0, 'max_score' => 0, 'sort_order' => 8]);

        // ══════════════════════════════════════════════════════════════════
        // القسم 7: المكابح والسلامة
        // ══════════════════════════════════════════════════════════════════
        $brakes = InspectionSection::create([
            'template_id' => $template->id,
            'name'        => 'المكابح والسلامة',
            'description' => 'فحص أنظمة السلامة العامة والبريكات والأكياس الهوائية.',
            'sort_order'  => 7,
        ]);

        $brakeOptions = [
            ['label' => 'ممتاز', 'score' => 10],
            ['label' => 'جيد', 'score' => 8],
            ['label' => 'مقبول', 'score' => 5],
            ['label' => 'بحاجة تبديل', 'score' => 2],
            ['label' => 'سيء', 'score' => 0],
        ];

        $brakeParts = [
            ['label' => 'وجود جميع الأكياس الهوائية', 'critical' => true,  'w' => 2.0, 'opts' => $yesNoOptions],
            ['label' => 'بريك / ديسك أمامي يمين',     'critical' => false, 'w' => 2.0, 'opts' => $brakeOptions],
            ['label' => 'بريك / ديسك أمامي شمال',     'critical' => false, 'w' => 2.0, 'opts' => $brakeOptions],
            ['label' => 'بريك / ديسك خلفي يمين',      'critical' => false, 'w' => 1.5, 'opts' => $brakeOptions],
            ['label' => 'بريك / ديسك خلفي شمال',      'critical' => false, 'w' => 1.5, 'opts' => $brakeOptions],
            ['label' => 'نظام مانع الانزلاق (ABS)',    'critical' => true,  'w' => 1.5, 'opts' => $condOptions],
            ['label' => 'حزام الأمان الأمامي يمين',   'critical' => false, 'w' => 0.5, 'opts' => $condOptions],
            ['label' => 'حزام الأمان الأمامي شمال',   'critical' => false, 'w' => 0.5, 'opts' => $condOptions],
            ['label' => 'حزام الأمان الخلفي',          'critical' => false, 'w' => 0.5, 'opts' => $condOptions],
            ['label' => 'عمر الإطارات',                'critical' => false, 'w' => 1.0, 'opts' => [
                ['label' => 'أقل من 3 سنوات', 'score' => 10],
                ['label' => '3-5 سنوات', 'score' => 7],
                ['label' => 'أكثر من 5 سنوات', 'score' => 3],
            ]],
            ['label' => 'عمق الفرزة الأمامية',        'critical' => false, 'w' => 1.0, 'opts' => $condOptions],
            ['label' => 'عمق الفرزة الخلفية',         'critical' => false, 'w' => 1.0, 'opts' => $condOptions],
        ];

        foreach ($brakeParts as $i => $p) {
            InspectionQuestion::create([
                'section_id' => $brakes->id, 'label' => $p['label'],
                'type' => 'dropdown', 'options' => $p['opts'],
                'weight' => $p['w'], 'max_score' => 10,
                'is_critical' => $p['critical'], 'sort_order' => $i + 1,
            ]);
        }
        InspectionQuestion::create(['section_id' => $brakes->id, 'label' => 'ملاحظات المكابح', 'type' => 'text', 'weight' => 0, 'max_score' => 0, 'sort_order' => 13]);
        InspectionQuestion::create(['section_id' => $brakes->id, 'label' => 'صور المكابح', 'type' => 'photo', 'weight' => 0, 'max_score' => 0, 'sort_order' => 14]);

        // ══════════════════════════════════════════════════════════════════
        // القسم 8: فحص الطريق
        // ══════════════════════════════════════════════════════════════════
        $road = InspectionSection::create([
            'template_id' => $template->id,
            'name'        => 'فحص الطريق',
            'description' => 'فحص أداء السيارة على الطريق من قِبل فريق متخصص.',
            'sort_order'  => 8,
        ]);

        $roadItems = [
            ['label' => 'مسير السيارة باستقامة',                  'w' => 1.5],
            ['label' => 'ميزان طارة الستيرنج',                    'w' => 1.5],
            ['label' => 'شعور طبيعي للستيرنج أثناء العمل',       'w' => 1.5],
            ['label' => 'لا يوجد صوت غير طبيعي من نظام التعليق', 'w' => 1.5],
            ['label' => 'أداء المحرك في درجة حرارة التشغيل',     'w' => 1.5],
            ['label' => 'أداء الجير الأوتوماتيكي',               'w' => 2.0],
            ['label' => 'أداء نظام البريك',                       'w' => 2.0],
            ['label' => 'عمل مثبت السرعة',                        'w' => 0.5],
            ['label' => 'لا يوجد اهتزازات من البريك',            'w' => 1.0],
            ['label' => 'عداد مسافة المركبة يعمل بشكل صحيح',     'w' => 0.5],
        ];

        foreach ($roadItems as $i => $item) {
            InspectionQuestion::create([
                'section_id' => $road->id, 'label' => $item['label'],
                'type' => 'dropdown', 'options' => $condOptions,
                'weight' => $item['w'], 'max_score' => 10,
                'is_critical' => false, 'sort_order' => $i + 1,
            ]);
        }
        InspectionQuestion::create(['section_id' => $road->id, 'label' => 'ملاحظات فحص الطريق', 'type' => 'text', 'weight' => 0, 'max_score' => 0, 'sort_order' => 11]);

        // ══════════════════════════════════════════════════════════════════
        // 2. Create Customer + Vehicle
        // ══════════════════════════════════════════════════════════════════
        $customer = Customer::firstOrCreate(
            ['phone' => '0791234567'],
            [
                'name'       => 'نوال ابراهيم سامي الكرادشه',
                'email'      => 'nowal@example.com',
                'created_by' => $admin->id,
            ]
        );

        $vehicle = Vehicle::create([
            'make'          => 'Toyota',
            'model'         => 'RAV4',
            'year'          => 2024,
            'color'         => 'فيراني',
            'vin'           => 'JTMRWRFV2RD246034',
            'license_plate' => '49-33720',
            'mileage'       => 17394,
            'fuel_type'     => 'hybrid',
            'owner_name'    => $customer->name,
            'owner_phone'   => $customer->phone,
            'customer_id'   => $customer->id,
            'created_by'    => $admin->id,
        ]);

        // ══════════════════════════════════════════════════════════════════
        // 3. Create Inspection
        // ══════════════════════════════════════════════════════════════════
        $inspection = Inspection::create([
            'reference_number' => 'INS-' . now()->format('Ymd') . '-ASCORE',
            'vehicle_id'       => $vehicle->id,
            'template_id'      => $template->id,
            'inspector_id'     => $inspector->id,
            'created_by'       => $admin->id,
            'status'           => InspectionStatus::COMPLETED,
            'started_at'       => now()->subDays(2),
            'completed_at'     => now()->subDays(2)->addHours(2),
            'price'            => 25.00,
            'payment_status'   => 'paid',
            'paid_at'          => now()->subDays(2)->addHours(2),
            'share_token'      => hash('sha256', Str::random(40)),
            'notes'            => 'سيارة واردة من الولايات المتحدة الأمريكية. تايتل سالفج. تم الفحص الشامل 200+ نقطة.',
        ]);

        // ══════════════════════════════════════════════════════════════════
        // 4. Create Results — مطابقة لتقرير AutoScore
        // ══════════════════════════════════════════════════════════════════

        // answers per label
        $answers = [
            // ─ الهيكل الخارجي ─
            'الصدام الأمامي'           => ['answer' => 'ضربة متوسطة لا تؤثر',    'score' => 3, 'remark' => 'بحاجة إلى إصلاح الصدام الأمامي', 'critical_fail' => false],
            'الصدام الخلفي'            => ['answer' => 'ضربة متوسطة لا تؤثر',    'score' => 3, 'remark' => 'بحاجة إلى إصلاح الصدام الخلفي', 'critical_fail' => false],
            'الرفرف الأمامي يمين'      => ['answer' => 'أصلي / جيد',             'score' => 10],
            'الرفرف الأمامي شمال'      => ['answer' => 'أصلي / جيد',             'score' => 10],
            'الرفرف الخلفي يمين'       => ['answer' => 'كحتات بسيطة',           'score' => 8, 'remark' => 'يوجد خدوش بالجناح الخلفي اليمين'],
            'الرفرف الخلفي شمال'       => ['answer' => 'أصلي / جيد',             'score' => 10],
            'الباب الأمامي يمين'       => ['answer' => 'بوية (مصبوغ)',           'score' => 6, 'remark' => 'بحاجة لإصلاح يد الباب الأمامي اليمين'],
            'الباب الأمامي شمال'       => ['answer' => 'ضربة ومشغولة',           'score' => 3, 'remark' => 'بحاجة إلى إصلاح الباب الأمامي اليسار'],
            'الباب الخلفي يمين'        => ['answer' => 'ضربة ومشغولة',           'score' => 3, 'remark' => 'بحاجة إلى إصلاح الباب الخلفي اليمين'],
            'الباب الخلفي شمال'        => ['answer' => 'أصلي / جيد',             'score' => 10],
            'الكابوت (غطاء المحرك)'    => ['answer' => 'بوية (مصبوغ)',           'score' => 6, 'remark' => 'بحاجة إلى إصلاح غطاء المحرك'],
            'السقف'                    => ['answer' => 'كحتات بسيطة',           'score' => 8],
            'الشنطة (باب الصندوق)'     => ['answer' => 'أصلي / جيد',             'score' => 10],
            'الزجاج الأمامي'           => ['answer' => 'أصلي / جيد',             'score' => 10],
            'الزجاج الخلفي'            => ['answer' => 'أصلي / جيد',             'score' => 10],
            'المرايا الخارجية'         => ['answer' => 'أصلي / جيد',             'score' => 10],

            // ─ الشاصي ─
            'الشاصي الأمامي اليمين'    => ['answer' => 'ضربة متوسطة لا تؤثر',   'score' => 3, 'remark' => 'ضربة متوسطة في الشاصي الأمامي اليمين غير مصلحة لا تؤثر على الأداء'],
            'الشاصي الأمامي الشمال'    => ['answer' => 'جيد / أصلي',            'score' => 10],
            'الشاصي الخلفي اليمين'     => ['answer' => 'جيد / أصلي',            'score' => 10],
            'الشاصي الخلفي الشمال'     => ['answer' => 'جيد / أصلي',            'score' => 10],
            'الهيكل الأمامي'           => ['answer' => 'قصعات بالراس + أثر ضرب', 'score' => 5, 'remark' => 'ضربة في مقدمة المركبة مع الهيكل الرئيسي من الجهة اليمين', 'critical_fail' => false],
            'الهيكل الخلفي'            => ['answer' => 'قصعات بالراسية',        'score' => 7, 'remark' => 'ضربة في مؤخرة المركبة مع الجناح الخلفي اليمين'],
            'الهيكل السفلي'            => ['answer' => 'جيد / أصلي',            'score' => 10],
            'الهيكل العلوي (السقف)'    => ['answer' => 'قصعات بالراسية',        'score' => 7, 'remark' => 'ضربة خفيفة في سقف المركبة'],
            'هيكل الجنب اليمين'        => ['answer' => 'قصعات بالراس + أثر ضرب', 'score' => 5, 'remark' => 'ضربة في الهيكل الرئيسي للباب الأمامي اليمين من الأمام'],
            'هيكل الجنب الشمال'        => ['answer' => 'جيد / أصلي',            'score' => 10],
            'وجود الصدأ'               => ['answer' => '0', 'score' => 10],

            // ─ المحرك ─
            'كفاءة وأداء المحرك'        => ['answer' => 'فك مسبق + إصلاح',       'score' => 2, 'remark' => 'يوجد فك مسبق وإصلاح في المحرك - ضرب في برميل الاكزوزت الخلفي - كسر ولحام في المحرك', 'critical_fail' => false],
            'الجير (ناقل الحركة)'       => ['answer' => 'ممتاز',                 'score' => 10],
            'تهريب زيت'                => ['answer' => 'سليم / لا يوجد',         'score' => 10],
            'دخان من العادم'            => ['answer' => 'سليم / لا يوجد',         'score' => 10],
            'البكس والأكسات'            => ['answer' => 'ممتاز',                 'score' => 10],
            'اختبار الأعطال بالأجهزة الإلكترونية' => ['answer' => '1', 'score' => 10],

            // ─ التوجيه ─
            'الانحراف في الميزان الأمامي' => ['answer' => 'ممتاز', 'score' => 10],
            'الانحراف في الميزان الخلفي'  => ['answer' => 'ممتاز', 'score' => 10],
            'الصنوبرصات الأمامية'         => ['answer' => 'ممتاز', 'score' => 10],
            'الصنوبرصات الخلفية'          => ['answer' => 'ممتاز', 'score' => 10],
            'الدنجل الأمامي'              => ['answer' => 'بحاجة صيانة', 'score' => 3, 'remark' => 'يوجد ضرب في الدنجل الأمامي'],
            'الدنجل الخلفي'               => ['answer' => 'ممتاز', 'score' => 10],
            'بيل العجلات الأمامية'        => ['answer' => 'ممتاز', 'score' => 10],
            'بيل العجلات الخلفية'         => ['answer' => 'ممتاز', 'score' => 10],
            'الأكسات الأمامية / الخلفية'  => ['answer' => 'ممتاز', 'score' => 10],
            'مجموعة التوجيه (الستيرنج)'   => ['answer' => 'بحاجة صيانة', 'score' => 3, 'remark' => 'بحاجة إلى تبديل كفات أمامية وإصلاح مجموعة الستيرنج', 'critical_fail' => false],
            'قواعد المحرك والجير'         => ['answer' => 'ممتاز', 'score' => 10],

            // ─ الكهرباء ─
            'الإضاءة الأمامية (كشافات)'  => ['answer' => 'يعمل / ملاحظة بسيطة', 'score' => 7, 'remark' => 'الكشافات غير أصلية'],
            'الإضاءة الخلفية'            => ['answer' => 'يعمل بشكل ممتاز', 'score' => 10],
            'بطارية التشغيل 12 فولت'      => ['answer' => 'يعمل بشكل ممتاز', 'score' => 10],
            'الشاشة / الراديو الأصلي'     => ['answer' => 'يعمل بشكل ممتاز', 'score' => 10],
            'السماعات'                    => ['answer' => 'يعمل بشكل ممتاز', 'score' => 10],
            'اختبار الزامور'              => ['answer' => 'يعمل بشكل ممتاز', 'score' => 10],
            'المساحات الأمامية'           => ['answer' => 'يعمل بشكل ممتاز', 'score' => 10],
            'المساحة الخلفية'             => ['answer' => 'يعمل بشكل ممتاز', 'score' => 10],
            'إختبار عمل النوافذ'          => ['answer' => 'يعمل بشكل ممتاز', 'score' => 10],
            'إختبار عمل فتحة السقف'       => ['answer' => 'يعمل بشكل ممتاز', 'score' => 10],
            'إختبار عمل الكراسي الكهربائية' => ['answer' => 'يعمل بشكل ممتاز', 'score' => 10],
            'إختبار عمل ريموت فتح الأبواب' => ['answer' => 'يعمل بشكل ممتاز', 'score' => 10],
            'المرايا الكهربائية'          => ['answer' => 'يعمل بشكل ممتاز', 'score' => 10],
            'اختبار الديفروست الخلفي'     => ['answer' => 'يعمل بشكل ممتاز', 'score' => 10],

            // ─ التكييف ─
            'التبريد (AC)'                => ['answer' => 'ممتاز', 'score' => 10],
            'التدفئة'                      => ['answer' => 'ممتاز', 'score' => 10],
            'التحويلات'                    => ['answer' => 'ممتاز', 'score' => 10],
            'فحص تهريب غاز التكييف'       => ['answer' => 'ممتاز', 'score' => 10],
            'فحص نظام تبريد المحرك'       => ['answer' => 'ممتاز', 'score' => 10],
            'فحص تهريب سائل تبريد المحرك' => ['answer' => 'ممتاز', 'score' => 10],
            'فحص مراوح تبريد المحرك'      => ['answer' => 'ممتاز', 'score' => 10],

            // ─ المكابح ─
            'وجود جميع الأكياس الهوائية'  => ['answer' => 'يعمل بشكل ممتاز', 'score' => 10],
            'بريك / ديسك أمامي يمين'      => ['answer' => 'ممتاز', 'score' => 10],
            'بريك / ديسك أمامي شمال'      => ['answer' => 'ممتاز', 'score' => 10],
            'بريك / ديسك خلفي يمين'       => ['answer' => 'ممتاز', 'score' => 10],
            'بريك / ديسك خلفي شمال'       => ['answer' => 'ممتاز', 'score' => 10],
            'نظام مانع الانزلاق (ABS)'    => ['answer' => 'بحاجة صيانة', 'score' => 3, 'remark' => 'بحاجة إلى صيانة Brake switch (كود)'],
            'حزام الأمان الأمامي يمين'    => ['answer' => 'ممتاز', 'score' => 10],
            'حزام الأمان الأمامي شمال'    => ['answer' => 'ممتاز', 'score' => 10],
            'حزام الأمان الخلفي'           => ['answer' => 'ممتاز', 'score' => 10],
            'عمر الإطارات'                 => ['answer' => 'أقل من 3 سنوات', 'score' => 10, 'remark' => 'إطارات إنتاج 2024'],
            'عمق الفرزة الأمامية'         => ['answer' => 'ممتاز', 'score' => 10],
            'عمق الفرزة الخلفية'          => ['answer' => 'ممتاز', 'score' => 10],

            // ─ فحص الطريق ─
            'مسير السيارة باستقامة'                   => ['answer' => 'ممتاز', 'score' => 10],
            'ميزان طارة الستيرنج'                     => ['answer' => 'بحاجة صيانة', 'score' => 3, 'remark' => 'بحاجة لتعديل المقود'],
            'شعور طبيعي للستيرنج أثناء العمل'        => ['answer' => 'ممتاز', 'score' => 10],
            'لا يوجد صوت غير طبيعي من نظام التعليق'  => ['answer' => 'ممتاز', 'score' => 10],
            'أداء المحرك في درجة حرارة التشغيل'      => ['answer' => 'ممتاز', 'score' => 10],
            'أداء الجير الأوتوماتيكي'                => ['answer' => 'ممتاز', 'score' => 10],
            'أداء نظام البريك'                        => ['answer' => 'ممتاز', 'score' => 10],
            'عمل مثبت السرعة'                         => ['answer' => 'ممتاز', 'score' => 10],
            'لا يوجد اهتزازات من البريك'             => ['answer' => 'ممتاز', 'score' => 10],
            'عداد مسافة المركبة يعمل بشكل صحيح'      => ['answer' => 'ممتاز', 'score' => 10],
        ];

        // Insert results
        $allQuestions = $template->sections()
            ->with('questions')
            ->get()
            ->flatMap(fn($s) => $s->questions);

        $totalScore    = 0;
        $totalMaxScore = 0;

        foreach ($allQuestions as $question) {
            $ans = $answers[$question->label] ?? null;
            if (!$ans) continue;

            $score    = $ans['score']     ?? null;
            $maxScore = $question->max_score;

            if ($maxScore > 0 && $score !== null) {
                $totalScore    += $score * $question->weight;
                $totalMaxScore += $maxScore * $question->weight;
            }

            InspectionResult::create([
                'inspection_id'   => $inspection->id,
                'question_id'     => $question->id,
                'answer'          => $ans['answer'],
                'score'           => $maxScore > 0 ? $score : null,
                'remarks'         => $ans['remark']        ?? null,
                'is_critical_fail'=> $ans['critical_fail'] ?? false,
            ]);
        }

        // Update inspection score
        $percentage = $totalMaxScore > 0
            ? round(($totalScore / $totalMaxScore) * 100, 2)
            : 0;

        $grade = match(true) {
            $percentage >= 90 => 'excellent',
            $percentage >= 75 => 'good',
            $percentage >= 50 => 'needs_attention',
            default           => 'critical',
        };

        $inspection->update([
            'total_score'       => $totalScore,
            'percentage'        => $percentage,
            'grade'             => $grade,
            'has_critical_failure' => false,
        ]);

        $this->command->info("✅ AutoScore-style inspection created!");
        $this->command->info("   Reference: {$inspection->reference_number}");
        $this->command->info("   Vehicle: Toyota RAV4 2024 | VIN: JTMRWRFV2RD246034");
        $this->command->info("   Score: {$percentage}% | Grade: {$grade}");
        $this->command->info("   Template: {$template->name} ({$template->questions()->count()} questions)");
    }
}