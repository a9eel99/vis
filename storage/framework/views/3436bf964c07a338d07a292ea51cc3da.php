<?php $lang = app()->getLocale(); ?>

<div id="pay-modal" style="display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.5);backdrop-filter:blur(3px);align-items:center;justify-content:center" onclick="if(event.target===this)closePayModal()">
    <div class="card" style="max-width:420px;width:90%;box-shadow:0 20px 60px rgba(0,0,0,.3)">
        <div class="card-header" style="display:flex;justify-content:space-between;align-items:center">
            <h3 style="margin:0">💵 <?php echo e($lang === 'ar' ? 'تسجيل الدفع' : 'Record Payment'); ?></h3>
            <button type="button" onclick="closePayModal()" style="background:none;border:none;font-size:1.2rem;cursor:pointer;color:var(--gray-500)">&times;</button>
        </div>
        <div class="card-body">
            <form id="pay-form" method="POST">
                <?php echo csrf_field(); ?>
                <div id="pay-ref" style="font-size:.85rem;color:var(--gray-500);margin-bottom:.75rem"></div>

                <div class="form-grid-2" style="margin-bottom:.75rem">
                    <div class="form-group" style="margin:0">
                        <label class="form-label" style="font-size:.8rem"><?php echo e($lang === 'ar' ? 'السعر' : 'Price'); ?></label>
                        <div id="pay-price" style="font-weight:700;font-size:1.1rem;color:var(--success)"></div>
                    </div>
                    <div class="form-group" style="margin:0">
                        <label class="form-label" style="font-size:.8rem"><?php echo e($lang === 'ar' ? 'خصم (د.أ)' : 'Discount'); ?></label>
                        <input type="number" name="discount" value="0" min="0" step="0.01" class="form-control" style="padding:6px 10px">
                    </div>
                </div>

                <label class="form-label" style="font-size:.8rem"><?php echo e($lang === 'ar' ? 'طريقة الدفع' : 'Payment Method'); ?></label>
                <div style="display:flex;gap:6px;flex-wrap:wrap;margin-bottom:.5rem">
                    <?php $__currentLoopData = [
                        ['💵', $lang === 'ar' ? 'كاش' : 'Cash'],
                        ['📱', 'CliQ'],
                        ['👛', $lang === 'ar' ? 'محفظة' : 'Wallet'],
                        ['🏦', $lang === 'ar' ? 'تحويل بنكي' : 'Bank Transfer'],
                    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as [$icon, $tag]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <button type="button" class="btn btn-secondary btn-sm" onclick="addPayTag('<?php echo e($tag); ?>')"><?php echo e($icon); ?> <?php echo e($tag); ?></button>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                <label class="form-label" style="font-size:.8rem"><?php echo e($lang === 'ar' ? 'ملاحظات الدفع' : 'Payment Notes'); ?></label>
                <textarea name="payment_note" id="pay-note" class="form-control" rows="3" style="resize:vertical" placeholder="<?php echo e($lang === 'ar' ? 'مثال: كاش — استلم المبلغ أحمد' : 'e.g., Cash — received by Ahmed'); ?>"></textarea>

                <div style="display:flex;gap:8px;margin-top:1rem">
                    <button type="submit" class="btn btn-success" style="flex:1">💵 <?php echo e($lang === 'ar' ? 'تأكيد القبض' : 'Confirm Payment'); ?></button>
                    <button type="button" class="btn btn-secondary" onclick="closePayModal()"><?php echo e($lang === 'ar' ? 'إلغاء' : 'Cancel'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openPayModal(id, ref, price) {
    document.getElementById('pay-form').action = '/finance/' + id + '/paid';
    document.getElementById('pay-ref').textContent = ref;
    document.getElementById('pay-price').textContent = price.toFixed(2) + ' <?php echo e($lang === "ar" ? "د.أ" : "JOD"); ?>';
    document.getElementById('pay-note').value = '';
    document.getElementById('pay-form').querySelector('[name=discount]').value = 0;
    document.getElementById('pay-modal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closePayModal() {
    document.getElementById('pay-modal').style.display = 'none';
    document.body.style.overflow = '';
}

function addPayTag(tag) {
    var note = document.getElementById('pay-note');
    var current = note.value.trim();
    if (current && !current.endsWith('—') && !current.endsWith('-')) {
        note.value = current + ' — ' + tag;
    } else if (current) {
        note.value = current + ' ' + tag;
    } else {
        note.value = tag;
    }
    note.focus();
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closePayModal();
});
</script><?php /**PATH C:\xampp\htdocs\vis\resources\views/partials/payment-modal.blade.php ENDPATH**/ ?>