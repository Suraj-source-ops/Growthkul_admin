@extends('layouts.main')
@section('title', '- Team Members')
@section('main-content')


<div class="main_container_section">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="content-area">
                    <div class="button-heading-box">
                        <div class="head-box">
                            <h1>Members</h1>
                        </div>
                        <div class="add-members-btn-box">
                            <select class="form-select select-boxes" id="team_filter" style="width: 332px;">
                                <option value="">Select Team</option>
                                @foreach ($teams as $key => $team)
                                <option value="{{ $key }}">{{ $team }}</option>
                                @endforeach
                            </select>
                            <button class="comman-btn" id="searchBtn">Search</button>
                            <button class="border-comman-btn" id="resetMemberBtn">Reset</button>
                            @can('add-member-button-members')
                                <a href="{{ route('add.members') }}" class="anchor-comman-btn"
                                    style="height: 40px !important">Add Member</a>
                            @endcan
                        </div>
                    </div>
                    <div class="main-table">
                        <table class="table table-bordered table-custom-css-box" id="members-table">
                            <thead>
                                <tr>
                                    <th>S.No</th>
                                    <th>Member Name</th>
                                    <th>Email</th>
                                    <th>Team</th>
                                    @can('change-status-button-members')
                                    <th>Active / Inactive</th>
                                    @endcan
                                    @can('edit-member-button-members')
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

                $('#searchBtn').on('click', function() {
                    $('#members-table').DataTable().ajax.reload();
                });

                $('#resetMemberBtn').on('click', function() {
                    $('#team_filter').val('').trigger('change');
                    $('#members-table').DataTable().ajax.reload();
                });

                $('#members-table').DataTable({
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
                        url: "{{ route('members') }}",
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
                            name: 'name',
                            orderable: false,
                        },
                        {
                            data: 'email',
                            name: 'email',
                            orderable: false,
                        },
                        {
                            data: 'team',
                            name: 'team',
                            orderable: false,
                        },
                        @can('change-status-button-members')
                        {
                            data: 'status',
                            name: 'status',
                             orderable: false,
                        },
                        @endcan
                        @can('edit-member-button-members')
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        },
                        @endcan
                    ],
                });
            });


            // active/inactive member
            function activeOrInactiveMember(id) {
                $.ajax({
                    url: base_url + `/member-status/${id}`,
                    type: 'GET',
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (!response.status) {
                            toastr.error(response.message);
                            $('#members-table').DataTable().ajax.reload();
                        }else{
                            $('#members-table').DataTable().ajax.reload();
                            toastr.success(response.message);
                        }
                    },
                    error: function(xhr) {
                        toastr.error('Error while updating member status.');
                    }
                });
            }
</script>
@endpush
@endsection