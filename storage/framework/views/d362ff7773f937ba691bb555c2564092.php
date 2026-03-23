<?php $__env->startSection('title', app()->getLocale() === 'ar' ? 'تفاصيل الفحص' : 'Inspection Details'); ?>

<?php
    $lang = app()->getLocale();
    $isScored = $inspection->template->isScored();
    $gradeStr = is_object($inspection->grade) ? $inspection->grade->value : ($inspection->grade ?? '');
    $gradeLabel = $inspection->grade_label ?? ucfirst(str_replace('_', ' ', $gradeStr));
    $gradeColor = $inspection->grade_color ?? '#6b7280';
    $gradeMap = ['excellent'=>'success','good'=>'primary','needs_attention'=>'warning','critical'=>'danger'];
    $gradeBadge = $gradeMap[$gradeStr] ?? 'secondary';
?>

<?php $__env->startSection('content'); ?>


<?php if($inspection->is_hidden): ?>
<div class="alert-warning" style="border-radius:8px;padding:12px 20px;margin-bottom:16px;display:flex;align-items:center;justify-content:space-between">
    <div>
        <span style="font-weight:700;color:#92400e">🙈 <?php echo e($lang === 'ar' ? 'هذا الفحص مخفي' : 'This inspection is hidden'); ?></span>
        <?php if($inspection->hidden_reason): ?>
            <span style="color:#92400e;font-size:.85rem"> — <?php echo e($inspection->hidden_reason); ?></span>
        <?php endif; ?>
        <div style="font-size:.75rem;color:#b45309;margin-top:2px">
            <?php echo e($lang === 'ar' ? 'مخفي بواسطة' : 'Hidden by'); ?>: <?php echo e($inspection->hiddenByUser?->name ?? '—'); ?>

            · <?php echo e($inspection->hidden_at?->format('Y-m-d H:i')); ?>

        </div>
    </div>
    <form method="POST" action="<?php echo e(route('inspections.toggleHidden', $inspection)); ?>" style="margin:0">
        <?php echo csrf_field(); ?>
        <button type="submit" class="btn btn-sm btn-warning" style="white-space:nowrap">
            👁️ <?php echo e($lang === 'ar' ? 'إظهار الفحص' : 'Show Inspection'); ?>

        </button>
    </form>
</div>
<?php endif; ?>


