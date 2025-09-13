<form enctype="multipart/form-data" id="commentForm" autocomplete="off">
    @csrf
    <div class="comment-box">
        <input type="hidden" name="productId" value="{{$productId}}">
        <input type="hidden" name="product_type" value="{{$type}}">
        <div class="commant-input">
            <input type="text" placeholder="Comment" name="comment">
        </div>
        <div class="comment-file-btn">
            <div class="fileUploadWrap">
                <img src="{{ asset('/assets/images/attachment.png') }}" alt="attachment" />
                <input type="file" name="comment_files[]" id="commentattchment" multiple>
            </div>
            <p id="files-area">
                <span id="filesList">
                    <span id="files-names"></span>
                </span>
            </p>
            <button type="submit" class="comman-btn">Comment</button>
        </div>
    </div>
    <div class="row" id="comment-uploaded-files-wrapper">
    </div>
</form>
<div id="comments">
    @foreach ($comments as $comment)
    @include('products.viewpartials.commentAndHistory.comment.single-comment', [
    'comment' => $comment,
    'user'=>isset($comment->user->name) ? $comment->user->name:'NA',
    'profile' => isset($comment->user->profile) && $comment->user->profile ? $comment->user->profile->file_path : '',
    ])
    @endforeach
</div>