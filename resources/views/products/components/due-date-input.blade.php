@can('change-product-due-date-products')    
<div class="ui calendar">
    <div class="ui input left icon">
        <i class="calendar icon"></i>
        <input type="text" class="due-date-picker" value="{{ $due_date }}" data-product-id="{{ $product_id }}"
            placeholder="--/--/----" style="width: 85px; padding: 3px; border: 1px solid #ccc; border-radius: 4px;" />
    </div>
</div>
@endcan