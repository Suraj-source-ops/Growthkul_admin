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
                            <form action="{{ route('dashboard') }}" method="GET" id="filterForm" autocomplete="off">
                            <div class="date-picker">
                                <div class="date-picker-box">
                                    <div class="field">
                                        <div class="ui calendar" id="rangestart">
                                            <p>From</p>
                                            <div class="ui input left icon">
                                                <i class="calendar icon"></i>
                                                        <input type="text" name="start_date"
                                                            value="{{ request('start_date') }}" placeholder="Start">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="field">
                                        <div class="ui calendar" id="rangeend">
                                            <p>To</p>
                                            <div class="ui input left icon">
                                                <i class="calendar icon"></i>
                                                        <input type="text" name="end_date"
                                                            value="{{ request('end_date') }}" placeholder="End">
                                            </div>
                                        </div>
                                    </div>

                                    <input type="hidden" name="filter" id="selectedFilter"
                                        value="{{ request('filter', null) }}">

                                    <div class="filter-btn-box">
                                        <button class="filter-btn">Filter</button>
                                    </div>
                                    <div class="dropdowns" id="dropdowns">
                                        <div class="select-box" id="selected">
                                            {{ request('filter', 'Select Type') }}
                                        </div>
                                        <div class="options-container">
                                            @foreach ($filterOptions as $option)
                                                <div class="option {{ $selectedFilter === $option ? 'active' : '' }}">
                                                    {{ $option }}</div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                        </div>

                        
                    </div>
                </div>
            </div>
            <div class="number-box">
                <div class="row">
                    @can('view-total-clients-dashboard')
                        <div class="col-lg-3 col-md-3 col-sm-6">
                            <div class="dash-content-box">
                                <div class="clients-box">
                                    <a href="{{ route('clients') }}">
                                        <h3>{{ $clients }}</h3>
                                    </a>
                                    <img class="" src="{{ asset('/assets/images/total-client.png') }}" alt="back-icon" />
                                </div>
                                <p>Total Clients</p>
                            </div>
                        </div>
                    @endcan
                    @can('view-total-products-dashboard')
                        <div class="col-lg-3 col-md-3 col-sm-6">
                            <div class="dash-content-box">
                                <div class="clients-box">
                                    <a href="{{ route('product.lists') }}">
                                        <h3>{{ $products }}</h3>
                                    </a>
                                    <img class="" src="{{ asset('/assets/images/total-product.png') }}"
                                        alt="back-icon" />
                                </div>
                                <p>Total Products</p>
                            </div>
                        </div>
                    @endcan
                    @can('view-pending-products-dashboard')
                        <div class="col-lg-3 col-md-3 col-sm-6">
                            <div class="dash-content-box">
                                <div class="clients-box">
                                    <a href="{{ route('product.lists') }}">
                                        <h3>{{ $inProgressCount }}</h3>
                                    </a>
                                    <img class="" src="{{ asset('/assets/images/pending-products.png') }}"
                                        alt="back-icon" />
                                </div>
                                <p>In Progress Products</p>
                            </div>
                        </div>
                    @endcan
                    @can('view-complete-products-dashboard')
                        <div class="col-lg-3 col-md-3 col-sm-6">
                            <div class="dash-content-box">
                                <div class="clients-box">
                                    <a href="{{ route('product.lists') }}">
                                        <h3>{{ $completedCount }}</h3>
                                    </a>
                                    <img class="" src="{{ asset('/assets/images/complete-products.png') }}"
                                        alt="back-icon" />
                                </div>
                                <p>Complete Products</p>
                            </div>
                        </div>
                    @endcan

                </div>
            </div>
        </div>


        @push('scripts')
            <script>
                const dropdown = document.getElementById('dropdowns');
                const selected = document.getElementById('selected');
                const options = dropdown.querySelectorAll('.option');
                let selectedValue = selected.textContent;
                selected.addEventListener('click', () => {
                    dropdown.classList.toggle('active');
                });

                options.forEach(option => {
                    option.addEventListener('click', () => {
                        options.forEach(o => o.classList.remove('active'));
                        option.classList.add('active');
                        selected.textContent = option.textContent.trim();
                        dropdown.classList.remove('active');
                        selectedValue = option.textContent.trim();
                        if (selectedValue) {
                            window.location.href = base_url + `/?filter=${encodeURIComponent(selectedValue)}`
                        }
                    });
                });

                // Close dropdown on outside click
                document.addEventListener('click', function(e) {
                    if (!dropdown.contains(e.target)) {
                        dropdown.classList.remove('active');
                    }
                });
            </script>
           
        @endpush
    @endsection
