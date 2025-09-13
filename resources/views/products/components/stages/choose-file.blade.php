<div class="file-text-box">
  <p id="uploadStatus_{{ $stageid }}"></p>
</div>
<div class="fileUploadWrap2">
    <img src="{{ asset('/assets/images/attachment.png') }}" alt="attachment" />
    <input type="file" name="stage_files[]" multiple
        onchange="uploadStageFiles(this, '{{ $stageid }}', '{{ $productid }}')">
    {{-- <p class="fileNames"></p> --}}
    <button class="cls-btn" id="cancelUpload_{{ $stageid }}" style="display:none;" onclick="cancelUpload('{{ $stageid }}')">Cancel Upload</button>
</div>