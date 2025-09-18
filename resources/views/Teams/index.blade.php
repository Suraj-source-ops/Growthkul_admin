@extends('layouts.main')
@section('title', '- Teams')
@section('main-content')
<div class="main_container_section">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="content-area">
                    <div class="button-heading-box">
                        <div class="head-box">
                            <h1>Teams</h1>
                        </div>
                        @can('add-team-button-teams')
                        <div class="add-team-btn">
                            <a class="comman-btn" href="{{ route('add.teams') }}">Add Team</a>
                        </div>
                        @endcan
                        <div class="search-icons">
                            <img class="" src="{{ asset('/assets/images/search-icon.svg') }}" alt="back-icon" />
                        </div>
                    </div>
                    <div class="main-table">
                        <table class="table table-bordered table-custom-css-box" id="members-table">
                            <thead>
                                <tr>
                                    <th style="width: 30%;">S.No</th>
                                    <th style="width: 30%;">Team Name</th>
                                    <th style="width: 30%;">Description</th>
                                    @can('delete-team-button-teams')
                                    <th style="width: 10%;">Action</th>
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
@push('scripts')

<script>
    $(document).ready(function() {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $('#searchBtn').on('click', function() {
                    $('#members-table').DataTable().ajax.reload();
                });

                $('#members-table').DataTable({
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
                        url: "{{ route('teams') }}",
                        type: "POST",
                        data: function(d) {
                            d.team_id = $('#team_filter').val();
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
                            data: 'name',
                            name: 'name'
                        },
                        {
                            data: 'description',
                            name: 'description'
                        },
                        
                        @can('delete-team-button-teams')
                        {
                            data: 'id',
                            orderable: false,
                            searchable: false,
                            render: function(data, type, row) {
                                return `
                        <div class="">
                        <a href="javascript:void(0);" onclick="deleteTeam(${data})" class="text-danger" title="Delete">
                            <i class="far fa-trash-alt tras-icons"></i>
                        </a>
                        </div>
                        `;
                            }
                        }
                        @endcan
                    ],
                    initComplete: function() {
                        $('#members-table_filter input')
                            .attr('placeholder', 'Search...')
                            .addClass('my-search-box'); // <-- your class here
                    }
                });
            });

            function deleteTeam(id) {
                if (confirm('Are you sure you want to delete this team?')) {
                    $.ajax({
                        url: base_url + `/delete-team/${id}`,
                        type: 'get',
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            $('#members-table').DataTable().ajax.reload();
                            if (response.status === false) {
                                toastr.error(response.message);
                                return;
                            } else if (response.status === true) {
                                toastr.success(response.message);
                                return;
                            }
                        },
                        error: function(xhr) {
                            toastr.error('Error while deleting team.');
                        }
                    });
                }
            }
</script>
@endpush
@endsection