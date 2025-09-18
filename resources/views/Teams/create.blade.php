@extends('layouts.main')
@section('title', '- Add Team')
@section('main-content')
    <div class="main_container_section">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="content-area">
                        <div class="head-box">
                            <a href="{{ route('teams') }}">
                                <img class="" src="{{ asset('/assets/images/back.png') }}" alt="back-icon" />
                            </a>
                            <h1>Add Team</h1>
                        </div>
                        <div class="content-box">
                            <form action="{{ route('team.store') }}" method="POST" autocomplete="off" id="addteam">
                                @csrf
                                <div class="row">
                                    <div class="col-lg-4 col-md-6 col-sm-6">
                                        <div class="input_box">
                                            <label>Team Name</label>
                                            <input type="text" name="name" placeholder="Enter Team Name"
                                                value="{{ old('name') }}">
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6 col-sm-6">
                                        <div class="input_box">
                                            <label>Description</label>
                                            <input type="text" name="description" placeholder="Enter description"
                                                value="{{ old('description') }}">
                                        </div>
                                    </div>
                                    @can('add-team-button-teams')
                                    <div class="col-md-12">
                                        <div class="add-member-btn">
                                            <button>Add Team</button>
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
    $("#addteam").submit(function(event){
        const name = $('input[name="name"]').val(); 
        const description = $('input[name="description"]').val(); 
        if(name === '' || name === undefined || name === null){
            toastr.error('Please enter team name');
            return false;
        }
        if(description === '' || description === undefined || description === null){
            toastr.error('Please enter team description');
            return false;
        }
    });
</script>
   
@endpush
@endsection
