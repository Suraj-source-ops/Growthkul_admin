@can('change-status-button-clients')    
<label class="switch">
    <input class="form-check-input check-status-css"
           type="checkbox"
           role="switch"
           id="statusSwitch{{ $row->id }}"
           {{ $row->is_active == 1 ? 'checked' : '' }}
           onchange="activeOrInactiveClient({{ $row->id }})">
    <span class="slider round"></span>
</label>
@endcan