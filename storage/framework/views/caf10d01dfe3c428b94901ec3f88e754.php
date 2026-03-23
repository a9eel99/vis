<?php $lang = app()->getLocale(); ?>

<div id="note-modal" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.5);backdrop-filter:blur(3px);align-items:center;justify-content:center" onclick="if(event.target===this)closeNoteModal()">
    <div class="card" style="max-width:440px;width:90%;box-shadow:0 20px 60px rgba(0,0,0,.3)">
        <div class="card-header" style="display:flex;justify-content:space-between;align-items:center">
            <h3 style="margin:0">📝 <?php echo e($lang === 'ar' ? 'ملاحظة الدفع' : 'Payment Note'); ?></h3>
            <button type="button" onclick="closeNoteModal()" style="background:none;border:none;font-size:1.2rem;cursor:pointer;color:var(--gray-500)">&times;</button>
        </div>
        <div class="card-body">
            <div style="display:flex;gap:1rem;margin-bottom:1rem;font-size:.82rem;color:var(--gray-500)">
                <div>
                    <div style="font-size:.7rem;color:var(--gray-400)"><?php echo e($lang === 'ar' ? 'الرقم المرجعي' : 'Reference'); ?></div>
                    <div id="note-ref" class="font-mono" style="font-weight:600;color:var(--gray-800)"></div>
                </div>
                <div>
                    <div style="font-size:.7rem;color:var(--gray-400)"><?php echo e($lang === 'ar' ? 'المبلغ' : 'Amount'); ?></div>
                    <div id="note-amount" style="font-weight:700;color:var(--success)"></div>
                </div>
                <div>
                    <div style="font-size:.7rem;color:var(--gray-400)"><?php echo e($lang === 'ar' ? 'التاريخ' : 'Date'); ?></div>
                    <div id="note-date" style="font-weight:500"></div>
                </div>
            </div>
            <div style="background:var(--gray-50);border:1px solid var(--gray-200);border-radius:8px;padding:12px 16px;font-size:.9rem;line-height:1.7;white-space:pre-wrap;word-break:break-word;min-height:60px;color:var(--gray-800)" id="note-text"></div>
        </div>
    </div>
</div>

<script>
function viewNote(btn) {
    document.getElementById('note-ref').textContent = btn.dataset.ref;
    document.getElementById('note-amount').textContent = btn.dataset.amount + ' <?php echo e($lang === "ar" ? "د.أ" : "JOD"); ?>';
    document.getElementById('note-date').textContent = btn.dataset.date;
    document.getElementById('note-text').textContent = btn.dataset.note;
    document.getElementById('note-modal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeNoteModal() {
    document.getElementById('note-modal').style.display = 'none';
    document.body.style.overflow = '';
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeNoteModal();
});
</script><?php /**PATH C:\xampp\htdocs\vis\resources\views/partials/note-modal.blade.php ENDPATH**/ ?>