@extends('layouts.main')
@section('title', '- Product\'s Graphic Type')
@section('main-content')

<div class="main_container_section">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="content-area">
                    <div class="button-heading-box">
                        <div class="head-box">
                            <h1>Product's Graphic Type</h1>
                        </div>
                        @can('add-graphic-types-button-graphictypes')
                        <div class="add-members-btn-box">
                            <button data-toggle="modal" data-target="#graphicModal" class="comman-btn">Add
                                Graphic Type</button>
                        </div>
                        @endcan
                    </div>
                    <div class="main-table">
                        <div class="">
                            <table class="table table-bordered table-custom-css-box" id="graphic-table">
                                <thead>
                                    <tr>
                                        <th style="width:7%" scope="col">S. No.</th>
                                        <th style="width:85%" scope="col">Graphic Type Name</th>
                                        @can('delete-graphic-types-button-graphictypes')
                                        <th style="width:8%" scope="col">Action</th>
                                        @endcan
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="graphicModal" tabindex="-1" role="dialog" aria-labelledby="graphicModalTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content modal-box">
            <div class="modal-header">
                <h5 class="modal-title modal-product-type-title" id="exampleModalLongTitle">Add Product's Graphic Type</h5>
                <button type="button" class="close cross-btn" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{route('add.graphic.product')}}" method="POST" id="graphicProductTypeForm" autocomplete="off">
                @csrf
                <div class="modal-body product-modal-body">
                    <div class="produts-input">
                        <div class="input_box">
                            <label for="">Graphic Type Name</label>
                            <input type="text" name="name" placeholder="Enter Product Type">
                        </div>

                    </div>
                </div>
                <div class="modal-footer product-modal-footer">
                    <button type="submit" class="comman-btn">+ Add</button>
                </div>
            </form>
        </div>
    </div>
</div>
@push('scripts')
<script>
    $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $('#graphic-table').DataTable({
                processing: true,
                serverSide: true,
                bFilter: false,
                responsive: true,
                bPaginate: false,
                 scrollX: true,
                ajax: {
                    url: "{{ route('graphic.product.types') }}",
                    type: "POST",
                },
                language: {
                    paginate: {
                        previous: '<i class="fas fa-angle-left"></i>',
                        next: '<i class="fas fa-angle-right"></i>' 
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    @can('delete-graphic-types-button-graphictypes')
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                    @endcan
                ],
            });
        });

    function deleteGraphicProductType(id) {
        if (confirm('Are you sure you want to delete this product type?')) {
            $.ajax({
                url: base_url + `/delete-graphic-type/${id}`,
                type: 'get',
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    $('#graphic-table').DataTable().ajax.reload();
                    if (response.status === false) {
                        toastr.error(response.message);
                        return;
                    } else if (response.status === true) {
                        toastr.success(response.message);
                        return;
                    }
                },
                error: function(xhr) {
                    toastr.error('Error while deleting product\'s graphic type.');
                }
            });
        }
    }
</script>
@endpush
@endsection