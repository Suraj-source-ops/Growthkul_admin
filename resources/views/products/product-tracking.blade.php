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
                                <a href="{{ route('product.track.lists') }}">
                                    <img class="" src="{{ asset('/assets/images/back.png') }}" alt="Back Icon" />
                                </a>
                                <h1>Product Tracking<span class="product-name">({{ $code }})</span></h1>
                            </div>
                        </div>
                        <div class="content-box">
                            <div class="main-table">
                                <table class="table table-custom-css-box" id="product-stage-table">
                                    <thead>
                                        <tr>
                                            <th style="width:7%" scope="col">S. No.</th>
                                            <th style="width:15%" scope="col">Product Stage</th>
                                            <th style="width:15%" scope="col">Estimated. Date</th>
                                            <th style="width:20%" scope="col">Status ( Completed / Pending )</th>
                                            <th style="width:43%;text-align: left;" scope="col">Description / Notes</th>
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
        {{-- select file type onclick attchment image start --}}
        <script>
            $(document).on('change', ".fileUploadWrap2 input[type='file']", function() {
                if ($(this).val()) {
                    var filenames = $(this).val().split("\\");
                    filenames = filenames[filenames.length - 1];
                    $('.fileNames').text(filenames);
                }
            });
        </script>
        {{-- select file type onclick attchment image END --}}

        {{-- Datatable --}}
        <script>
            $(document).ready(function() {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $('#product-stage-table').DataTable({
                    processing: true,
                    serverSide: true,
                    bFilter: false,
                    paging: false,
                    responsive: true,
                    scrollX: true,
                    ajax: {
                        url: "{{ route('product.stages', [$productId]) }}",
                        type: "POST",
                        data: function(d) {
                            d.client_id = $('#product_filter_tracking').val();
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
                            data: 'stage_name',
                            name: 'stage_name',
                            orderable: false,
                        },
                        {
                            data: 'estimate_date',
                            name: 'estimate_date',
                            orderable: false,
                        },
                        {
                            data: 'status',
                            name: 'status',
                            orderable: false,
                        },
                        {
                            data: 'notes',
                            name: 'notes',
                            orderable: false,
                            searchable: false
                        }
                    ],
                    drawCallback: function() {
                        // datePicker();
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
                            $('#product-stage-table').DataTable().ajax.reload();
                        } else {
                            $('#product-stage-table').DataTable().ajax.reload();
                            toastr.success(response.message);
                        }
                    },
                    error: function(xhr) {
                        toastr.error('Error while updating product stage status.');
                    }
                });
            }

            // Date Update
            function datePicker(data) {
                const stageId = data.dataset.stageId;
                const dateStr = data.value;
                $.ajax({
                    url: '{{ route('update.stage.estimate.date') }}',
                    type: 'POST',
                    data: {
                        stageId: stageId,
                        est_date: dateStr,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.status) {
                            toastr.success(response.message);
                            $('#product-stage-table').DataTable().ajax.reload();
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(err) {
                        toastr.error('Error while updating stage estimate date');
                    }
                });
            }

            //update notes
            function updateNotes(data) {
                const stageId = data.dataset.stageid;
                const notes = data.value;
                $.ajax({
                    url: '{{ route('update.stage.notes') }}',
                    type: 'POST',
                    data: {
                        stageId: stageId,
                        note: notes,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.status) {
                            toastr.success(response.message);
                            $('#product-stage-table').DataTable().ajax.reload();
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(err) {
                        toastr.error('Error while updating notes');
                    }
                });
            }
        </script>
        <script>
            const uploadControllers = {};
            const activeUploads = {};

            function uploadStageFiles(input, stageId, productId) {
                const files = input.files;
                for (let i = 0; i < files.length; i++) {
                    uploadLargeFile(files[i], stageId, productId, input);
                }
            }
            function uploadLargeFile(file, stageId, productId, input) {
                // document.getElementById('fullscreenLoader').style.display = 'block';
                const chunkSize = 10 * 1024 * 1024;
                const totalChunks = Math.ceil(file.size / chunkSize);
                const uploadId = Date.now().toString();
                activeUploads[stageId] = uploadId;

                const fileName = file.name;
                const fileType = file.type;
                let chunkIndex = 0;
                let uploadedBytes = 0;

                const statusLabel = document.getElementById('uploadStatus_' + stageId);
                const cancelButton = document.getElementById('cancelUpload_' + stageId);
                cancelButton.style.display = 'inline-block';

                const controller = new AbortController();
                uploadControllers[stageId] = controller;

                function sendChunk() {
                    if (chunkIndex >= totalChunks) {
                        return mergeChunks();
                    }

                    const start = chunkIndex * chunkSize;
                    const end = Math.min(start + chunkSize, file.size);
                    const blob = file.slice(start, end);
                    const formData = new FormData();
                    formData.append('chunk', blob);
                    formData.append('upload_id', uploadId);
                    formData.append('chunk_index', chunkIndex);

                    fetch('{{ url('api/upload-chunk') }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        signal: controller.signal
                    }).then(res => res.json()).then(() => {
                        uploadedBytes += blob.size;
                        const progress = Math.round((uploadedBytes / file.size) * 100);
                        // document.getElementById('loaderText').textContent = `Uploading "${file.name}" : ${progress}%`;
                        statusLabel.textContent = `UPLOADING.. "${file.name}" : ${progress}%`;
                        chunkIndex++;
                        sendChunk();
                    }).catch(err => {
                        if (err.name === 'AbortError') {
                            statusLabel.textContent = 'Upload canceled';
                            // document.getElementById('fullscreenLoader').style.display = 'none';
                            cancelButton.style.display = 'none';
                            cleanupUpload(uploadId);
                        } else {
                            console.error(`Chunk ${chunkIndex} failed`, err);
                            setTimeout(sendChunk, 1000);
                        }
                    });
                }

                function mergeChunks() {
                    document.getElementById('fullscreenLoader').style.display = 'block';
                    fetch('{{ url('api/merge-chunks') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            user_id: "{{ Auth::user()->id }}",
                            upload_id: uploadId,
                            filename: fileName,
                            total_chunks: totalChunks,
                            product_id: productId,
                            stage_id: stageId,
                            mime_type: fileType,
                            identifier:'stagefile',
                        })
                    }).then(res => res.json()).then(res => {
                        if (res.status) {
                            statusLabel.textContent = `Upload complete: ${fileName}`;
                            toastr.success(`File "${fileName}" uploaded`);
                            $('#product-stage-table').DataTable().ajax.reload();
                        } else {
                            statusLabel.textContent = 'Upload failed';
                            toastr.error('Merge failed');
                        }
                        cancelButton.style.display = 'none';
                        document.getElementById('fullscreenLoader').style.display = 'none';
                    }).catch(err => {
                        statusLabel.textContent = 'Merge failed';
                        cancelButton.style.display = 'none';
                        // document.getElementById('fullscreenLoader').style.display = 'none';
                        toastr.error('Merge error');
                    });
                }

                sendChunk();
            }

            function cancelUpload(stageId) {
                if (uploadControllers[stageId]) {
                    uploadControllers[stageId].abort();
                    delete uploadControllers[stageId];
                }
            }

            function cleanupUpload(uploadId) {
                fetch('{{ url('api/cancel-upload') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        upload_id: uploadId
                    })
                }).then(res => res.json()).then(data => {
                    if (data.status) {
                        toastr.success(data.message);
                    } else {
                        toastr.error(data.message);
                    }
                    $('#product-stage-table').DataTable().ajax.reload();
                }).catch(err => {
                    console.error('Cleanup failed', err);
                    toastr.error('Error while canceling upload.');
                });
            }
        </script>
    @endpush

@endsection
