@can('view-task-comment-button-tasks')
<a href="{{ route('view.tasks.product.details', ['slug' => $slug, 'type' => $type]) }}">
    <div class="massage-img">
        <img src="{{asset('assets/images/message-f.png')}}" alt="attachment">
        <div class="comment-numbers">
            <p>{{ $count <= 10 ? $count : 10 . '+' }}</p>
        </div>
    </div>
</a>
@endcan
