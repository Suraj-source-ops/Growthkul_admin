<button name="product_status" class="task-status-button" style="background-color: {{$color}}; color:#fff;">
    @if($status == 0)
    Pending
    @elseif ($status == 1)
    In Progress
    @elseif ($status == 2)
    On Hold
    @elseif ($status == 3)
    Completed
    @endif
</button>