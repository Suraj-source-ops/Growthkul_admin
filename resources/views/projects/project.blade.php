@extends('layouts.main')
@section('title', '- Projects')
@section('main-content')
    <div class="main_container_section">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="content-area">
                        <div class="button-heading-box">
                            <div class="head-box">
                                <h1>Projects</h1>
                            </div>
                            @can('add-project-button-projects')
                                <div class="add-members-btn-box">
                                    <a href="{{ route('add.project') }}" class="comman-btn">Add Project</a>
                                </div>
                            @endcan
                        </div>
                        <div class="main-table">
                            <table class="table table-bordered table-custom-css-box" id="project-table">
                                <thead>
                                    <tr>
                                        <th>S.No.</th>
                                        <th>Project Name</th>
                                        <th>Project Title</th>
                                        <th>Project Image</th>
                                        @can('change-status-button-projects')
                                            <th>Active/Inactive</th>
                                        @endcan
                                        <th>URL</th>
                                        @if (auth()->user()->can('edit-project-button-projects') || auth()->user()->can('delete-project-button-projects'))
                                        @endif
                                        <th>Action</th>
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

                $('#project-table').DataTable({
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
                        url: "{{ route('projects') }}",
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
                            name: 'name',
                            orderable: false,
                            searchable: true
                        },

                        {
                            data: 'title',
                            name: 'title',
                            orderable: false,
                            searchable: true
                        },
                        {
                            data: 'image',
                            name: 'image',
                            searchable: false,
                            orderable: false
                        },
                        @can('change-status-button-projects')
                            {
                                data: 'status',
                                name: 'status',
                                orderable: false,
                                searchable: false
                            },
                        @endcan {
                            data: 'project_url',
                            name: 'project_url',
                            orderable: false,
                            searchable: false
                        },
                        @if (auth()->user()->can('edit-project-button-projects') || auth()->user()->can('delete-project-button-projects'))
                            {
                                data: 'action',
                                name: 'action',
                                orderable: false,
                                searchable: false
                            }
                        @endcan
                    ],
                    initComplete: function() {
                        let $dataTableFilter = $('#project-table_filter');
                        let $input = $dataTableFilter.find('input');

                        $input
                            .attr('placeholder', 'Search projects...')
                            .addClass('my-search-box')
                            .wrap(
                                '<div class="datatable-search-wrapper position-relative d-inline-block"></div>'
                            );

                        $dataTableFilter.find('.datatable-search-wrapper').prepend(`
                            <span class="datatable-search-icon">
                                <img class="search-imgs" src="{{ asset('/assets/images/search-icon.svg') }}" alt="search-icon" style="width:16px;height:16px;">
                            </span>
                        `);
                    },
                });
            });


            // active/inactive member
            function activeOrInactiveProjects(id) {
                $.ajax({
                    url: base_url + `/projects-status/${id}`,
                    type: 'GET',
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (!response.status) {
                            toastr.error(response.message);
                            $('#project-table').DataTable().ajax.reload();
                        } else {
                            $('#project-table').DataTable().ajax.reload();
                            toastr.success(response.message);
                        }
                    },
                    error: function(xhr) {
                        toastr.error('Error while updating project status.');
                    }
                });
            }
        </script>
    @endpush
@endsection
