@extends('layouts.main')
@section('title', '- Master Stage List')
@section('main-content')


<div class="main_container_section">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="content-area">
                    <div class="button-heading-box">
                        <div class="head-box">
                            <h1>Master Stages</h1>
                        </div>
                        @can('add-master-stage-button-masterstage')
                        <div class="add-members-btn-box">
                            <button class="comman-btn" data-toggle="modal" data-target="#stagesModalCenter">Add
                                Stages</button>
                        </div>
                        @endcan
                    </div>
                    <div class="main-table">
                        <div class="">
                            <table class="table table-bordered table-custom-css-box" id="stages-table">
                                <thead>
                                    <tr>
                                        <th style="width:7%" scope="col">S. No.</th>
                                        <th style="width:43%" scope="col">Name</th>
                                        <th style="width:40%" scope="col">Type</th>
                                        @can('delete-master-stage-button-masterstage')
                                        <th style="width:10%" scope="col">Action</th>
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
<!-- Modal -->
<div class="modal fade" id="stagesModalCenter" tabindex="-1" role="dialog" aria-labelledby="stagesModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content modal-box">
            <div class="modal-header">
                <h5 class="modal-title modal-product-type-title" id="exampleModalLongTitle">Add Master Stages</h5>
                <button type="button" class="close cross-btn" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('add.master.stage') }}" method="POST" id="masterStageForm" autocomplete="off">
                @csrf
                <div class="modal-body product-modal-body">
                    <div class="produts-input">
                        <div class="input_box">
                            <label for="">Stage Type</label>
                            <select name="stage_type" id="" class="select-boxes">
                                <option value="">Select Stage Type</option>
                                <option value="1">File</option>
                                <option value="2">Toggle Button</option>
                            </select>
                        </div>
                        <div class="input_box">
                            <label for="">Add Stage Name</label>
                            <input type="text" name="stage_name" placeholder="Enter Stage Name">
                        </div>
                    </div>
                </div>
                @can('add-master-stage-button-masterstage')
                <div class="modal-footer product-modal-footer">
                    <button type="submit" class="comman-btn">+ Add</button>
                </div>
                @endcan
            </form>
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
            var stageTable = $('#stages-table').DataTable({
                processing: true,
                serverSide: true,
                bFilter: false,
                responsive: true,
                scrollX: true,
                bPaginate: false,
                order: [[0, 'asc']],
                rowReorder: {
                    dataSrc: 'sequence',
                },
                ajax: {
                    url: "{{ route('master.stages') }}",
                    type: "POST",
                },
                language: {
                    paginate: {
                        previous: '<i class="fas fa-angle-left"></i>',
                        next: '<i class="fas fa-angle-right"></i>' 
                    }
                },
                columns: [
                    {
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'reorder'
                    },
                    {
                        data: 'name',
                        name: 'name',
                        orderable: false,
                    },
                    {
                        data: 'type',
                        name: 'type',
                        orderable: false,
                    },
                    @can('delete-master-stage-button-masterstage')
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                    @endcan
                ],
            });
            stageTable.on('row-reorder', function (e, diff, edit) {
                var change1 = {};
                var result = 'Reorder started on row: ' + edit.triggerRow.data().id + '<br>';
                for (var i = 0, ien = diff.length; i < ien; i++) {
                    var rowData = stageTable.row(diff[i].node).data();
                    change1 = {
                        ...change1,
                        [rowData.id]: diff[i].newData
                    };
                    
                    result +=
                        rowData.id +
                        ' updated to be in position ' +
                        diff[i].newData +
                        ' (was ' +
                        diff[i].oldData +
                        ')<br>';
                }
                change_sequence(change1);
            });
        });

    function deleteStages(id) {
        if (confirm('Are you sure you want to delete this stage?')) {
            $.ajax({
                url: base_url + `/delete-stages/${id}`,
                type: 'get',
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    $('#stages-table').DataTable().ajax.reload();
                    if (response.status === false) {
                        toastr.error(response.message);
                        return;
                    } else if (response.status === true) {
                        toastr.success(response.message);
                        return;
                    }
                },
                error: function(xhr) {
                    toastr.error('Error while deleting stage');
                }
            });
        }
    }

    function change_sequence(change_data) {
      $.ajax({
        url:  "{{ route('change.stage.sequence') }}",
        type: 'post',
        data: {
          _token: "{{ csrf_token() }}",
          sequence: JSON.stringify(change_data)
        },
        success: function(response) {
          $('#stages-table').DataTable().ajax.reload();
          toastr.success(response.message);
        },
        error: function(xhr) {
          toastr.error('Error while updating stage sequence');
        }
      });
  }
</script>
@endpush
@endsection