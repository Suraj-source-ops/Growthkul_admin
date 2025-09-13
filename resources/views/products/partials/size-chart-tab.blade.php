{{-- <div id="sizeChart" class="tab-pane active"> --}}
    <form action="{{route('update.product.details',['slug'=> $product->slug,'type' => 1])}}" method="POST"
        enctype="multipart/form-data" autocomplete="off">
        @csrf
        <input type="hidden" name="productId" value="{{$product->id}}">
        <div class="row">
            <div class="col-md-12">
                <div class="input_box">
                    <label>Product Details</label>
                    <textarea name="description" cols="10" rows="4"
                        placeholder="Enter Product Description">{{$product->product_description}}</textarea>
                </div>
            </div>
            <div class="col-md-12">
                <div class="input_box">
                    <label>Upload References</label>
                    {{-- <input type="file" name="product_files[]" id="sizeChartattachment" multiple /> --}}
                    <input type="file" onchange="startSizeChartattachmentChunkUpload(this)"
                        data-productid="{{$product->id}}" id="sizeChartInputFile" />
                </div>
                <div id="upload-progress-wrapper-sizechart"
                    style="display:none; margin-top: 10px; margin-bottom: 10px;">
                    <span id="upload-status-text-sizechart" style="font-weight: bold; font-size:14px;"></span>
                    <div
                        style="width: 100%; background: #f1f1f1; border-radius: 4px; overflow: hidden; margin-top: 5px;">
                        <div id="upload-progress-bar-sizechart" style="height: 10px; width: 0%; background: #4CAF50;">
                        </div>
                    </div>
                    <button id="cancel-upload-btn-sizechart" class="comman-btn"
                        onclick="cancelProductUploadSizeChart(event)" style="margin-top: 8px;">Cancel
                        Upload</button>
                </div>
                <input type="hidden" name="product_files" id="productfiles-sizechart" value="">
            </div>
            <div class="col-md-12">
                <div class="file-upload-box">
                    <h3>Product Files</h3>
                    <div class="row" id="size-chart-uploaded-files-wrapper">
                        @if(!empty($productDocs) && count($productDocs)>0)
                        @foreach ($productDocs as $document)
                        <div class="col-md-4 uploaded-file">
                            <div class="file-view">
                                <div class="sample-pdf-img">
                                    <img src="{{ asset('/assets/images/file_icon.png') }}" alt="file icon" />
                                    <p style="width: 235px; overflow: hidden; text-overflow: ellipsis;">
                                        {{$document->file_name}}
                                    </p>
                                </div>
                                <div class="file-view-icons">
                                    <a href="{{route('view.product.file',['docId' => $document])}}"><img
                                            src="{{ asset('/assets/images/views.png') }}" alt="View file" /></a>
                                    <a href="{{route('delete.product.file',['docId' => $document])}}"
                                        onclick="return confirm('Are you sure you want to delete this file?')"><img
                                            class="remove-icon" src="{{ asset('/assets/images/cross.png') }}"
                                            alt="remove icon" /></a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                        @endif
                    </div>

                    {{-- Stage files --}}
                    @if (!empty($stageDocs))
                    <h3>Product Stage File</h3>
                    <div class="row" id="size-chart-uploaded-files-wrapper">
                        @if(!empty($stageDocs) && count($stageDocs)>0)
                        @foreach ($stageDocs as $document)
                        <div class="col-md-4 uploaded-file">
                            <div class="file-view">
                                <div class="sample-pdf-img">
                                    <img src="{{ asset('/assets/images/file_icon.png') }}" alt="file icon" />
                                    <p style="width: 235px; overflow: hidden; text-overflow: ellipsis;">
                                        {{$document->file_name}}
                                    </p>
                                </div>
                                <div class="file-view-icons">
                                    <a href="{{route('view.product.file',['docId' => $document])}}"><img
                                            src="{{ asset('/assets/images/views.png') }}" alt="View file" /></a>
                                    <a href="{{route('delete.product.file',['docId' => $document])}}"
                                        onclick="return confirm('Are you sure you want to delete this file?')"><img
                                            class="remove-icon" src="{{ asset('/assets/images/cross.png') }}"
                                            alt="remove icon" /></a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                        @endif
                    </div>
                    @endif
                </div>
            </div>
            <div class="col-md-12">
                <div class="ui form multiple-select-box">
                    <div class="inline field">
                        <div class="input_box">
                            <label>Product's Graphic Type</label>
                            <select name="graphic_product_type[]" multiple
                                class="label ui selection fluid dropdown select-boxes">
                                <option value="">Select Product's Graphic Type</option>
                                @if(count($graphicTypes)> 0 && isset($graphicTypes))
                                @foreach ($graphicTypes as $type)
                                <option value="{{$type}}" @if(in_array($type, $product->graphic_type)) selected
                                    @endif>{{$type}}
                                </option>
                                @endforeach
                                @endif
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            {{-- Team and member --}}
            @if (!empty($team) && !empty($member))
            <div class="col-md-12">
                <div class="assign-to-box">
                    <h3>Assign To</h3>
                    <hr>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="input_box">
                            <label>First Assigned Team</label>
                            <input type="text" name="team" value="{{$team}}" disabled/>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input_box">
                            <label>First Assigned Member</label>
                            <input type="text" name="member" value="{{$member}}" disabled/>
                        </div>
                    </div>
                </div>
            </div>
            @endif
            @can('update-product-button-products')
            <div class="col-md-12">
                <div class="add-member-btn">
                    <button type="submit" class="comman-btn">Update</button>
                </div>
            </div>
            @endcan
        </div>
    </form>
    {{--
</div> --}}