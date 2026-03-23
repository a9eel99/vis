@php $lang = app()->getLocale(); @endphp
<div class="modal modal-sm" id="delete-modal">
    <div class="modal-body" style="padding:2rem">
        <div class="modal-delete-icon">🗑️</div>
        <div class="modal-delete-title">{{ $lang === 'ar' ? 'تأكيد الحذف' : 'Confirm Delete' }}</div>
        <div class="modal-delete-msg">
            {{ $lang === 'ar' ? 'هل أنت متأكد من حذف' : 'Are you sure you want to delete' }}
            <strong id="delete-item-name"></strong>{{ $lang === 'ar' ? '؟' : '?' }}
            <br>
            <small style="color:var(--danger)">{{ $lang === 'ar' ? 'لا يمكن التراجع عن هذا الإجراء' : 'This action cannot be undone' }}</small>
        </div>
    </div>
    <div class="modal-footer" style="justify-content:center">
        <button type="button" class="btn btn-secondary" onclick="closeModal('delete-modal')">{{ $lang === 'ar' ? 'إلغاء' : 'Cancel' }}</button>
        <form id="delete-confirm-form" method="POST" style="display:inline">
            @csrf
            <input type="hidden" name="_method" id="delete-method-field" value="DELETE">
            <button type="submit" class="btn btn-danger">{{ $lang === 'ar' ? 'نعم، احذف' : 'Yes, Delete' }}</button>
        </form>
    </div>
</div>
