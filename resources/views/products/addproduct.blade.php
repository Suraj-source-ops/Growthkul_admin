@extends('layouts.main')
@section('title', '- Add Product details')
@section('main-content')
<div class="main_container_section">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="content-area">
                    <div class="head-box">
                        <a href="{{ route('clients') }}" class="back-icon">
                            <img class="" src="{{ asset('/assets/images/back.png') }}" alt="back-icon" />
                        </a>
                        <h1>Add Product details</h1>
                    </div>
                    <div class="content-box">
                        <form action="{{ route('store.product') }}" method="POST" enctype="multipart/form-data"
                            autocomplete="off" id="addProduct">
                            @csrf
                            <input type="hidden" name="clientid" value="{{ $clientid}}">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="input_box">
                                                <label for="">Product Type</label>
                                                <select name="product_type" id="product_type" class="select-boxes">
                                                    <option value="1" @if (old('product_type')==1) selected @endif>Size
                                                        Chart</option>
                                                    <option value="2" @if (old('product_type')==2) selected @endif>Tech
                                                        Pack</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="input_box">
                                                <label>Product Code</label>
                                                <input type="text" name="product_code" placeholder="Enter Product Code"
                                                    value="{{old('product_code')}}" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="input_box">
                                        <label>Product Details</label>
                                        <textarea name="description" cols="10" rows="4"
                                            placeholder="Enter Product Description">{{old('description')}}</textarea>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="input_box">
                                        <label>Upload References</label>
                                        <input type="file" id="attachment" onchange="startChunkUpload(this)"/>
                                    </div>
                                    <div id="upload-progress-wrapper"
                                        style="display:none; margin-top: 10px; margin-bottom: 10px;">
                                        <span id="upload-status-text" style="font-weight: bold; font-size:14px;"></span>
                                        <div
                                            style="width: 100%; background: #f1f1f1; border-radius: 4px; overflow: hidden; margin-top: 5px;">
                                            <div id="upload-progress-bar"
                                                style="height: 10px; width: 0%; background: #4CAF50;"></div>
                                        </div>
                                        <button id="cancel-upload-btn" class="comman-btn"
                                            onclick="cancelProductUpload()" style="margin-top: 8px;">Cancel
                                            Upload</button>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="file-upload-box">
                                        <h3>File Uploaded</h3>
                                        <div class="row" id="uploaded-files-wrapper">

                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="uploaded_files" id="uploaded_files" value="">
                                <div class="col-md-12">
                                    <div class="ui form multiple-select-box">
                                        <div class="inline field">
                                            <div class="input_box">
                                                <label>Product's Graphic Type</label>
                                                <select name="graphic_product_type[]" multiple
                                                    class="label select-boxes ui selection fluid dropdown">
                                                    <option value="">Select Product's Graphic Type</option>
                                                    @foreach ($graphicProductTypes as $key=> $types)
                                                    <option value="{{$types}}" @if(is_array(old('graphic_product_type'))
                                                        && in_array($types, old('graphic_product_type'))) selected
                                                        @endif>
                                                        {{$types}}
                                                    </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="assign-to-box">
                                        <h3>Assign To</h3>
                                        <hr>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="input_box">
                                                <label for="">Team</label>
                                                <select name="team_id" id="team_id" class="select-boxes">
                                                    <option value="">Select</option>
                                                    @foreach($teamDetails as $id => $name)
                                                    <option value="{{ $id }}">{{ $name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="input_box">
                                                <label for="">Member</label>
                                                <select name="member_id" id="member_id" class="select-boxes">
                                                    <option value="">Select Member</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="add-member-btn">
                                        <button class="comman-btn">Add Product</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')

{{-- NEW hERE --}}
<script>
    let uploadedFiles = [];
    let uploadController = null;
    let currentUploadId = null;

    async function startChunkUpload(input) {
        const file = input.files[0];
        if (!file) return;

        const chunkSize = 10 * 1024 * 1024;
        const totalChunks = Math.ceil(file.size / chunkSize);
        const uploadId = "{{ time() }}";
        currentUploadId = uploadId;

        const fileName = file.name;
        const mimeType = file.type;

        // UI Elements
        const progressBar = document.getElementById('upload-progress-bar');
        const statusText = document.getElementById('upload-status-text');
        const cancelBtn = document.getElementById('cancel-upload-btn');
        const progressWrapper = document.getElementById('upload-progress-wrapper');

        // Reset UI
        progressWrapper.style.display = 'block';
        progressBar.style.width = '0%';
        cancelBtn.style.display = 'inline-block';
        statusText.textContent = `Uploading "${fileName}"...`;

        let chunkIndex = 0;
        let uploadedBytes = 0;
        uploadController = new AbortController();

        async function sendChunk() {
            if (chunkIndex >= totalChunks) return mergeChunks();

            const start = chunkIndex * chunkSize;
            const end = Math.min(start + chunkSize, file.size);
            const blob = file.slice(start, end);
            const formData = new FormData();
            formData.append('chunk', blob);
            formData.append('upload_id', uploadId);
            formData.append('chunk_index', chunkIndex);

            try {
                await fetch('/api/upload-chunk', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    signal: uploadController.signal
                });

                uploadedBytes += blob.size;
                const progress = Math.round((uploadedBytes / file.size) * 100);
                progressBar.style.width = `${progress}%`;
                statusText.textContent = `Uploading "${fileName}"... ${progress}%`;
                chunkIndex++;
                sendChunk();
            } catch (err) {
                if (err.name === 'AbortError') {
                    statusText.textContent = 'Upload cancelled.';
                    cancelBtn.style.display = 'none';
                    cleanupCancelledUpload(event, uploadId);
                } else {
                    statusText.textContent = 'Upload failed. Retrying...';
                    setTimeout(sendChunk, 1000);
                }
            }
        }

        async function mergeChunks() {
            try {
                document.getElementById('fullscreenLoader').style.display = 'block';
                const response = await fetch('/api/merge-chunks', {
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
                        product_id: null,
                        stage_id: null,
                        mime_type: mimeType,
                        identifier: 'productfile'
                    })
                });

                const result = await response.json();
                cancelBtn.style.display = 'none';

                if (result.status && result.url) {
                    toastr.success(result.message)
                    statusText.textContent = 'Upload complete.';
                    progressWrapper.style.display = 'none';
                    document.getElementById('fullscreenLoader').style.display = 'none';
                    uploadedFiles.push({
                        file_id: result.docId,
                        file_name: fileName,
                        file_path: result.url
                    });
                    document.getElementById('uploaded_files').value = JSON.stringify(uploadedFiles);
                    document.getElementById('uploaded-files-wrapper').insertAdjacentHTML('beforeend', `
                        <div class="col-md-4 uploaded-file" data-doc-id="${result.docId}">
                            <div class="file-view">
                                <div class="sample-pdf-img">
                                    <img src="{{ asset('/assets/images/file_icon.png') }}" alt="file icon"/>
                                    <p style="width: 150px; overflow: hidden; text-overflow: ellipsis;" title="${fileName}">${fileName}</p>
                                </div>
                                <div class="file-view-icons" style="cursor:pointer;">
                                    <img class="remove-icon" src="{{ asset('/assets/images/cross.png') }}" alt="remove icon" onclick="deleteProductDoc(event,${result.docId})"/>
                                </div>
                            </div>
                        </div>
                    `);
                } else {
                    document.getElementById('fullscreenLoader').style.display = 'none';
                    statusText.textContent = 'Upload failed.';
                    toastr.error('Error merging uploaded chunks.');
                }
            } catch (err) {
                document.getElementById('fullscreenLoader').style.display = 'none';
                statusText.textContent = 'Merge error.';
                toastr.error('Merge failed.');
            }

            input.value = '';
        }

        sendChunk();
    }

    function cancelProductUpload() {
        if (uploadController) {
            uploadController.abort();
            uploadController = null;
            document.getElementById('cancel-upload-btn').style.display = 'none';
        }
    }

    function cleanupCancelledUpload(event, uploadId) {
        event.preventDefault();
        const progressSection = document.getElementById('upload-progress-wrapper');
        fetch('/api/cancel-upload', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ upload_id: uploadId })
        })
        .then(res => res.json())
        .then(res => {
            if (res.status) {
                progressSection.style.display = 'none';
                $('#attachment').val('');
                toastr.success(res.message);
            } else {
                toastr.error(res.message);
            }
        })
        .catch(() => {
            toastr.error('Error cleaning up upload.');
        });
    }
