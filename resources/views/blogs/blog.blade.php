@extends('layouts.main')
@section('title', '- Blogs')
@section('main-content')
    <div class="main_container_section">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="content-area">
                        <div class="button-heading-box">
                            <div class="head-box">
                                <h1>Blogs</h1>
                            </div>
                            <div class="add-members-btn-box">
                                <a href="{{ route('add.blog') }}" class="comman-btn">Add Blog</a>
                            </div>
                        </div>
                        <div class="main-table">
                            <table class="table table-bordered table-custom-css-box" id="blog-table">
                                <thead>
                                    <tr>
                                        <th>S.No.</th>
                                        <th>Author</th>
                                        <th>Blog Image</th>
                                        <th>Blog Title</th>
                                        <th>Published Date</th>
                                        <th>Active/Inactive</th>
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

                $('#blog-table').DataTable({
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
                        url: "{{ route('blogs') }}",
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
                            data: 'author',
                            name: 'author',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'image',
                            name: 'image',
                            searchable: false,
                            orderable: false
                        },
                        {
                            data: 'title',
                            name: 'title',
                            orderable: false,
                            searchable: true
                        },
                        {
                            data: 'created_at',
                            name: 'created_at',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'status',
                            name: 'status',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        }
                    ],
                    initComplete: function() {
                        let $dataTableFilter = $('#blog-table_filter');
                        let $input = $dataTableFilter.find('input');

                        $input
                            .attr('placeholder', 'Search blogs...')
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
            function activeOrInactiveBlog(id) {
                $.ajax({
                    url: base_url + `/blogs-status/${id}`,
                    type: 'GET',
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (!response.status) {
                            toastr.error(response.message);
                            $('#blog-table').DataTable().ajax.reload();
                        } else {
                            $('#blog-table').DataTable().ajax.reload();
                            toastr.success(response.message);
                        }
                    },
                    error: function(xhr) {
                        toastr.error('Error while updating blog status.');
                    }
                });
            }
        </script>
    @endpush
@endsection
