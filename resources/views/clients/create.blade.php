@extends('layouts.main')
@section('title', '- Add Client')
@section('main-content')
<div class="main_container_section">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="content-area">
                    <div class="head-box">
                        <a href="{{ route('clients') }}" class="back-icon">
                            <img class="" src="{{ asset('/assets/images/back.png') }}" alt="back-icon" />
                        </a>
                        <h1>Add Clients</h1>
                    </div>
                    <div class="content-box">
                        <form action="{{route('store.client')}}" method="POST" autocomplete="off">
                            @csrf
                            <div class="row">
                                <div class="col-lg-4 col-md-6 col-sm-6">
                                    <div class="input_box">
                                        <label>Client Name</label>
                                        <input type="text" name="name" placeholder="Name" value="{{ old('name') }}">
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6 col-sm-6">
                                    <div class="input_box">
                                        <label>Phone Number</label>
                                        <input type="number" name="phone" placeholder="Enter Phone Number" value="{{ old('phone') }}">
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6 col-sm-6">
                                    <div class="input_box">
                                        <label>Email</label>
                                        <input type="email" name="email" placeholder="Enter Email" value="{{ old('email') }}">
                                    </div>
                                </div>
                                @can('add-client-button-clients')
                                <div class="col-md-12">
                                    <div class="add-member-btn">
                                        <button type="submit">Save</button>
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
@endsection