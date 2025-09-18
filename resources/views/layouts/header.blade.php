<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Growthkul @yield('title')</title>
    <link rel="shortcut icon" type="image/png" href="" />  {{-- set fevicon icon --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/common.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.6.0/css/all.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/css/bootstrap4-toggle.min.css"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/rowreorder/1.5.0/css/rowReorder.dataTables.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/semantic-ui@2.2.13/dist/semantic.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.css" rel="stylesheet">
    <script>
        var base_url = '{{ url('/admin') }}';
        var csrf_token = "{{ csrf_token() }}";
    </script>
</head>

<body>
    @php
    $color = '#' . substr(md5(Auth::user()->name), 0, 6);
    @endphp
    <header class="main_header_section">
        <div class="web-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-6 col-sm-6 col-6">

                    </div>
                    <div class="col-md-6 col-sm-6 col-6">
                        <div class="header_right_box">
                            <div class="notification-image">
                                <a href="#">
                                    <img src="{{ asset('assets\images\notification-icons.png') }}"
                                        onclick="toggleNotification()" alt="bell-icons" />
                                </a>
                                <img class="notification-icons"
                                    src="{{ asset('assets\images\notification-icons.png') }}" alt="bell-icons" />
                                <div class="notification-box" id="notification" style="display: none;">
                                    <div class="notification-head">
                                        <h3>Notification</h3>
                                        <div class="cross-btns" onclick="toggleNotification()">
                                            <img src="{{ asset('/assets/images/cross.png') }}" alt="back-icon" />
                                        </div>
                                    </div>
                                    <div class="notification-content-box-main">
                                        <table id="notificationTable" class="table table-sm table-borderless"
                                            style="width: 100%;">
                                            <tbody></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="dropdown">
                                <button class="header_drop_btn dropdown-toggle" type="button" data-toggle="dropdown">
                                    @if (isset(Auth::user()->profile->file_path))
                                    <img src="{{ asset(isset(Auth::user()->profile->file_path) ? Auth::user()->profile->file_path : 'assets\images\dummy-user.png') }}"
                                        alt="user-icon" /> {{ Auth::user()->name }}
                                    @else
                                    <div class="circle-name" style="background-color: {{ $color }};">
                                        <p>{{ strtoupper(substr(Auth::user()->name, 0, 2)) }}</p>
                                    </div>
                                    @endif
                                </button>
                                <div class="dropdown-menu header_admin_drop">
                                    <div class="admin_profile_box">
                                        @if (isset(Auth::user()->profile->file_path))
                                        <img src="{{ asset(isset(Auth::user()->profile->file_path) ? Auth::user()->profile->file_path : 'assets\images\dummy-user.png') }}"
                                            alt="user-icon" />
                                        @else
                                        <div class="circle-name" style="background-color: {{ $color }};">
                                            <p>{{ strtoupper(substr(Auth::user()->name, 0, 2)) }}</p>
                                        </div>
                                        @endif
                                        <div>
                                            <h3>{{ Auth::user()->name }}</h3>
                                            <a href="#">{{ Auth::user()->email }}</a>
                                        </div>
                                    </div>
                                    <div class="profile_logout_btn">
                                        {{-- <a class="dropdown-item Profile_bor">Profile</a> --}}
                                        <a class="dropdown-item" href="{{ route('logout') }}">Logout</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mob-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-6 col-sm-6 col-6">
                        <div class="menu-icon">
                            <img id="mob-menu" src="{{ url('/assets/images/menu.png') }}" alt="Menu" />
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-6 col-6 text-right">
                        <div class="header_right_box">
                            <div class="dropdown">
                                <button class="header_drop_btn dropdown-toggle" type="button" data-toggle="dropdown">
                                    @if (isset(Auth::user()->profile->file_path))
                                    <img src="{{ asset(isset(Auth::user()->profile->file_path) ? Auth::user()->profile->file_path : 'assets\images\dummy-user.png') }}"
                                        alt="user-icon" /> {{ Auth::user()->name }}
                                    @else
                                    <div class="circle-name" style="background-color: {{ $color }};">
                                        <p>{{ strtoupper(substr(Auth::user()->name, 0, 2)) }}</p>
                                    </div>
                                    @endif
                                </button>
                                <div class="dropdown-menu header_admin_drop">
                                    <div class="admin_profile_box">
                                        @if (isset(Auth::user()->profile->file_path))
                                        <img src="{{ asset(isset(Auth::user()->profile->file_path) ? Auth::user()->profile->file_path : 'assets\images\dummy-user.png') }}"
                                            alt="user-icon" />
                                        @else
                                        <div class="circle-name" style="background-color: {{ $color }};">
                                            <p>{{ strtoupper(substr(Auth::user()->name, 0, 2)) }}</p>
                                        </div>
                                        @endif
                                        <div>
                                            <h3>{{ Auth::user()->name }}</h3>
                                            <a href="#">{{ Auth::user()->email }}</a>
                                        </div>
                                    </div>
                                    <div class="profile_logout_btn">
                                        {{-- <a class="dropdown-item Profile_bor" href="#">Profile</a> --}}
                                        <a class="dropdown-item" href="{{ route('logout') }}">Logout</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    @include('layouts.sidebar')
    @yield('main-content')
    <div id="fullscreenLoader" style="display:none;">
        <div class="loader-backdrop">
            <div class="loader-content">
                <div class="spinner"></div>
                <div id="loaderText">Uploading, Please wait...</div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/js/bootstrap4-toggle.min.js"></script>
    <script
        src="https://cdn.rawgit.com/mdehoog/Semantic-UI/6e6d051d47b598ebab05857545f242caf2b4b48c/dist/semantic.min.js">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/semantic-ui@2.2.13/dist/semantic.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="https://cdn.datatables.net/rowreorder/1.5.0/js/dataTables.rowReorder.min.js"></script>
    <script src="https://cdn.datatables.net/rowreorder/1.5.0/js/rowReorder.dataTables.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.js"></script>
    <script>
        toastr.options = {
            closeButton: true,
            preventDuplicates: true,
            timeOut: 3000,
        }
    </script>

    @stack('scripts')
    <script>
        @if (Session::has('message'))
            toastr.options = {
                closeButton: true,
                preventDuplicates: true,
                timeOut: 3000
            };
            var type = "{{ Session::get('alert-type', 'info') }}";
            var message = "{{ Session::get('message') }}";
            toastr[type](message);
        @endif
    </script>
    <script>
        $("#mob-menu").on("click", function() {
            $(".main_sidebar_section").addClass("addsidebar");
        });
    </script>
    <script>
        $('#rangestart').calendar({
            type: 'date',
            endCalendar: $('#rangeend')
        });
        $('#rangeend').calendar({
            type: 'date',
            startCalendar: $('#rangestart')
        });
    </script>
    <script>
        $("#close-menu").on("click", function() {
            $(".main_sidebar_section").removeClass("addsidebar");
        });
    </script>
    <script>
        $(document).ready(function() {
            let currentUrl = window.location.href;
            let urlParts = currentUrl.split("/");
            return false;
        });
    </script>
    {{-- multiple selection dropdown start --}}
    <script>
        $(document).ready(function() {
            $('.label.ui.dropdown').dropdown();
        });
    </script>
    {{-- multiple selection dropdown end --}}

    {{-- file and dropdown selection js for product edit table --}}

    <!-- Modal -->
    <script>
        function toggleNotification() {
            $('#notificationTable').DataTable().ajax.reload();
            const box = document.getElementById('notification');
            if (box.style.display === "none" || box.style.display === "") {
                box.style.display = "block";
            } else {
                box.style.display = "none";
            }
        }
    </script>

</body>

</html>
