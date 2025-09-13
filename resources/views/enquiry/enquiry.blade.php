@extends('layouts.main')
@section('title', '- Enquiry')
@section('main-content')
    <div class="main_container_section">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="content-area">
                        <div class="button-heading-box">
                            <div class="head-box">
                                <h1>Enquiries</h1>
                            </div>
                        </div>
                        <div class="main-table">
                            <table class="table table-bordered table-custom-css-box" id="enquiry-table">
                                <thead>
                                    <tr>
                                        <th>S.No.</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Is Subscribed</th>
                                        <th>Source</th>
                                        <th>Description</th>
                                        <th>Created At</th>
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

                $('#enquiry-table').DataTable({
                    processing: true,
                    serverSide: true,
                    bFilter: true,
                    pageLength: 10,
                    lengthMenu: [
                        [5, 10, 25, 50, 100, 500],
                        [5, 10, 25, 50, 100, 500]
                    ],
                    responsive: true,
                    scrollX: true,
                    ajax: {
                        url: "{{ route('enquiry') }}",
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
                            searchable: true
                        },
                        {
                            data: 'email',
                            name: 'email',
                            orderable: false,
                            searchable: true
                        },
                        {
                            data: 'phone',
                            name: 'phone',
                            orderable: false,
                            searchable: true
                        },
                        {
                            data: 'is_subscribed',
                            name: 'is_subscribed',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'source',
                            name: 'source',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'description',
                            name: 'description',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'created_at',
                            name: 'created_at',
                            orderable: false,
                            searchable: false
                        },
                    ],
                    initComplete: function() {
                        let $dataTableFilter = $('#enquiry-table_filter');
                        let $input = $dataTableFilter.find('input');

                        $input
                            .attr('placeholder', 'Search enquiry...')
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
        </script>
    @endpush
@endsection
