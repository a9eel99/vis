<?php $__env->startSection('title', app()->getLocale() === 'ar' ? 'تنفيذ الفحص' : 'Conduct Inspection'); ?>

<?php $__env->startSection('content'); ?>
<?php
    $sections = $inspection->template->sections;
    $totalSections = $sections->count();
    $lang = app()->getLocale();
    $isScored = $inspection->template->isScored();
?>


<div class="wizard-header">
    <div class="wizard-info">
        <div class="wizard-vehicle-badge">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 17h14v-3H5v3zm12-7l-2-4H9L7 10"/><circle cx="7.5" cy="17.5" r="1.5"/><circle cx="16.5" cy="17.5" r="1.5"/></svg>
            <span><?php echo e($inspection->vehicle->full_name); ?></span>
        </div>
        <div class="wizard-meta">
            <span class="wizard-ref"><?php echo e($inspection->reference_number); ?></span>
            <span class="wizard-template"><?php echo e($inspection->template->name); ?></span>
            <?php if(!$isScored): ?>
                <span class="badge badge-secondary" style="font-size:.7rem"><?php echo e($lang === 'ar' ? '📝 فحص وصفي' : '📝 Descriptive'); ?></span>
            <?php endif; ?>
        </div>
    </div>
    <?php if($isScored): ?>
    <div class="wizard-score-box">
        <div class="wizard-score-ring" id="score-ring">
            <svg viewBox="0 0 80 80">
                <circle cx="40" cy="40" r="35" fill="none" stroke="#e5e7eb" stroke-width="5"/>
                <circle cx="40" cy="40" r="35" fill="none" stroke="var(--accent)" stroke-width="5" stroke-linecap="round" stroke-dasharray="220" stroke-dashoffset="220" id="score-circle" style="transition:stroke-dashoffset .5s ease,stroke .3s"/>
            </svg>
            <div class="wizard-score-value" id="score-percentage">0%</div>
        </div>
        <div id="live-grade" class="wizard-grade">-</div>
    </div>
    <?php endif; ?>
</div>


<div class="wizard-steps" id="wizard-steps">
    <?php $__currentLoopData = $sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="wizard-step <?php echo e($i === 0 ? 'active' : ''); ?>" data-step="<?php echo e($i); ?>" onclick="goToStep(<?php echo e($i); ?>)">
        <div class="step-number"><?php echo e($i + 1); ?></div>
        <div class="step-label"><?php echo e($section->name); ?></div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>

