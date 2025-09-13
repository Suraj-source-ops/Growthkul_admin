@extends('layouts.main')
@section('title', '- Permissions')

@section('main-content')
<div class="main_container_section">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="content-area">
                    <div class="head-box">
                        <a href="{{ route('roles') }}">
                            <img src="{{ asset('/assets/images/back.png') }}" alt="back-icon" />
                        </a>
                        <h1>Permissions for Role: {{ ucfirst($role->name) }}</h1>
                    </div>

                    <div class="main-table">
                        <form action="{{ route('roles.permissions.update', ['roleId'=> $role->id]) }}" method="POST"
                            autocomplete="off">
                            @csrf
                            <div class="row">
                                @foreach($usersPermissions as $group => $permissions)
                                <div class="col-md-6 mb-4">
                                    <div class="role-box">
                                        <div class="role-permission-check-box mb-2">
                                            <input type="checkbox" class="select-group" data-group="{{ $group }}"
                                                id="select-all-{{ $group }}">
                                            <span class="checkmark"></span>
                                            <label for="select-all-{{ $group }}"><strong>{{
                                                    ucfirst($group)}}</strong></label>
                                        </div>
                                        @foreach($permissions as $permission)
                                        <div class="role-permission-check-box">
                                            <input type="checkbox" class="permission-checkbox group-{{ $group }}"
                                                data-group="{{ $group }}" name="permissions[]" value="{{ $permission }}"
                                                {{ in_array($permission, $assignedPermissions) ? 'checked' : '' }}>
                                            <span class="checkmark"></span>
                                            <p>{{ $permission }}</p>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endforeach
                                @can('update-role-button-roles')
                                <div class="col-md-12">
                                    <div class="add-member-btn">
                                        <button type="submit">Update Permissions</button>
                                    </div>
                                </div>
                                @endcan
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Handle group-wise "Select All"
    document.querySelectorAll('.select-group').forEach(function(selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function () {
            const group = this.getAttribute('data-group');
            const checkboxes = document.querySelectorAll('.group-' + group);
            checkboxes.forEach(cb => cb.checked = this.checked);
        });
    });
    
</script>
@endpush
@endsection