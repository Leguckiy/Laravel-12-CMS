@props([
    'editRoute',
    'destroyRoute',
    'backRoute',
    'editPermission',
    'destroyPermission',
    'itemName',
    'confirmText',
])

<div class="d-flex gap-2">
    <x-admin.button-edit 
        :route="$editRoute" 
        :permission="$editPermission" 
        :title="__('admin.edit_item', ['item' => $itemName])"
        variant="text"
    />
    
    <x-admin.button-delete 
        :route="$destroyRoute" 
        :permission="$destroyPermission" 
        :title="__('admin.delete_item', ['item' => $itemName])"
        :confirmText="$confirmText"
        variant="text"
    />
    
    <x-admin.button-back :route="$backRoute" />
</div>
