<select name="assigned_member" class="custom-dropdown select-boxes task_assigned_member" style="width: 100%"
    data-product-id={{$selectedProductId}} data-assined-by={{$user_id}}>
    @foreach ($groupedOptions as $teamName => $members)
    @if ($teamName != 'NOTEAM')
    <optgroup label="{{ $teamName }}">
        @foreach ($members as $member)
        <option value="{{ $member->id }}" {{ $selected==$member->id ? 'selected' : '' }}>{{ $member->name }}
        </option>
        @endforeach
    </optgroup>
    @endif
    @endforeach
</select>