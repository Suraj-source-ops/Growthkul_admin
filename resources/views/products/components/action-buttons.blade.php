<div class="action-btn-box">
    @can('edit-product-button-products')    
    <a href="{{ route('edit.product.details', ['slug' => $row->slug, 'type' => $row->product_type]) }}"
        class="edit-comman-btn">Edit</a>
    @endcan
    @can('view-product-comment-button-products') 
    <a href="{{route('view.product.details', ['slug' => $row->slug, 'type' => $row->product_type]) }}"
        class="edit-comman-btn">Comment</a>
    @endcan
</div>