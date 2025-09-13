<div class="toglle-edit-icons">
    <label class="switch">
        <input class="form-check-input check-status-css" type="checkbox" role="switch"
            id="statusSwitchStage-{{$stageid}}" onchange="changeStatus({{$stageid}})" @if ($status) checked @endif><span
            class="slider round"></span>
    </label>
</div>