<div class="ins-hero">
    <?php if($inspection->status->value === 'completed'): ?>
    <?php
        if (!$inspection->share_token) {
            $inspection->share_token = bin2hex(random_bytes(32));
            $inspection->saveQuietly();
        }
        $cust = $inspection->vehicle?->customer;
        $ownerPhone = $cust?->phone ?? $inspection->vehicle?->owner_phone;
        $ownerEmail = $cust?->email ?? $inspection->vehicle?->owner_email;
        $ownerName = $cust?->name ?? $inspection->vehicle?->owner_name ?? '';
        $waPhone = $ownerPhone ? preg_replace('/[^0-9]/', '', $ownerPhone) : null;
        if ($waPhone && str_starts_with($waPhone, '0')) { $waPhone = '962' . substr($waPhone, 1); }
        $shareUrl = url('/share/' . $inspection->share_token);
        $pdfUrl = route('share.pdf', $inspection->share_token);
    ?>
    <?php endif; ?>

    <div class="ins-hero-top">
        <div>
            <div class="ins-hero-title">
                <?php echo e($lang === 'ar' ? 'تفاصيل الفحص' : 'Inspection Details'); ?>

                <?php if(!$isScored): ?>
                    <span style="font-size:.7rem;background:rgba(255,255,255,.2);padding:2px 10px;border-radius:20px;margin-inline-start:8px"><?php echo e($lang === 'ar' ? '📝 وصفي' : '📝 Descriptive'); ?></span>
                <?php endif; ?>
            </div>
            <div class="ins-hero-ref"><?php echo e($inspection->reference_number); ?></div>
        </div>
        <div class="ins-hero-actions">
            <?php if($inspection->status->value === 'completed'): ?>
                
                <?php if($inspection->payment_status === 'paid'): ?>
                    <span class="hbtn" style="background:#dcfce7;color:#16a34a;cursor:default;font-weight:700" title="<?php echo e($inspection->payment_note ?? ''); ?>">
                        💰 <?php echo e($lang === 'ar' ? 'مدفوع' : 'Paid'); ?> — <?php echo e(number_format($inspection->price - $inspection->discount, 2)); ?> <?php echo e($lang === 'ar' ? 'د.أ' : 'JOD'); ?>

                        <?php if($inspection->payment_note): ?> <span style="font-weight:400;font-size:.75rem;opacity:.8">| <?php echo e($inspection->payment_note); ?></span> <?php endif; ?>
                    </span>
                <?php elseif($inspection->price > 0): ?>
                    <form method="POST" action="<?php echo e(route('finance.markPaid', $inspection->id)); ?>" style="display:inline-flex;gap:4px;align-items:center">
                        <?php echo csrf_field(); ?>
                        <input type="text" name="payment_note" class="form-control" style="width:120px;padding:4px 8px;font-size:.8rem;background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.3);color:#fff;border-radius:6px" placeholder="<?php echo e($lang === 'ar' ? 'ملاحظة...' : 'Note...'); ?>">
                        <button type="submit" class="hbtn" style="background:#f59e0b;color:#fff">
                            💵 <?php echo e($lang === 'ar' ? 'قبض ' . number_format($inspection->price, 2) . ' د.أ' : 'Collect ' . number_format($inspection->price, 2) . ' JOD'); ?>

                        </button>
                    </form>
                <?php endif; ?>

                <a href="<?php echo e(route('reports.pdf', $inspection)); ?>" class="hbtn hbtn-pdf">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6"/><path d="M12 18v-6"/><path d="M9 15h6"/></svg>
                    PDF
                </a>
                <a href="<?php echo e($shareUrl); ?>" target="_blank" class="hbtn hbtn-preview">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    <?php echo e($lang === 'ar' ? 'معاينة' : 'Preview'); ?>

                </a>
                <button type="button" class="hbtn hbtn-link" onclick="copyShareLink()" id="share-btn"
                    data-copied="<?php echo e($lang === 'ar' ? '✅ تم النسخ!' : '✅ Copied!'); ?>">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71"/></svg>
                    <?php echo e($lang === 'ar' ? 'نسخ الرابط' : 'Copy Link'); ?>

                </button>
                <input type="hidden" id="share-url" value="<?php echo e($shareUrl); ?>">
                <?php if($waPhone): ?>
                <a href="https://wa.me/<?php echo e($waPhone); ?>?text=<?php echo e(urlencode(($lang === 'ar' ? 'مرحباً '.$ownerName."،\nتقرير فحص مركبتك جاهز:\n" : 'Hi '.$ownerName.",\nYour report:\n").$shareUrl)); ?>" target="_blank" class="hbtn hbtn-wa">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    <?php echo e($lang === 'ar' ? 'واتساب' : 'WhatsApp'); ?>

                </a>
                <?php endif; ?>
                <?php if($ownerEmail): ?>
                <a href="mailto:<?php echo e($ownerEmail); ?>?subject=<?php echo e(urlencode(($lang === 'ar' ? 'تقرير فحص - ' : 'Report - ').$inspection->reference_number)); ?>&body=<?php echo e(urlencode(($lang === 'ar' ? "مرحباً ".$ownerName."\n\nتقرير فحص مركبتك:\n".$shareUrl."\n\nPDF: ".$pdfUrl : "Hi ".$ownerName."\n\nYour report:\n".$shareUrl."\n\nPDF: ".$pdfUrl))); ?>" class="hbtn hbtn-email">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M22 4l-10 8L2 4"/></svg>
                    <?php echo e($lang === 'ar' ? 'إيميل' : 'Email'); ?>

                </a>
                <?php endif; ?>
            <?php endif; ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('conduct inspections')): ?>
            <?php if(in_array($inspection->status->value, ['draft','in_progress'])): ?>
                <a href="<?php echo e(route('inspections.conduct', $inspection)); ?>" class="hbtn hbtn-continue"><?php echo e($lang === 'ar' ? 'استكمال الفحص' : 'Continue'); ?></a>
            <?php endif; ?>
            <?php endif; ?>

            
            <?php if(auth()->user()->hasRole('Super Admin') && !$inspection->is_hidden): ?>
                <button type="button" class="hbtn hbtn-continue" onclick="document.getElementById('hide-modal').style.display='flex'">
                    🙈 <?php echo e($lang === 'ar' ? 'إخفاء' : 'Hide'); ?>

                </button>
            <?php endif; ?>
        </div>
    </div>

    
    <div class="score-ring-wrap">
        <?php if($isScored): ?>
            <?php
                $pct = $inspection->percentage ?? 0;
                $circumference = 2 * 3.14159 * 38;
                $offset = $circumference - ($pct / 100) * $circumference;
                $ringColor = $pct >= 75 ? '#6ee7b7' : ($pct >= 50 ? '#fcd34d' : '#fca5a5');
            ?>
            <div class="score-ring">
                <svg viewBox="0 0 88 88" width="100%" height="100%">
                    <circle class="score-ring-bg" cx="44" cy="44" r="38"/>
                    <circle class="score-ring-fill" cx="44" cy="44" r="38"
                        stroke="<?php echo e($ringColor); ?>"
                        stroke-dasharray="<?php echo e($circumference); ?>"
                        stroke-dashoffset="<?php echo e($offset); ?>"/>
                </svg>
                <div class="score-ring-text">
                    <span class="score-ring-value"><?php echo e($pct ? $pct.'%' : '—'); ?></span>
                    <span class="score-ring-sub"><?php echo e($lang === 'ar' ? 'النتيجة' : 'Score'); ?></span>
                </div>
            </div>
        <?php endif; ?>

        <div class="hero-stats">
            <div class="hero-stat">
                <span class="hero-stat-label"><?php echo e(__('status')); ?></span>
                <span class="hero-badge hero-badge-<?php echo e($inspection->status->color()); ?>"><?php echo e($inspection->status->label()); ?></span>
            </div>
            <?php if($isScored): ?>
            <div class="hero-stat">
                <span class="hero-stat-label"><?php echo e($lang === 'ar' ? 'التقييم' : 'Grade'); ?></span>
                <?php if($gradeStr): ?>
                <span class="hero-badge hero-badge-<?php echo e($gradeBadge); ?>"><?php echo e($lang === 'ar' ? __($gradeStr) : $gradeLabel); ?></span>
                <?php else: ?> <span class="hero-badge hero-badge-secondary">—</span> <?php endif; ?>
            </div>
            <div class="hero-stat">
                <span class="hero-stat-label"><?php echo e($lang === 'ar' ? 'إخفاق حرج' : 'Critical'); ?></span>
                <span class="hero-badge <?php echo e($inspection->has_critical_failure ? 'hero-badge-danger' : 'hero-badge-success'); ?>">
                    <?php echo e($inspection->has_critical_failure ? ($lang === 'ar' ? 'نعم' : 'Yes') : ($lang === 'ar' ? 'لا' : 'No')); ?>

                </span>
            </div>
            <?php else: ?>
            <div class="hero-stat">
                <span class="hero-stat-label"><?php echo e($lang === 'ar' ? 'النمط' : 'Mode'); ?></span>
                <span class="hero-badge hero-badge-secondary"><?php echo e($lang === 'ar' ? 'فحص وصفي' : 'Descriptive'); ?></span>
            </div>
            <?php endif; ?>
            <div class="hero-stat">
                <span class="hero-stat-label"><?php echo e($lang === 'ar' ? 'المالك' : 'Owner'); ?></span>
                <span class="hero-stat-value"><?php echo e($inspection->vehicle?->owner_name ?? '—'); ?></span>
            </div>
        </div>
    </div>
