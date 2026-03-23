<?php $lang = app()->getLocale(); ?>
<div class="modal modal-sm" id="delete-modal">
    <div class="modal-body" style="padding:2rem">
        <div class="modal-delete-icon">🗑️</div>
        <div class="modal-delete-title"><?php echo e($lang === 'ar' ? 'تأكيد الحذف' : 'Confirm Delete'); ?></div>
        <div class="modal-delete-msg">
            <?php echo e($lang === 'ar' ? 'هل أنت متأكد من حذف' : 'Are you sure you want to delete'); ?>

            <strong id="delete-item-name"></strong><?php echo e($lang === 'ar' ? '؟' : '?'); ?>

            <br>
            <small style="color:var(--danger)"><?php echo e($lang === 'ar' ? 'لا يمكن التراجع عن هذا الإجراء' : 'This action cannot be undone'); ?></small>
        </div>
    </div>
    <div class="modal-footer" style="justify-content:center">
        <button type="button" class="btn btn-secondary" onclick="closeModal('delete-modal')"><?php echo e($lang === 'ar' ? 'إلغاء' : 'Cancel'); ?></button>
        <form id="delete-confirm-form" method="POST" style="display:inline">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="_method" id="delete-method-field" value="DELETE">
            <button type="submit" class="btn btn-danger"><?php echo e($lang === 'ar' ? 'نعم، احذف' : 'Yes, Delete'); ?></button>
        </form>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\vis\resources\views/partials/delete-modal.blade.php ENDPATH**/ ?>