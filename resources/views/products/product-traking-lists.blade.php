@extends('layouts.main')
@section('title', '- Product Tracking')
@section('main-content')
    <div class="main_container_section">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="content-area">
                        <div class="button-heading-box">
                            <div class="head-box">
                                <h1>Product Tracking</h1>
                            </div>
                            <div class="add-members-btn-box">
                                <select class="form-select select-boxes" id="product_filter_tracking" style="width: 332px;">
                                    <option value="">Select Client</option>
                                    @foreach ($clients as $key => $client)
                                        <option value="{{ $key }}">{{ $client }}</option>
                                    @endforeach
                                </select>
                                <button class="comman-btn" id="searchBtn">Search</button>
                                <button class="border-comman-btn" id="resetBtn">Reset</button>
                            </div>

                        </div>
                        <div class="table-responsive">
                            <div class="main-table">
                                <table class="table table-bordered table-custom-css-box" id="product-track-list-table">
                                    <thead>
                                        <tr>
                                            <th>S.No.</th>
                                            <th class="pro-width-more">Product Code</th>
                                            <th class="pro-width">Client Name</th>
                                            <th class="pro-width-more">Stage 1</th>
                                            <th class="pro-width-more">Stage 2</th>
                                            <th class="pro-width-more">Stage 3</th>
                                            <th class="pro-width">Action</th>
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
    </div>
    @push('scripts')
        <script>
            $(document).ready(function() {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $('#searchBtn').on('click', function() {
                    $('#product-track-list-table').DataTable().ajax.reload();
                });
                $('#resetBtn').on('click', function() {
                    $('#product_filter_tracking').val('').trigger('change');
                    $('#product-track-list-table').DataTable().ajax.reload();
                });

                $('#product-track-list-table').DataTable({
                    processing: true,
                    serverSide: true,
                    bFilter: true,
                    pageLength: 10,
                    scrollX: true,
                    lengthMenu: [
                        [5, 10, 25, 50],
                        [5, 10, 25, 50]
                    ],
                    responsive: true,
                    ajax: {
                        url: "{{ route('product.track.lists') }}",
                        type: "POST",
                        data: function(d) {
                            d.client_id = $('#product_filter_tracking').val();
                        }
                    },
                    language: {
                        searchPlaceholder: "Search...",
                        search: "",
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
                            data: 'product_code',
                            name: 'product_code',
                            orderable: false,
                            searchable: true
                        },
                        {
                            data: 'client_name',
                            name: 'client_name',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'stage_first',
                            name: 'stage_first',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'stage_second',
                            name: 'stage_second',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'stage_third',
                            name: 'stage_third',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        },
                    ],
                    initComplete: function() {
                        let $dataTableFilter = $('#product-track-list-table_filter');
                        let $input = $dataTableFilter.find('input');

                        $input
                            .attr('placeholder', 'Search...')
                            .addClass('my-search-box')
                            .wrap(
                                '<div class="datatable-search-wrapper position-relative d-inline-block"></div>'
                            );

                        $dataTableFilter.find('.datatable-search-wrapper').prepend(`
                            <span class="datatable-search-icon">
                                <img class="search-imgs" src="{{ asset('/assets/images/search-icon.svg') }}" alt="search-icon" style="width:16px;height:16px;">
                            </span>
                        `);
                    }
                });
            });

            // active/inactive status
            function changeStatus(id) {
                $.ajax({
                    url: base_url + `/change-stage-status/${id}`,
                    type: 'GET',
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (!response.status) {
                            toastr.error(response.message);
                            $('#product-track-list-table').DataTable().ajax.reload();
                        } else {
                            $('#product-track-list-table').DataTable().ajax.reload();
                            toastr.success(response.message);
                        }
                    },
                    error: function(xhr) {
                        toastr.error('Error while updating product stage status.');
                    }
                });
            }
        </script>
    @endpush
@endsection
