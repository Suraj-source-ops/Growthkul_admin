@extends('layouts.main')
@section('title', '- Tasks')
@section('main-content')
    <div class="main_container_section">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="content-area">
                        <div class="button-heading-box">
                            <div class="head-box">
                                <h1>Tasks</h1>
                            </div>
                            <div class="button-box">
                                <button class="comman-btn" id="searchBtn">Search</button>
                                <button class="border-comman-btn" id="resetBtn">Reset</button>
                            </div>
                        </div>
                        <div class="task-filter mt-3">
                            <div class="add-members-btn-box">
                                <div class="input_box mb-0" style="width:20%;">
                                    <input type="text" placeholder="Search Product Code" id="task_product_code_filter">
                                </div>
                                <select class="form-select select-boxes" id="task_client_filter" style="width: 20%;">
                                    <option value="">Select Clients</option>
                                    @foreach ($clients as $key => $client)
                                        <option value="{{ $key }}">{{ $client }}</option>
                                    @endforeach
                                </select>
                                <select class="form-select select-boxes" id="task_team_filter" style="width: 20%;">
                                    <option value="">Select Teams</option>
                                    @foreach ($teams as $key => $team)
                                        <option value="{{ $key }}">{{ $team }}</option>
                                    @endforeach
                                </select>
                                <select class="form-select select-boxes" id="task_member_filter" style="width: 20%;">
                                    <option value="">Select Member</option>
                                    @foreach ($members as $key => $member)
                                        <option value="{{ $key }}">{{ $member }}</option>
                                    @endforeach
                                </select>
                                <select class="form-select select-boxes" id="task_status_filter" style="width: 20%;">
                                    <option value="">Select Status</option>
                                    @foreach ($status as $key => $stat)
                                        <option value="{{ $key }}">{{ $stat }}</option>
                                    @endforeach
                                </select>


                            </div>
                        </div>
                        <div class="main-table">
                            <table class="table table-bordered table-custom-css-box" id="task-list-table">
                                <thead>
                                    <tr>
                                        <th>S.No.</th>
                                        <th>Product Code</th>
                                        <th>Client Name</th>
                                        <th>Start Date</th>
                                        <th>Due Date</th>
                                        <th>Team</th>
                                        <th>Assigned To</th>
                                        <th>Status</th>
                                        @can('view-task-comment-button-tasks')
                                        <th>Comment</th>
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
    @push('scripts')
        <script>
            $(document).ready(function() {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $('#searchBtn').on('click', function() {
                    $('#task-list-table').DataTable().ajax.reload();
                });
                $('#resetBtn').on('click', function() {
                    $('#task_product_code_filter').val('');
                    $('#task_client_filter').val('').trigger('change');
                    $('#task_team_filter').val('').trigger('change');
                    $('#task_member_filter').val('').trigger('change');
                    $('#task_status_filter').val('').trigger('change');
                    $('#task-list-table').DataTable().ajax.reload();
                });

                $('#task-list-table').DataTable({
                    processing: true,
                    serverSide: true,
                    bFilter: false,
                    pageLength: 10,
                    scrollX: true,
                    lengthMenu: [
                        [5, 10, 25, 50],
                        [5, 10, 25, 50]
                    ],
                    responsive: true,
                    ajax: {
                        url: "{{ $route }}",
                        type: "POST",
                        data: function(d) {
                            d.productcode = $('#task_product_code_filter').val();
                            d.client_id = $('#task_client_filter').val();
                            d.team_id = $('#task_team_filter').val();
                            d.user_id = $('#task_member_filter').val();
                            d.status_id = $('#task_status_filter').val();
                        }
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
                            data: 'product_code',
                            name: 'product_code',
                            orderable: false,
                            searchable: false
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
                        {
                            data: 'due_date',
                            name: 'due_date',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'assigned_team',
                            name: 'assigned_team',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'assigned_member',
                            name: 'assigned_member',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'product_status',
                            name: 'product_status',
                            orderable: false,
                            searchable: false
                        },
                        @can('view-task-comment-button-tasks')
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
                            width: 'resolve',
                        });

                        bindDropdownListeners();
                    },
                    initComplete: function() {
                        let $dataTableFilter = $('#task-list-table_filter');
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
        </script>
        <script>
            function bindDropdownListeners() {
                // Change Assignee
                $('.task_assigned_member').off('change').on('change', function() {
                    let productId = $(this).data('product-id');
                    let currentUser = $(this).data('assined-by');
                    let memberId = $(this).val();
                    $.ajax({
                        url: base_url + '/tasks/assign-task',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            product_id: productId,
                            assigned_member: memberId,
                            assigned_by: currentUser,
                        },
                        success: function(response) {
                            if (response.status) {
                                toastr.success(response.message);
                                $('#task-list-table').DataTable().ajax.reload();
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
            }
        </script>
    @endpush
@endsection
