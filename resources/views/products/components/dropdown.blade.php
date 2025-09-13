@can('assign-product-dropdown-products')    
<select name="assigned_member[]" class="custom-dropdown select-boxes assigned_member" style="width: 100%"
    data-product-id={{$selectedProductId}} data-assined-by={{$user_id}} multiple="multiple">
    @foreach ($groupedOptions as $teamName => $members)
    @if ($teamName != 'NOTEAM')
    <optgroup label="{{ $teamName }}">
        @foreach ($members as $member)
        <option value="{{ $member->id }}" {{ in_array($member->id, $selected) ? 'selected' : '' }}>{{ $member->name }}
        </option>
        @endforeach
    </optgroup>
    @endif
    @endforeach
</select>
@endcan