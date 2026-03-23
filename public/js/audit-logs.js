// ─── Translation Maps ───
var fieldLabels = {
    // Inspection
    total_score: { ar: 'الدرجة الكلية', en: 'Total Score' },
    max_possible_score: { ar: 'أعلى درجة ممكنة', en: 'Max Possible Score' },
    percentage: { ar: 'النسبة المئوية', en: 'Percentage' },
    grade: { ar: 'التقدير', en: 'Grade' },
    grade_label: { ar: 'وصف التقدير', en: 'Grade Label' },
    has_critical_failure: { ar: 'فشل حرج', en: 'Critical Failure' },
    critical_items: { ar: 'العناصر الحرجة', en: 'Critical Items' },
    status: { ar: 'الحالة', en: 'Status' },
    reference_number: { ar: 'الرقم المرجعي', en: 'Reference' },
    notes: { ar: 'الملاحظات', en: 'Notes' },
    started_at: { ar: 'وقت البدء', en: 'Started At' },
    completed_at: { ar: 'وقت الإنهاء', en: 'Completed At' },
    share_token: { ar: 'رمز المشاركة', en: 'Share Token' },
    is_hidden: { ar: 'مخفي', en: 'Hidden' },
    hidden_reason: { ar: 'سبب الإخفاء', en: 'Hidden Reason' },
    hidden_at: { ar: 'وقت الإخفاء', en: 'Hidden At' },
    price: { ar: 'السعر', en: 'Price' },
    payment_status: { ar: 'حالة الدفع', en: 'Payment Status' },
    paid_at: { ar: 'وقت الدفع', en: 'Paid At' },
    discount: { ar: 'الخصم', en: 'Discount' },
    payment_note: { ar: 'ملاحظة الدفع', en: 'Payment Note' },
    // Vehicle
    make: { ar: 'الشركة المصنعة', en: 'Make' },
    model: { ar: 'الموديل', en: 'Model' },
    year: { ar: 'سنة الصنع', en: 'Year' },
    vin: { ar: 'رقم الهيكل', en: 'VIN' },
    license_plate: { ar: 'رقم اللوحة', en: 'License Plate' },
    color: { ar: 'اللون', en: 'Color' },
    mileage: { ar: 'عداد المسافة', en: 'Mileage' },
    fuel_type: { ar: 'نوع الوقود', en: 'Fuel Type' },
    owner_name: { ar: 'اسم المالك', en: 'Owner Name' },
    owner_phone: { ar: 'هاتف المالك', en: 'Owner Phone' },
    owner_email: { ar: 'إيميل المالك', en: 'Owner Email' },
    // Template
    name: { ar: 'الاسم', en: 'Name' },
    description: { ar: 'الوصف', en: 'Description' },
    is_active: { ar: 'نشط', en: 'Active' },
    scoring_mode: { ar: 'نمط التقييم', en: 'Scoring Mode' },
    version: { ar: 'الإصدار', en: 'Version' },
    // Customer
    phone: { ar: 'الهاتف', en: 'Phone' },
    email: { ar: 'الإيميل', en: 'Email' },
    id_number: { ar: 'رقم الهوية', en: 'ID Number' },
    address: { ar: 'العنوان', en: 'Address' },
    // User
    password: { ar: 'كلمة المرور', en: 'Password' },
    role: { ar: 'الدور', en: 'Role' },
    // Common
    created_by: { ar: 'أنشئ بواسطة', en: 'Created By' },
    created_at: { ar: 'تاريخ الإنشاء', en: 'Created At' },
    updated_at: { ar: 'تاريخ التحديث', en: 'Updated At' },
    deleted_at: { ar: 'تاريخ الحذف', en: 'Deleted At' },
    vehicle_id: { ar: 'المركبة', en: 'Vehicle' },
    template_id: { ar: 'القالب', en: 'Template' },
    inspector_id: { ar: 'الفاحص', en: 'Inspector' },
    customer_id: { ar: 'العميل', en: 'Customer' },
};

var actionLabels = {
    inspection_created: { ar: 'إنشاء فحص', en: 'Inspection Created' },
    inspection_completed: { ar: 'إكمال فحص', en: 'Inspection Completed' },
    inspection_cancelled: { ar: 'إلغاء فحص', en: 'Inspection Cancelled' },
    inspection_started: { ar: 'بدء فحص', en: 'Inspection Started' },
    inspection_hidden: { ar: 'إخفاء فحص', en: 'Inspection Hidden' },
    inspection_shown: { ar: 'إظهار فحص', en: 'Inspection Shown' },
    inspection_deleted: { ar: 'حذف فحص', en: 'Inspection Deleted' },
    vehicle_created: { ar: 'إنشاء مركبة', en: 'Vehicle Created' },
    vehicle_updated: { ar: 'تعديل مركبة', en: 'Vehicle Updated' },
    vehicle_deleted: { ar: 'حذف مركبة', en: 'Vehicle Deleted' },
    template_created: { ar: 'إنشاء قالب', en: 'Template Created' },
    template_updated: { ar: 'تعديل قالب', en: 'Template Updated' },
    template_deleted: { ar: 'حذف قالب', en: 'Template Deleted' },
    customer_created: { ar: 'إنشاء عميل', en: 'Customer Created' },
    customer_updated: { ar: 'تعديل عميل', en: 'Customer Updated' },
    customer_deleted: { ar: 'حذف عميل', en: 'Customer Deleted' },
    user_created: { ar: 'إنشاء مستخدم', en: 'User Created' },
    user_updated: { ar: 'تعديل مستخدم', en: 'User Updated' },
    user_deleted: { ar: 'حذف مستخدم', en: 'User Deleted' },
    settings_updated: { ar: 'تعديل الإعدادات', en: 'Settings Updated' },
};

