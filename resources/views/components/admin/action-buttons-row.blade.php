@props([
    'showRoute',
    'editRoute',
    'destroyRoute',
    'showPermission',
    'editPermission',
    'destroyPermission',
    'itemName',
    'confirmText',
])

<div class="btn-group actions" role="group">
    <x-admin.button-view 
        :route="$showRoute" 
        :permission="$showPermission" 
        :title="'View ' . $itemName"
        variant="icon"
    />
    
    <x-admin.button-edit 
        :route="$editRoute" 
        :permission="$editPermission" 
        :title="'Edit ' . $itemName"
        variant="icon"
    />
    
    <x-admin.button-delete 
        :route="$destroyRoute" 
        :permission="$destroyPermission" 
        :title="'Delete ' . $itemName"
        :confirmText="$confirmText"
        variant="icon"
    />
</div>