<form method="POST" action="<?php echo e(route('inspections.submit', $inspection)); ?>" id="inspection-form" data-total-steps="<?php echo e($totalSections); ?>" data-scoring-mode="<?php echo e($isScored ? 'scored' : 'descriptive'); ?>" enctype="multipart/form-data">
    <?php echo csrf_field(); ?>

    <?php $__currentLoopData = $sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sIdx => $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="wizard-panel <?php echo e($sIdx === 0 ? 'active' : ''); ?>" id="panel-<?php echo e($sIdx); ?>">
        <div class="wizard-panel-header">
            <div class="panel-step-badge"><?php echo e($lang === 'ar' ? 'الخطوة' : 'Step'); ?> <?php echo e($sIdx + 1); ?> / <?php echo e($totalSections); ?></div>
            <h2><?php echo e($section->name); ?></h2>
            <?php if($section->description): ?>
                <p><?php echo e($section->description); ?></p>
            <?php endif; ?>
        </div>

        <div class="wizard-panel-body">
            <?php $__currentLoopData = $section->questions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $qIdx => $question): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php $existing = $existingAnswers[$question->id] ?? null; ?>
            <div class="wizard-question <?php echo e($question->is_critical && $isScored ? 'critical' : ''); ?>">
                <div class="wq-header">
                    <span class="wq-num"><?php echo e($qIdx + 1); ?></span>
                    <div class="wq-title">
                        <?php echo e($question->label); ?>

                        <?php if($question->is_critical && $isScored): ?>
                            <span class="wq-critical-tag"><?php echo e($lang === 'ar' ? '⚠ حرج' : '⚠ Critical'); ?></span>
                        <?php endif; ?>
                    </div>
                    <?php if($isScored && $question->max_score > 0 && !in_array(is_object($question->type) ? $question->type->value : $question->type, ['text', 'photo', 'video'])): ?>
                        <div class="wq-score-badge" id="qscore-<?php echo e($question->id); ?>">0/<?php echo e(intval($question->max_score)); ?></div>
                    <?php endif; ?>
                </div>

                <?php if($question->description): ?>
                    <p class="wq-desc"><?php echo e($question->description); ?></p>
                <?php endif; ?>

                <div class="wq-body">
                    <?php switch($question->type->value):
                        case ('text'): ?>
                            <textarea name="answers[<?php echo e($question->id); ?>][answer]" class="form-control" rows="2" placeholder="<?php echo e($lang === 'ar' ? 'أدخل ملاحظاتك...' : 'Enter your notes...'); ?>"><?php echo e($existing?->answer ?? ''); ?></textarea>
                            <?php break; ?>

                        <?php case ('number'): ?>
                            <div class="wq-number-input">
                                <button type="button" class="num-btn" onclick="stepNum(this,-1)">−</button>
                                <input type="number" name="answers[<?php echo e($question->id); ?>][answer]" class="form-control"
                                    value="<?php echo e($existing?->answer ?? ''); ?>" min="0" max="999999"
                                    step="any" placeholder="0"
                                    data-question="<?php echo e($question->id); ?>" data-weight="<?php echo e($question->weight); ?>" data-max="<?php echo e($question->max_score); ?>"
                                    <?php echo e($isScored ? 'oninput=updateNumScore(this)' : ''); ?>>
                                <button type="button" class="num-btn" onclick="stepNum(this,1)">+</button>
                            </div>
                            <?php break; ?>

                        <?php case ('checkbox'): ?>
                            <div class="wq-toggle-group">
                                <input type="hidden" name="answers[<?php echo e($question->id); ?>][answer]" value="0" id="hidden-<?php echo e($question->id); ?>">
                                <button type="button" class="wq-toggle <?php echo e(($existing?->answer ?? '') == '1' ? 'active' : ''); ?>" onclick="toggleCheck(this, '<?php echo e($question->id); ?>', <?php echo e(intval($question->max_score)); ?>)" data-question="<?php echo e($question->id); ?>" data-weight="<?php echo e($question->weight); ?>" data-max="<?php echo e($question->max_score); ?>">
                                    <span class="toggle-icon"><?php echo e(($existing?->answer ?? '') == '1' ? '✅' : '☐'); ?></span>
                                    <span><?php echo e($lang === 'ar' ? 'نعم / متوفر' : 'Yes / Available'); ?></span>
                                </button>
                            </div>
                            <?php break; ?>

                        <?php case ('dropdown'): ?>
                            <div class="wq-options-grid">
                                <input type="hidden" name="answers[<?php echo e($question->id); ?>][answer]" value="<?php echo e($existing?->answer ?? ''); ?>" id="dd-<?php echo e($question->id); ?>">
                                <?php if(is_array($question->options)): ?>
                                    <?php $__currentLoopData = $question->options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $optIdx => $opt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $optLabel = is_array($opt) ? ($opt['label'] ?? '') : $opt;
                                            $optScore = is_array($opt) ? ($opt['score'] ?? 0) : 0;
                                            $isSelected = ($existing?->answer ?? '') == $optLabel;
                                        ?>
                                        <button type="button"
                                            class="wq-option <?php echo e($isSelected ? 'selected' : ''); ?>"
                                            data-label="<?php echo e($optLabel); ?>"
                                            data-score="<?php echo e($optScore); ?>"
                                            data-question="<?php echo e($question->id); ?>"
                                            data-weight="<?php echo e($question->weight); ?>"
                                            data-max="<?php echo e($question->max_score); ?>"
                                            onclick="selectOption(this)">
                                            <?php if($isScored): ?>
                                                <?php if($optScore >= 8): ?>
                                                    <span class="opt-indicator green"></span>
                                                <?php elseif($optScore >= 5): ?>
                                                    <span class="opt-indicator amber"></span>
                                                <?php else: ?>
                                                    <span class="opt-indicator red"></span>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                            <?php echo e($optLabel); ?>

                                        </button>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            </div>
                            <?php break; ?>

                        <?php case ('photo'): ?>
                        <?php case ('video'): ?>
                            <div class="wq-upload-area" id="upload-<?php echo e($question->id); ?>">
                                <div class="upload-files-list" id="files-<?php echo e($question->id); ?>"></div>
                                <button type="button" class="wq-upload-btn" onclick="document.getElementById('finput-<?php echo e($question->id); ?>').click()">
                                    <span><?php echo e($question->type->value == 'photo' ? '📷' : '🎥'); ?></span>
                                    <?php echo e($lang === 'ar' ? ($question->type->value == 'photo' ? 'إضافة صور' : 'إضافة فيديو') : ($question->type->value == 'photo' ? 'Add Photos' : 'Add Videos')); ?>

                                </button>
                                <input type="file" id="finput-<?php echo e($question->id); ?>"
                                    name="media[<?php echo e($question->id); ?>][]"
                                    accept="<?php echo e($question->type->value == 'photo' ? 'image/*' : 'video/*'); ?>"
                                    multiple style="display:none"
                                    onchange="previewFiles(this, '<?php echo e($question->id); ?>')">
                            </div>
                            <?php break; ?>
                    <?php endswitch; ?>

                    
                    <?php if($isScored && $question->max_score > 0 && !in_array(is_object($question->type) ? $question->type->value : $question->type, ['text', 'photo', 'video'])): ?>
                        <input type="hidden" name="answers[<?php echo e($question->id); ?>][score]" class="score-input" value="<?php echo e($existing?->score ?? ''); ?>" data-weight="<?php echo e($question->weight); ?>" data-max-score="<?php echo e($question->max_score); ?>" id="score-<?php echo e($question->id); ?>">
                    <?php endif; ?>

                    
                    <div class="wq-remarks">
                        <input type="text" name="answers[<?php echo e($question->id); ?>][remarks]" class="form-control" placeholder="<?php echo e($lang === 'ar' ? '💬 ملاحظات...' : '💬 Remarks...'); ?>" value="<?php echo e($existing?->remarks ?? ''); ?>">
                    </div>
                </div>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        
        <div class="wizard-nav">
            <?php if($sIdx > 0): ?>
                <button type="button" class="btn btn-secondary btn-lg" onclick="goToStep(<?php echo e($sIdx - 1); ?>)">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="<?php echo e($lang === 'ar' ? '9 18 15 12 9 6' : '15 18 9 12 15 6'); ?>"/></svg>
                    <?php echo e($lang === 'ar' ? 'السابق' : 'Previous'); ?>

                </button>
            <?php else: ?>
                <div></div>
            <?php endif; ?>

            <div class="wizard-nav-center">
                <span class="text-muted" style="font-size:.82rem"><?php echo e($lang === 'ar' ? 'الخطوة' : 'Step'); ?> <?php echo e($sIdx + 1); ?> <?php echo e($lang === 'ar' ? 'من' : 'of'); ?> <?php echo e($totalSections); ?></span>
            </div>

            <?php if($sIdx < $totalSections - 1): ?>
                <button type="button" class="btn btn-primary btn-lg" onclick="goToStep(<?php echo e($sIdx + 1); ?>)">
                    <?php echo e($lang === 'ar' ? 'التالي' : 'Next'); ?>

                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="<?php echo e($lang === 'ar' ? '15 18 9 12 15 6' : '9 18 15 12 9 6'); ?>"/></svg>
                </button>
            <?php else: ?>
                <button type="submit" class="btn btn-success btn-lg">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    <?php echo e($lang === 'ar' ? 'إرسال وإنهاء الفحص' : 'Submit & Complete'); ?>

                </button>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</form>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('modals'); ?>
<script src="<?php echo e(asset('js/inspection-wizard.js')); ?>"></script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\vis\resources\views/inspections/conduct.blade.php ENDPATH**/ ?>