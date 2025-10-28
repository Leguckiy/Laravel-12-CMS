@props([
    'editRoute',
    'destroyRoute',
    'backRoute',
    'editPermission',
    'destroyPermission',
    'itemName',
    'confirmText',
])

<div class="mt-4">
    <x-admin.button-edit 
        :route="$editRoute" 
        :permission="$editPermission" 
        :title="'Edit ' . $itemName"
        variant="text"
    />
    
    <x-admin.button-delete 
        :route="$destroyRoute" 
        :permission="$destroyPermission" 
        :title="'Delete ' . $itemName"
        :confirmText="$confirmText"
        variant="text"
    />
    
    <x-admin.button-back :route="$backRoute" />
</div>

