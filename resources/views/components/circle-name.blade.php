<style>
    .member-names {
        display: flex;
        align-items: center;
    }
</style>

<div class="member-names">
    @if((isset($profile) && !empty($profile)))
    <img class="circle-name" src="{{ asset($profile->file_path) }}" alt="back-icon" />
    @else
    <div class="circle-name" style="background-color: {{ $color }};">
        <p>{{ strtoupper(substr($name, 0, 2)) }}</p>
    </div>
    @endif
    <p>{{ $name }}</p>

</div>