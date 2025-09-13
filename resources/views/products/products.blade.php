@extends('layouts.main')
@section('title', '- Products')
@section('main-content')
<div class="main_container_section">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="content-area">
                    <div class="button-heading-box">
                        <div class="head-box">
                            <h1>Products</h1>
                        </div>
                        <div class="add-members-btn-box">
                            <select class="form-select select-boxes" id="product_filter" style="width: 332px;">
                                <option value="">Select Clients</option>
                                @foreach ($clients as $key => $client)
                                <option value="{{ $key }}" @if($selected_client_id==$key) selected @endif>{{ $client }}
                                </option>
                                @endforeach
                            </select>
                            <button class="comman-btn" id="searchBtn">Search</button>
                            <button class="border-comman-btn" id="resetBtn">Reset</button>
                            <div class="search-icons-client">
                                <img class="" src="{{ asset('/assets/images/search-icon.svg') }}" alt="back-icon" />
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <div class="main-table">
                            <table class="table table-bordered table-custom-css-box" id="product-list-table">
                                <thead>
                                    <tr>
                                        <th class="sr-no">S.No.</th>
                                        <th class="pro-width-more">Product Code</th>
                                        <th class="pro-width">Client Name</th>
                                        <th class="pro-width"> Start Date</th>
                                        @can('change-product-due-date-products')
                                        <th class="pro-width">Due Date</th>
                                        @endcan
                                        @can('assign-product-dropdown-products')
                                        <th class="pro-width-more">Assigned To</th>
                                        @endcan
                                        @can('change-product-status-dropdown-products')
                                        <th class="pro-width">Status</th>
                                        @endcan
                                        @if (auth()->user()->can('edit-product-button-products')
                                        ||auth()->user()->can('view-product-comment-button-products'))
                                        <th class="pro-width">Action</th>
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
                    $('#product-list-table').DataTable().ajax.reload();
                });
                $('#resetBtn').on('click', function() {
                    window.location.href = "{{url()->current()}}";
                    $('#product_filter').val('').trigger('change');
                    $('#product-list-table').DataTable().ajax.reload();
                });

                $('#product-list-table').DataTable({
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
                        url: "{{ route('product.lists') }}",
                        type: "POST",
                        data: function(d) {
                            d.client_id = $('#product_filter').val();
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
                        },
                        {
                            data: 'client_name',
                            name: 'client_name',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'start_date',
                            name: 'start_date',
                            orderable: false,
                            searchable: false
                        },
                        @can('change-product-due-date-products')
                        {
                            data: 'due_date',
                            name: 'due_date',
                            orderable: false,
                            searchable: false
                        },
                        @endcan
                        @can('assign-product-dropdown-products')
                        {
                            data: 'assigned_member',
                            name: 'assigned_member',
                            orderable: false,
                            searchable: false
                        },
                        @endcan
                        @can('change-product-status-dropdown-products')  
                        {
                            data: 'product_status',
                            name: 'product_status',
                            orderable: false,
                            searchable: false
                        },
                        @endcan
                        @if (auth()->user()->can('edit-product-button-products') ||auth()->user()->can('view-product-comment-button-products'))
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        },
                        @endcan
                    ],
                    drawCallback: function() {
                        $('.custom-dropdown').select2({
                            placeholder: 'Select Members',
                            allowClear: true,
                            width: 'resolve',
                            multiple: true,
                        });

                        bindDropdownListeners();
                        initDatePickers();
                    },
                });
            });
</script>
<script>
    function bindDropdownListeners() {
                // Change Assignee
                $('.assigned_member').off('change').on('change', function() {
                    let productId = $(this).data('product-id');
                    let currentUser = $(this).data('assined-by');
                    let memberIds = $(this).val();
                    $.ajax({
                        url: base_url + '/assign-product',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            product_id: productId,
                            assigned_members: memberIds,
                            assigned_by: currentUser,
                        },
                        success: function(response) {
                            if (response.status) {
                                toastr.success(response.message);
                                $('#product-list-table').DataTable().ajax.reload();
                                $('.assigned_member').select2({
                                    closeOnSelect: false
                                });
                            } else {
                                toastr.error(response.message);
                            }
                        },
                        error: function(xhr) {
                            toastr.error('Error while updating member');
                        }
                    });
                });

                // Change the Product Status
                $('.product_status').off('change').on('change', function() {
                    let productId = $(this).data('product-id');
                    let currentUser = $(this).data('change-by');
                    let statusValue = $(this).val();
                    $.ajax({
                        url: base_url + '/change-product-status',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            product_id: productId,
                            product_status: statusValue,
                            status_changed_by: currentUser,
                        },
                        success: function(response) {
                            if (response.status) {
                                toastr.success(response.message);
                                $('#product-list-table').DataTable().ajax.reload();
                            } else {
                                toastr.error(response.message);
                            }
                        },
                        error: function(xhr) {
                            toastr.error('Error while updating member');
                        }
                    });
                });
            }
            // Bind after DataTable is drawn
            // $('#product-list-table').on('draw.dt', function () {
            //     bindDropdownListeners();
            // });

            function initDatePickers() {
                $('.due-date-picker').flatpickr({
                    dateFormat: 'd-m-Y',
                    allowInput: true,
                    minDate: "today",
                    onChange: function(selectedDates, dateStr, instance) {
                        const productId = $(instance.element).data('product-id');
                        $.ajax({
                            url: '{{ route('update.product.duedate') }}',
                            type: 'POST',
                            data: {
                                productId: productId,
                                due_date: dateStr,
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.status) {
                                    toastr.success(response.message);
                                    $('#product-list-table').DataTable().ajax.reload();
                                } else {
                                    toastr.error(response.message);
                                }
                            },
                            error: function(err) {
                                toastr.error('Error while updating due date');
                            }
                        });
                    }
                });
            }

            function updateStatusColor(select) {
                const value = select.value;
                select.style.backgroundColor = '';
                switch (value) {
                    case '0':
                        select.style.backgroundColor = '#D57C7C'; // Red
                        break;
                    case '1':
                        select.style.backgroundColor = '#709CC2'; // Blue
                        break;
                    case '2':
                        select.style.backgroundColor = '#CFB55E'; // Yellow
                        break;
                    case '3':
                        select.style.backgroundColor = '#76BF97'; // Green
                        break;
                }
            }

            // Apply color on page load
            $(document).on('draw.dt', function() {
                document.querySelectorAll('.product_status').forEach(updateStatusColor);
            });
</script>
@endpush
@endsection