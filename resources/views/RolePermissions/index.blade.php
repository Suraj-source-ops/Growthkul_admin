@extends('layouts.main')
@section('title', '- Role Lists')
@section('main-content')


<div class="main_container_section">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="content-area">
                    <div class="button-heading-box">
                        <div class="head-box">
                            <h1>Roles</h1>
                        </div>
                        @can('add-role-button-roles')
                        <div class="add-members-btn-box">
                            <a href="{{ route('add.role') }}" class="comman-btn">Add Role</a>
                        </div>
                        @endcan
                    </div>
                    <div class="main-table">
                        <table class="table table-bordered table-custom-css-box" id="roles-permission-table">
                            <thead>
                                <tr>
                                    <th>S.No.</th>
                                    <th>Roles</th>
                                    <th>Permissions</th>
                                    @can('edit-role-button-roles')
                                    <th>Action</th>
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

        $('#roles-permission-table').DataTable({
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
                url: "{{ route('roles') }}",
                type: "POST",
            },
            language: {
                searchPlaceholder: "Search roles...",
                paginate: {
                    previous: '<i class="fas fa-angle-left"></i>',
                    next: '<i class="fas fa-angle-right"></i>' 
                }
            },
            initComplete: function() {
                let $dataTableFilter = $('#roles-permission-table_filter');
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
                {
                    data: 'permissions',
                    name: 'permissions',
                    orderable: false,
                    searchable: false
                },
                @can('edit-role-button-roles')
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
</script>
@endpush
@endsection