var valueLabels = {
    // Status
    draft: { ar: 'مسودة', en: 'Draft' },
    in_progress: { ar: 'قيد التنفيذ', en: 'In Progress' },
    completed: { ar: 'مكتمل', en: 'Completed' },
    cancelled: { ar: 'ملغي', en: 'Cancelled' },
    // Grade
    excellent: { ar: 'ممتاز', en: 'Excellent' },
    good: { ar: 'جيد', en: 'Good' },
    fair: { ar: 'مقبول', en: 'Fair' },
    poor: { ar: 'ضعيف', en: 'Poor' },
    critical: { ar: 'حرج', en: 'Critical' },
    // Boolean
    true: { ar: 'نعم', en: 'Yes' },
    false: { ar: 'لا', en: 'No' },
    '1': { ar: 'نعم', en: 'Yes' },
    '0': { ar: 'لا', en: 'No' },
    // Payment
    paid: { ar: 'مدفوع', en: 'Paid' },
    unpaid: { ar: 'غير مدفوع', en: 'Unpaid' },
    // Scoring
    scored: { ar: 'تقييم بالعلامات', en: 'Scored' },
    descriptive: { ar: 'وصفي', en: 'Descriptive' },
    // Fuel
    gasoline: { ar: 'بنزين', en: 'Gasoline' },
    diesel: { ar: 'ديزل', en: 'Diesel' },
    electric: { ar: 'كهربائي', en: 'Electric' },
    hybrid: { ar: 'هجين', en: 'Hybrid' },
    lpg: { ar: 'غاز', en: 'LPG' },
};

function tField(key) {
    var lang = document.documentElement.getAttribute('lang') || 'en';
    return (fieldLabels[key] && fieldLabels[key][lang]) || key;
}

function tAction(action) {
    var lang = document.documentElement.getAttribute('lang') || 'en';
    return (actionLabels[action] && actionLabels[action][lang]) || action;
}

function tValue(val) {
    if (val === null || val === undefined) return '-';
    var str = String(val);
    var lang = document.documentElement.getAttribute('lang') || 'en';
    return (valueLabels[str] && valueLabels[str][lang]) || str;
}

// ─── Main Function ───

function showLogDetails(data) {
    var body = document.getElementById('log-details-body');
    var lang = document.documentElement.getAttribute('lang') || 'en';
    var isDark = document.documentElement.classList.contains('dark');

    var html = '<div style="font-weight:700;margin-bottom:.75rem;font-size:.95rem;color:' + (isDark ? '#e2e8f0' : '#1e293b') + '">' + tAction(data.action) + '</div>';

    if (data.old && Object.keys(data.old).length) {
        html += '<div style="margin-bottom:1rem">';
        html += '<div style="font-size:.82rem;font-weight:600;color:#ef4444;margin-bottom:.35rem">' + (lang === 'ar' ? 'القيم القديمة' : 'Old Values') + '</div>';
        html += '<div style="background:' + (isDark ? 'rgba(239,68,68,.12)' : '#fef2f2') + ';border-radius:8px;padding:.75rem;font-size:.82rem">';
        for (var k in data.old) {
            html += '<div style="display:flex;justify-content:space-between;padding:.25rem 0;border-bottom:1px solid ' + (isDark ? 'rgba(239,68,68,.2)' : '#fecaca') + '">';
            html += '<span style="color:' + (isDark ? '#94a3b8' : '#64748b') + ';font-weight:500">' + tField(k) + '</span>';
            html += '<span style="font-weight:600;color:' + (isDark ? '#fca5a5' : '#1e293b') + '">' + tValue(data.old[k]) + '</span>';
            html += '</div>';
        }
        html += '</div></div>';
    }

    if (data.new && Object.keys(data.new).length) {
        html += '<div>';
        html += '<div style="font-size:.82rem;font-weight:600;color:#22c55e;margin-bottom:.35rem">' + (lang === 'ar' ? 'القيم الجديدة' : 'New Values') + '</div>';
        html += '<div style="background:' + (isDark ? 'rgba(34,197,94,.12)' : '#f0fdf4') + ';border-radius:8px;padding:.75rem;font-size:.82rem">';
        for (var k in data.new) {
            html += '<div style="display:flex;justify-content:space-between;padding:.25rem 0;border-bottom:1px solid ' + (isDark ? 'rgba(34,197,94,.2)' : '#bbf7d0') + '">';
            html += '<span style="color:' + (isDark ? '#94a3b8' : '#64748b') + ';font-weight:500">' + tField(k) + '</span>';
            html += '<span style="font-weight:600;color:' + (isDark ? '#86efac' : '#1e293b') + '">' + tValue(data.new[k]) + '</span>';
            html += '</div>';
        }
        html += '</div></div>';
    }

    if ((!data.old || !Object.keys(data.old).length) && (!data.new || !Object.keys(data.new).length)) {
        html += '<p class="text-muted text-center">' + (lang === 'ar' ? 'لا توجد تفاصيل إضافية' : 'No additional details') + '</p>';
    }

    body.innerHTML = html;
    openModal('log-details-modal');
}