@extends('layouts.main')
@section('title', '- Clients')
@section('main-content')
<div class="main_container_section">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="content-area">
                    <div class="button-heading-box">
                        <div class="head-box">
                            <h1>Clients</h1>
                        </div>
                        @can('add-client-button-clients')
                        <div class="add-members-btn-box">
                            <a href="{{ route('add.client') }}" class="comman-btn">Add Client</a>
                        </div>
                        @endcan
                    </div>
                    <div class="main-table">
                        <table class="table table-bordered table-custom-css-box" id="clients-table">
                            <thead>
                                <tr>
                                    <th>S.No.</th>
                                    <th>Client Name</th>
                                    <th>Total Product</th>
                                    @can('change-status-button-clients')
                                    <th>Active/Inactive</th>
                                    @endcan
                                    @if (auth()->user()->can('edit-client-button-clients') ||
                                    auth()->user()->can('view-client-detail-button-clients') ||
                                    auth()->user()->can('add-client-product-button-clients'))
                                    <th>Action</th>
                                    @endif
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
@push('scripts')
<script>
    $(document).ready(function() {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $('#clients-table').DataTable({
                    processing: true,
                    serverSide: true,
                    bFilter: true,
                    pageLength: 10,
                    lengthMenu: [
                        [5, 10, 25, 50],
                        [5, 10, 25, 50]
                    ],
                     responsive: true,
                     scrollX: true,
                    ajax: {
                        url: "{{ route('clients') }}",
                        type: "POST",
                    },
                    language: {
                        // searchPlaceholder: "Search clients...",
                        // search: "",
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
                            name: 'name',
                            searchable: true
                        },
                        {
                            data: 'products_count',
                            name: 'products_count',
                            orderable: false,
                            searchable: false
                        },
                        @can('change-status-button-clients')  
                        {
                            data: 'status',
                            name: 'status',
                            orderable: false,
                            searchable: false
                        },
                        @endcan
                        @if (auth()->user()->can('edit-client-button-clients') ||
                            auth()->user()->can('view-client-detail-button-clients') ||
                            auth()->user()->can('add-client-product-button-clients'))
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        }
                        @endcan
                    ],
                    initComplete: function() {
                        let $dataTableFilter = $('#clients-table_filter');
                        let $input = $dataTableFilter.find('input');

                        $input
                            .attr('placeholder', 'Search roles...')
                            .addClass('my-search-box')
                            .wrap('<div class="datatable-search-wrapper position-relative d-inline-block"></div>');

                        $dataTableFilter.find('.datatable-search-wrapper').prepend(`
                            <span class="datatable-search-icon">
                                <img class="search-imgs" src="{{ asset('/assets/images/search-icon.svg') }}" alt="search-icon" style="width:16px;height:16px;">
                            </span>
                        `);
                    },
                });
            });


            // active/inactive member
            function activeOrInactiveClient(id) {
                $.ajax({
                    url: base_url + `/clients-status/${id}`,
                    type: 'GET',
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (!response.status) {
                            toastr.error(response.message);
                            $('#clients-table').DataTable().ajax.reload();
                        } else {
                            $('#clients-table').DataTable().ajax.reload();
                            toastr.success(response.message);
                        }
                    },
                    error: function(xhr) {
                        toastr.error('Error while updating client status.');
                    }
                });
            }
</script>
@endpush
@endsection