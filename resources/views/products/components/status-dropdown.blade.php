@can('change-product-status-dropdown-products')    
<select name="product_status" class="product_status select-boxes" style="width: 50%" data-product-id={{$row->id}}
    data-change-by={{$user_id}} onchange="updateStatusColor(this)">
    <option value="0" {{$row->product_status == 0 ? 'selected' : 0}} {{ in_array($row->product_status, [1, 2, 3]) ?
        'disabled' : '' }}>Pending</option>
    <option value="1" {{$row->product_status == 1 ? 'selected' : 0}} {{ in_array($row->product_status, [0]) ?
        'disabled' : '' }}>In Progress</option>
    <option value="2" {{$row->product_status == 2 ? 'selected' : 0}} {{ in_array($row->product_status, [0]) ?
        'disabled' : '' }}>On Hold</option>
    <option value="3" {{$row->product_status == 3 ? 'selected' : 0}} {{ in_array($row->product_status, [0]) ?
        'disabled' : '' }}>Completed</option>
</select>
@endcan