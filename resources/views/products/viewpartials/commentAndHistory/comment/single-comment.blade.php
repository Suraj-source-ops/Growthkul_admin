@php
$color = '#' . substr(md5($user), 0, 6);
@endphp
<div class="comment-history-main">
    <div class="massage-img">
        <img src="{{ asset('/assets/images/message-f.png') }}" alt="attachment" />
    </div>
    <div class="comment-history-box">
        @if (isset($profile) && !empty($profile))
            <img class="circle-name" src="{{ asset($profile) }}" alt="back-icon" />
        @else    
        <div class="circle-name" style="background-color: {{ $color }};">
            <p>{{ strtoupper(substr($user, 0, 2)) }}</p>
        </div>
        @endif
        <div class="commment-history-content">
            <div class="comment-title-date">
                <h3>{{ $user }}</h3>
                <p>{{ now()->format('M d, Y | h:i A') }}</p>
            </div>
            <p class="comment-description">{{ $comment->comment }}</p>
            @if(!empty($comment->documents))
            @foreach ($comment->documents as $doc)
            <div class="file-upload-container mt-2">
                <img src="{{ asset('/assets/images/attachment.png') }}" alt="attachment" class="upload-img" />
                <p style="white-space: nowrap; width: 220px; overflow: hidden; text-overflow: ellipsis;">
                    {{$doc->file_name}}</p>
                <a href="{{route('view.comment.file',['docId' => $doc->id])}}">
                    <img src="{{ asset('/assets/images/views.png') }}" alt="View file"
                        style="height: 25px;margin-bottom: 8px;" />
                </a>
            </div>
            @endforeach
            @endif
        </div>
    </div>
</div>