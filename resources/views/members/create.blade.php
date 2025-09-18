@extends('layouts.main')
@section('title', '- Add Team Members')
@section('main-content')


<div class="main_container_section">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="content-area">
                    <div class="head-box">
                        <a href="{{ route('members')}}">
                            <img class="" src="{{ asset('/assets/images/back.png') }}" alt="back-icon" />
                        </a>
                        <h1>Add Members</h1>
                    </div>
                    <form action="{{route('team.members.store')}}" method="POST" enctype="multipart/form-data" autocomplete="off">
                        @csrf
                       <div class="add-members-main-box">
                         <div class="row">
                            <div class="col-lg-4 col-md-6 col-sm-6">
                                <div class="input_box">
                                    <label>Team</label>
                                    <select name="team" class="select-boxes">
                                        <option value="">Select Team</option>
                                        @foreach ($teams as $key => $team)
                                        <option value="{{ $key }}" @if (old('team')==$key) selected @endif>{{ $team }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6 col-sm-6">
                                <div class="input_box">
                                    <label>Member Name</label>
                                    <input type="text" name="member_name" placeholder="Enter member Name"
                                        value="{{old('member_name') }}">
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6 col-sm-6">
                                <div class="input_box">
                                    <label>Role</label>
                                    <select name="role" class="select-boxes">
                                        <option value="">Select Role</option>
                                        @foreach ($roles as $key => $role)
                                        <option value="{{ $role }}" @if (old('role')==$key) selected @endif>{{ $role }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6 col-sm-6">
                                <div class="input_box">
                                    <label>Phone number</label>
                                    <input type="number" name="mobile" placeholder="Enter phone number"
                                        value="{{ old('mobile') }}">
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6 col-sm-6">
                                <div class="input_box">
                                    <label>Email Address</label>
                                    <input type="email" name="email" placeholder="Enter email address"
                                        value="{{old('email') }}">
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6 col-sm-6">
                                <div class="input_box">
                                    <label>Profile Picture</label>
                                    <input type="file" name="profile_pic" placeholder="choose image" accept="image/*"
                                        id="file-input">
                                    <div>
                                        <img id="image_preview" class="choss-image"
                                            src="{{ asset('/assets/profile_pics/dummy.png') }}" alt="back-icon" />
                                    </div>
                                </div>
                            </div>
                            @can('add-member-button-members')
                                <div class="col-md-12">
                                    <div class="add-member-btn">
                                        <button>Add Member</button>
                                    </div>
                                </div>
                            @endcan
                        </div>
                       </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
            $('#image_preview').attr('src', e.target.result);
            $('#image_preview').hide();
            $('#image_preview').fadeIn(400);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    $("#file-input").change(function() {
        readURL(this);
    });

</script>
@endpush
@endsection