</div>


<div class="ins-info-grid">
    <div class="ins-info-card">
        <div class="ins-info-header">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 17h14v-3H5v3zm12-7l-2-4H9L7 10"/><circle cx="7.5" cy="17.5" r="1.5"/><circle cx="16.5" cy="17.5" r="1.5"/></svg>
            <?php echo e(__('vehicle')); ?>

        </div>
        <div class="ins-info-body">
            <div><div class="ins-detail-label"><?php echo e(__('vehicle')); ?></div><div class="ins-detail-value"><?php echo e($inspection->vehicle->full_name); ?></div></div>
            <div><div class="ins-detail-label"><?php echo e(__('plate_number')); ?></div><div class="ins-detail-value ins-detail-mono"><?php echo e($inspection->vehicle->license_plate ?? '—'); ?></div></div>
            <div><div class="ins-detail-label"><?php echo e(__('vin')); ?></div><div class="ins-detail-value ins-detail-mono"><?php echo e($inspection->vehicle->vin ?? '—'); ?></div></div>
            <div><div class="ins-detail-label"><?php echo e(__('owner_name')); ?></div><div class="ins-detail-value"><?php echo e($inspection->vehicle->owner_name ?? '—'); ?></div></div>
        </div>
    </div>
    <div class="ins-info-card">
        <div class="ins-info-header">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
            <?php echo e($lang === 'ar' ? 'تفاصيل الفحص' : 'Inspection Info'); ?>

        </div>
        <div class="ins-info-body">
            <div><div class="ins-detail-label"><?php echo e($lang === 'ar' ? 'القالب' : 'Template'); ?></div><div class="ins-detail-value"><?php echo e($inspection->template->name); ?></div></div>
            <div><div class="ins-detail-label"><?php echo e($lang === 'ar' ? 'الفاحص' : 'Inspector'); ?></div><div class="ins-detail-value"><?php echo e($inspection->inspector?->name ?? '—'); ?></div></div>
            <div><div class="ins-detail-label"><?php echo e($lang === 'ar' ? 'بدأ في' : 'Started'); ?></div><div class="ins-detail-value"><?php echo e($inspection->started_at?->format('Y-m-d H:i') ?? '—'); ?></div></div>
            <div><div class="ins-detail-label"><?php echo e($lang === 'ar' ? 'اكتمل في' : 'Completed'); ?></div><div class="ins-detail-value"><?php echo e($inspection->completed_at?->format('Y-m-d H:i') ?? '—'); ?></div></div>
        </div>
    </div>