</script>

{{-- end here --}}
{{-- Team Member Dropdown --}}
<script>
    $(document).ready(function() {
        $('#team_id').on('change', function() {
            var teamId = $(this).val();
            if (!teamId) {
                $('#member_id').empty();
                $('#member_id').append('<option value="">Select Member</option>');
                return;
            }
            $.ajax({
                url: base_url + '/get-team-members/' + teamId,
                type: 'GET',
                success: function(data) {
                    if(data.status){
                        $('#member_id').empty();
                        $('#member_id').append('<option value="">Select Member</option>');
                        $.each(data.members, function(id, name) {
                            $('#member_id').append('<option value="' + id + '">' + name + '</option>');
                        });
                    } else {
                        $('#member_id').empty();
                        $('#member_id').append('<option value="">Select Member</option>');
                       
                    }
                },
                error: function(xhr) {
                    toastr.error('An error occurred while deleting the file.');
                    console.error(xhr.responseText);
                }
            });
        });
    });
</script>
{{-- end dropdown --}}
<script>
    //delete uploaded file
    function deleteProductDoc(event, docid){
        event.preventDefault();
         if (!confirm('Are you sure you want to delete this file?')) return;
        $.ajax({
            url: base_url + '/api/delete-product-file/'+"{{ Auth::user()->id }}" + '/'+docid,
            type: 'GET',
            success: function(data) {
                if(data.status){
                    toastr.success(data.message);
                    $(`.uploaded-file[data-doc-id="${docid}"]`).remove();
                } else {
                    toastr.error(data.message);
                }
            }
        });
    }
</script>
<script>
    $("#addProduct").submit(function(event){
        const productcode = $('input[name="product_code"]').val(); 
        const description = $('textarea[name="description"]').val(); 
        const graphicTypes = $('select[name="graphic_product_type[]"]').val()
        const team = $('select[name="team_id"]').val();
        const member = $('select[name="member_id"]').val();
        if(productcode === '' || productcode === undefined || productcode === null){
            toastr.error('Please enter product code');
            return false;
        }
        if(description === '' || description === undefined || description === null){
            toastr.error('Please enter description');
            return false;
        }

        if(!graphicTypes || graphicTypes.length === 0){
            toastr.error("Please select at least one graphic product type");
            return false;
        }
        if(team === '' || team === undefined || team === null){
            toastr.error('Please select team');
            return false;
        }
        if(member === '' || member === undefined || member === null){
            toastr.error('Please select member');
            return false;
        }
    });
</script>
@endpush
@endsection