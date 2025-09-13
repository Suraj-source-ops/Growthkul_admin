@extends('layouts.main')
@section('title', '- Edit Product details')
@section('main-content')

<div class="main_container_section">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="content-area">
                    <div class="button-heading-box">
                        <div class="head-box">
                            <a href="{{route('product.lists')}}">
                                <img class="" src="{{ asset('/assets/images/back.png') }}" alt="back-icon" />
                            </a>
                            <h1>Product Details <span class="product-name">({{$product->product_code}})</span></h1>
                        </div>
                        <div class="add-members-btn-box">
                            @if ($product->product_status == 0)
                            <button class="pending-btn" style="cursor: none">
                                Pending
                            </button>
                            @elseif($product->product_status == 1)
                            <button class="in-progress-btn" style="cursor: none">
                                In Progress
                            </button>
                            @elseif($product->product_status == 2)
                            <button class="on-hold-btn" style="cursor: none">
                                On Hold
                            </button>
                            @elseif($product->product_status == 3)
                            <button class="completed-btn" style="cursor: none">
                                Completed
                            </button>
                            @endif
                            @can('view-tracking-products')
                            <a href="{{route('product.stages',['productId' => $product->id])}}"
                                class="tracking-btn">Tracking</a>
                            @endcan
                        </div>
                    </div>
                    <div class="content-box">
                        <div class="products-tab-box">
                            <ul class="nav nav-pills hr-line">
                                <li class="nav-item">
                                    <a class="nav-link {{$product->product_type == 1 ? 'active' : ''}}"
                                        data-toggle="pill" href="#sizeChart" data-type="1">Size
                                        Chart</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{$product->product_type == 2 ? 'active' : ''}}"
                                        data-toggle="pill" href="#techPack" data-type="2">Tech
                                        Pack</a>
                                </li>
                            </ul>
                            <!-- Tab panes -->
                            <div class="tab-content tab-content-box">
                                <div id="sizeChart"
                                    class="tab-pane {{ $product->product_type == 1 ? 'active show' : 'fade' }}">
                                    {!!$sizechart!!}
                                </div>
                                <div id="techPack"
                                    class="tab-pane {{ $product->product_type == 2 ? 'active show' : 'fade' }}">
                                    {!!$techpack!!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
    $(document).ready(function () {
        $('a[data-toggle="pill"]').on('shown.bs.tab', function (e) {
            const tabType = $(e.target).data('type');
            const target = $(e.target).attr('href');
            const productSlug = "{{$product->slug}}"
            window.location.href = base_url + '/product/' + productSlug + '/edit-product-details?type=' + tabType;
        });
    });
</script>
{{-- Delete File --}}
<script>
    //delete uploaded file
    function deleteProductDocSizeChart(event, docid){
        event.preventDefault();
         if (!confirm('Are you sure you want to delete this file?')) return;
        $.ajax({
            url: base_url + '/api/delete-product-file/'+"{{ Auth::user()->id }}" + '/'+docid,
            type: 'GET',
            success: function(data) {
                if(data.status){
                    toastr.success(data.message);
                    $(`.uploaded-file-sizechart[data-doc-id="${docid}"]`).remove();
                } else {
                    toastr.error(data.message);
                }
            }
        });
    }
</script>

{{-- Delete File --}}
<script>
    //delete uploaded file tech pack
    function deleteProductDocTechPack(event, docid){
        event.preventDefault();
         if (!confirm('Are you sure you want to delete this file?')) return;
        $.ajax({
            url: base_url + '/api/delete-product-file/'+"{{ Auth::user()->id }}" + '/'+docid,
            type: 'GET',
            success: function(data) {
                if(data.status){
                    toastr.success(data.message);
                    $(`.uploaded-file-techpack[data-doc-id="${docid}"]`).remove();
                } else {
                    toastr.error(data.message);
                }
            }
        });
    }
</script>

@endpush
@endsection