</div>


<?php $__currentLoopData = $inspection->template->sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<?php
    $sectionResults = $inspection->results->whereIn('question_id', $section->questions->pluck('id'));
    if ($isScored) {
        $sectionMax = $section->questions->where('max_score', '>', 0)->sum('max_score');
        $sectionScore = $sectionResults->sum('score');
        $sectionPct = $sectionMax > 0 ? round($sectionScore / $sectionMax * 100) : null;
    } else {
        $sectionPct = null;
    }
?>
<div class="ins-section">
    <div class="ins-section-header" onclick="toggleSec('isec-<?php echo e($loop->index); ?>')">
        <div class="ins-section-title">
            <span class="ins-section-num"><?php echo e($loop->iteration); ?></span>
            <?php echo e($section->name); ?>

        </div>
        <div style="display:flex;align-items:center;gap:12px">
            <?php if($isScored && $sectionPct !== null): ?>
            <span style="font-size:.82rem;font-weight:700;color:<?php echo e($sectionPct >= 75 ? '#6ee7b7' : ($sectionPct >= 50 ? '#fcd34d' : '#fca5a5')); ?>"><?php echo e($sectionPct); ?>%</span>
            <?php endif; ?>
            <span class="ins-section-arrow" id="isec-arrow-<?php echo e($loop->index); ?>">▼</span>
        </div>
    </div>
    <div id="isec-<?php echo e($loop->index); ?>">
        <?php $__currentLoopData = $section->questions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $question): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php $result = $inspection->results->where('question_id', $question->id)->first(); ?>
        <div class="ins-q-row">
            <div style="flex:1;min-width:0">
                <div class="ins-q-label">
                    <?php echo e($question->label); ?>

                    <?php if($isScored && $question->is_critical): ?> <span class="ins-q-critical"><?php echo e($lang === 'ar' ? 'حرج' : 'CRITICAL'); ?></span> <?php endif; ?>
                </div>
                <div class="ins-q-answer">
                    <?php if($result): ?>
                        <?php if($question->type->value === 'checkbox'): ?>
                            <?php if(($result->answer ?? '0') == '1'): ?>
                                <span style="color:var(--success)">✓ <?php echo e($lang==='ar'?'نعم':'Yes'); ?></span>
                            <?php else: ?>
                                <span style="color:var(--danger)">✗ <?php echo e($lang==='ar'?'لا':'No'); ?></span>
                            <?php endif; ?>
                        <?php else: ?>
                            <?php echo e($result->answer ?? '—'); ?>

                        <?php endif; ?>
                    <?php else: ?>
                        <span style="color:var(--gray-300)"><?php echo e($lang === 'ar' ? 'لم يُجب' : 'No answer'); ?></span>
                    <?php endif; ?>
                </div>
                <?php if($result?->remarks): ?>
                <div class="ins-q-remark">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
                    <?php echo e($result->remarks); ?>

                </div>
                <?php endif; ?>
                <?php if($isScored && $result?->is_critical_fail): ?>
                <span class="ins-q-critical" style="display:inline-block;margin-top:4px"><?php echo e($lang === 'ar' ? '⚠ إخفاق حرج' : '⚠ Critical Fail'); ?></span>
                <?php endif; ?>
                <?php if($result && $result->media && $result->media->count()): ?>
                <div class="ins-q-media">
                    <?php $__currentLoopData = $result->media; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $media): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div style="position:relative;display:inline-block">
                        <?php if($media->isImage()): ?>
                        <a href="<?php echo e($media->url); ?>" target="_blank"><img src="<?php echo e($media->url); ?>" alt="<?php echo e($media->original_name); ?>"></a>
                        <?php else: ?>
                        <a href="<?php echo e($media->url); ?>" target="_blank" class="ins-q-remark" style="padding:4px 10px;background:var(--gray-50);border:1px solid var(--gray-200);border-radius:6px;text-decoration:none;color:var(--primary)">📎 <?php echo e(\Illuminate\Support\Str::limit($media->original_name, 20)); ?></a>
                        <?php endif; ?>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit inspections')): ?>
                        <form method="POST" action="<?php echo e(route('inspections.deleteMedia', $media->id)); ?>" style="position:absolute;top:-6px;<?php echo e($lang === 'ar' ? 'left' : 'right'); ?>:-6px;margin:0" onsubmit="return confirm('<?php echo e($lang === 'ar' ? 'حذف هذه الصورة؟' : 'Delete this image?'); ?>')">
                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                            <button type="submit" style="width:20px;height:20px;border-radius:50%;background:#ef4444;color:#fff;border:2px solid #fff;font-size:11px;line-height:1;cursor:pointer;display:flex;align-items:center;justify-content:center;padding:0;box-shadow:0 1px 3px rgba(0,0,0,.3)" title="<?php echo e($lang === 'ar' ? 'حذف' : 'Delete'); ?>">✕</button>
                        </form>
                        <?php endif; ?>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <?php endif; ?>
            </div>
            <?php if($isScored): ?>
                <?php $qTypeVal = is_object($question->type) ? $question->type->value : $question->type; ?>
                <?php if($question->max_score > 0 && $result && !in_array($qTypeVal, ['text', 'photo'])): ?>
                <?php $scorePct = $question->max_score > 0 ? ($result->score / $question->max_score * 100) : 0; ?>
                <div class="ins-q-score">
                    <div class="ins-q-score-val" style="color:<?php echo e($scorePct >= 75 ? 'var(--success)' : ($scorePct >= 50 ? 'var(--warning)' : 'var(--danger)')); ?>"><?php echo e($result->score); ?></div>
                    <div class="ins-q-score-max">/ <?php echo e(intval($question->max_score)); ?></div>
                </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<?php if($inspection->notes): ?>
