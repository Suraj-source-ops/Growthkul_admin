@extends('layouts.main')
@section('title', '- dashboard')
@section('main-content')
    <div class="main_container_section">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="content-area">

                        <div class="button-heading-box">
                            <div class="head-box">
                                <h1>Dashboard</h1>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="number-box">
                <div class="row">
                    <div class="col-lg-3 col-md-3 col-sm-6">
                        <div class="dash-content-box">
                            <div class="clients-box">
                                <a href="{{ route('services.list') }}">
                                    <h3>{{ $services }}</h3>
                                </a>
                            </div>
                            <p>Total Services</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-6">
                        <div class="dash-content-box">
                            <div class="clients-box">
                                <a href="">
                                    <h3>44</h3>
                                </a>
                            </div>
                            <p>Total Blogs</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-6">
                        <div class="dash-content-box">
                            <div class="clients-box">
                                <a href="">
                                    <h3>566</h3>
                                </a>
                            </div>
                            <p>Total Projects</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-6">
                        <div class="dash-content-box">
                            <div class="clients-box">
                                <a href="{{ route('enquiry') }}">
                                    <h3>{{ $enquiries }}</h3>
                                </a>
                            </div>
                            <p>Total Queries</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @push('scripts')
        @endpush
    @endsection
