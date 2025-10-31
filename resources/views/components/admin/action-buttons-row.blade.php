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
        :title="__('admin.view_item', ['item' => $itemName])"
        variant="icon"
    />
    
    <x-admin.button-edit 
        :route="$editRoute" 
        :permission="$editPermission" 
        :title="__('admin.edit_item', ['item' => $itemName])"
        variant="icon"
    />
    
    <x-admin.button-delete 
        :route="$destroyRoute" 
        :permission="$destroyPermission" 
        :title="__('admin.delete_item', ['item' => $itemName])"
        :confirmText="$confirmText"
        variant="icon"
    />
</div>
