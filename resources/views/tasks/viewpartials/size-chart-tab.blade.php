<div class="row">
    <div class="col-md-12">
        <div class="input_box">
            <label>Product Details</label>
            <textarea name="description" cols="10" rows="4" placeholder="Enter Product Description"
                disabled>{{ $product->product_description }}</textarea>
        </div>
    </div>
    <div class="col-md-12">
        <div class="file-upload-box">
            <h3>Product Files</h3>
            <div class="row" id="size-chart-uploaded-files-wrapper">
                @if (!empty($productDocs) && count($productDocs) > 0)
                @foreach ($productDocs as $document)
                <div class="col-md-3 uploaded-file">
                    <div class="file-view">
                        <div class="sample-pdf-img">
                            <img src="{{ asset('/assets/images/file_icon.png') }}" alt="file icon" />
                            <p style="width: 235px; overflow: hidden; text-overflow: ellipsis;">
                                {{ $document->file_name }}
                            </p>
                        </div>
                        <div class="file-view-icons">
                            <a href="{{ route('view.product.file', ['docId' => $document]) }}"><img
                                    src="{{ asset('/assets/images/views.png') }}" alt="View file" /></a>
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
                @if (!empty($stageDocs) && count($stageDocs) > 0)
                @foreach ($stageDocs as $document)
                <div class="col-md-3 uploaded-file">
                    <div class="file-view">
                        <div class="sample-pdf-img">
                            <img src="{{ asset('/assets/images/file_icon.png') }}" alt="file icon" />
                            <p style="width: 235px; overflow: hidden; text-overflow: ellipsis;">
                                {{ $document->file_name }}
                            </p>
                        </div>
                        <div class="file-view-icons">
                            <a href="{{ route('view.product.file', ['docId' => $document]) }}"><img
                                    src="{{ asset('/assets/images/views.png') }}" alt="View file" /></a>
                        </div>
                    </div>
                </div>
                @endforeach
                @endif
            </div>
            @endif
        </div>
        <div class="ui form multiple-select-box">
            <div class="inline field">
                <div class="input_box">
                    <label>Product's Graphic Type</label>
                    <select name="graphic_product_type[]" multiple
                        class="label ui selection fluid dropdown select-boxes" disabled>
                        <option value="">Select Product's Graphic Types</option>
                        @if (count($graphicTypes) > 0)
                        @foreach ($graphicTypes as $type)
                        <option value="{{ $type }}" @if (in_array($type, $product->graphic_type)) selected @endif>
                            {{ $type }}
                        </option>
                        @endforeach
                        @endif
                    </select>
                </div>
            </div>
        </div>
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
                        <input type="text" name="team" value="{{$team}}" disabled />
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="input_box">
                        <label>First Assigned Member</label>
                        <input type="text" name="member" value="{{$member}}" disabled />
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
    <div class="col-md-12 comment-history-main-box">
        <div class="comment-history-btn">
            <button type="button" class="comman-btn" id="size-chart-comment-btn">Comment</button>
            <button type="button" class="border-comman-btn" id="size-chart-history-btn">History</button>
        </div>
        <div class="comment-section" id="view-sizechart-comment">
            @include('tasks.viewpartials.commentAndHistory.comment.comment',
            [
            'productId'=>$productId,
            'type'=>$prodtype,
            'comments' => $comments
            ])
        </div>
        <div class="history-section d-none" id="view-sizechart-history">
            @include('tasks.viewpartials.commentAndHistory.history.history',
            [
            'productId'=>$productId,
            'type'=>$prodtype
            ])
        </div>
    </div>
</div>