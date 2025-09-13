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
                    {{-- @can('view-total-clients-dashboard') --}}
                    <div class="col-lg-3 col-md-3 col-sm-6">
                        <div class="dash-content-box">
                            <div class="clients-box">
                                <a href="">
                                    <h3>11111</h3>
                                </a>
                            </div>
                            <p>Total Services</p>
                        </div>
                    </div>
                    {{-- @endcan --}}
                    {{-- @can('view-total-products-dashboard') --}}
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
                    {{-- @endcan --}}
                    {{-- @can('view-pending-products-dashboard') --}}
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
                    {{-- @endcan --}}
                    {{-- @can('view-complete-products-dashboard') --}}
                    <div class="col-lg-3 col-md-3 col-sm-6">
                        <div class="dash-content-box">
                            <div class="clients-box">
                                <a href="{{ route('product.lists') }}">
                                    <h3>{{ $completedCount }}</h3>
                                </a>
                            </div>
                            <p>Total Queries</p>
                        </div>
                    </div>
                    {{-- @endcan --}}
                </div>
            </div>
        </div>
        @push('scripts')
        @endpush
    @endsection
