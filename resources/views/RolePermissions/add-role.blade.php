@extends('layouts.main')
@section('title', '- Add Role')
@section('main-content')


<div class="main_container_section">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="content-area">
                    <div class="head-box">
                        <a href="{{ route('roles') }}">
                            <img class="" src="{{ asset('/assets/images/back.png') }}" alt="back-icon" />
                        </a>
                        <h1>Add Role</h1>
                    </div>
                    <div class="main-table">
                        <form action="{{route('store.role.name')}}" method="POST" autocomplete="off">
                            @csrf
                            <div class="row">
                                <div class="col-lg-4 col-md-6 col-sm-6">
                                    <div class="input_box">
                                        <label for="">Role Name</label>
                                        <input type="text" name="name" placeholder="Please Enter Role Name">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="add-member-btn">
                                        <button type="submit">Add Role</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection