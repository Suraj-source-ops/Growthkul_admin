<div class="date-input-wrapper">
    <img src="{{ asset('/assets/images/celander-img.png') }}" alt="Calendar Icon"
        onclick="document.getElementById('custom-date-{{$stageid}}').showPicker()" />
    <input type="date" id="custom-date-{{$stageid}}" value="{{$estimate_date}}" data-stage-id="{{$stageid}}"
        onchange="datePicker(this)" min="{{$today}}">
</div>