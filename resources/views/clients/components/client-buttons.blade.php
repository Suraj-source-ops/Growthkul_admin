<div class="action-btn-box">
    @can('edit-client-button-clients')
    <a href="{{route('edit.client.details', $row->clientid)}}" class="edit-comman-btn">Edit</a>
    @endcan
    @can('view-client-detail-button-clients')
    <a href="{{route('client.details', $row->clientid)}}" class="edit-comman-btn">View</a>
    @endcan
    @can('add-client-product-button-clients')
    <a href="{{route('add.product', $row->clientid)}}" class="edit-comman-btn">Add Products</a>
    @endcan
</div>