<div class="ins-info-card" style="margin-top:12px">
    <div class="ins-info-header">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6"/><line x1="16" y1="13" x2="8" y2="13"/></svg>
        <?php echo e(__('notes')); ?>

    </div>
    <div style="padding:16px 20px;font-size:.9rem;color:var(--gray-700);line-height:1.6"><?php echo e($inspection->notes); ?></div>
</div>
<?php endif; ?>

<?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete inspections')): ?>
<?php if($inspection->status->value !== 'completed'): ?>
<div style="margin-top:20px;text-align:<?php echo e($lang === 'ar' ? 'left' : 'right'); ?>">
    <form id="del-inspection" action="<?php echo e(route('inspections.destroy', $inspection)); ?>" method="POST" style="display:none"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?></form>
    <button type="button" class="btn btn-danger btn-sm" onclick="confirmDelete('del-inspection', '<?php echo e($inspection->reference_number); ?>')"><?php echo e(__('delete')); ?></button>
</div>
<?php endif; ?>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('modals'); ?>
<?php echo $__env->make('partials.delete-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>


<?php if(auth()->user()->hasRole('Super Admin')): ?>
<div id="hide-modal" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.5);align-items:center;justify-content:center" onclick="if(event.target===this)this.style.display='none'">
    <div class="card" style="max-width:420px;width:90%;box-shadow:0 20px 60px rgba(0,0,0,.3)">
        <div class="card-header"><h3 style="color:#92400e;margin:0">🙈 <?php echo e($lang === 'ar' ? 'إخفاء الفحص' : 'Hide Inspection'); ?></h3></div>
        <div class="card-body">
            <p style="color:var(--gray-500);font-size:.85rem;margin:0 0 16px">
                <?php echo e($lang === 'ar' ? 'الفحص المخفي لن يظهر لأحد غيرك، ولن يحتسب بالإحصائيات أو التقارير.' : 'Hidden inspections are invisible to all users except you, and excluded from all stats and reports.'); ?>

            </p>
            <form method="POST" action="<?php echo e(route('inspections.toggleHidden', $inspection)); ?>">
                <?php echo csrf_field(); ?>
                <div class="form-group" style="margin-bottom:16px">
                    <label class="form-label"><?php echo e($lang === 'ar' ? 'السبب (اختياري)' : 'Reason (optional)'); ?></label>
                    <input type="text" name="hidden_reason" class="form-control" placeholder="<?php echo e($lang === 'ar' ? 'مثال: فحص تجريبي' : 'e.g. Test inspection'); ?>">
                </div>
                <div style="display:flex;gap:8px;justify-content:flex-end">
                    <button type="button" class="btn btn-secondary" onclick="document.getElementById('hide-modal').style.display='none'"><?php echo e($lang === 'ar' ? 'إلغاء' : 'Cancel'); ?></button>
                    <button type="submit" class="btn btn-primary">🙈 <?php echo e($lang === 'ar' ? 'إخفاء' : 'Hide'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<script src="<?php echo e(asset('js/inspection-show.js')); ?>"></script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\vis\resources\views/inspections/show.blade.php ENDPATH**/ ?>