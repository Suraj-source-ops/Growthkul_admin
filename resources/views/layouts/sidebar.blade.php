<section class="main_sidebar_section" id="sidebarScroll">
    <img id="close-menu" class="close-icon" src="{{ url('/assets/images/close.svg') }}" alt="Close Menu" />


    {{-- Admin Routes --}}
    <ul class="sidebar_menu">
        <div class="sidebar-box">
            <div class="top-content">
                <div class="header_left_box">
                    <img class="logo" src="{{ url('/assets/images/logo.png') }}" alt="Website Logo" />
                </div>
                {{-- @can('view-dashboard-sidebar') --}}
                    <li class="">
                        <a href="{{ route('dashboard') }}" class="{{ Route::is('dashboard') ? 'active' : '' }}">
                            <span class="sidebar-border"></span>
                            <img class="show_icon" src="{{ url('/assets/images/sidebar-icon/dashboard.png') }}"
                                alt="Dashboard-icon" />
                            <img class="sidebar-hover-icon"
                                src="{{ url('/assets/images/sidebar-icon/dashboard-hover.png') }}" alt="Dashboard-icon" />
                            Dashboard
                        </a>
                    </li>
                {{-- @endcan --}}
                <div id="main">
                    <div class="accordion" id="faq">
                        @can('view-team-details-section-sidebar')
                            <div class="car">
                                <li class="card_drop" id="ReportTab">
                                    <a class="btn btn-header-link" data-toggle="collapse" data-target="#Reports"
                                        aria-expanded="true" aria-controls="Reports" id="rep_a">
                                        <span class="sidebar-border"></span>
                                        <img class="show_icon" src="{{ url('/assets/images/sidebar-icon/team-lead.png') }}"
                                            alt="Reports-icon" />
                                        <img class="sidebar-hover-icon"
                                            src="{{ url('/assets/images/sidebar-icon/team-lead-hover.png') }}"
                                            alt="Dashboard-icon" />
                                        Team Details
                                    </a>
                                </li>
                                <div id="Reports" class="collapse" aria-labelledby="ReportTab" data-parent="#faq">
                                    <div class="card-body sidebar_drop_body">
                                        @can('view-team-sidebar')
                                            <li class="">
                                                <a href="{{ route('teams') }}"
                                                    class="{{ Route::is('teams') || Route::is('add.teams') ? 'active' : '' }}">
                                                    <span class="sidebar-border2"></span>
                                                    <img class="right-icon-view"
                                                        src="{{ asset('/assets/images/sidebar-icon/right-icon.png') }}"
                                                        alt="sidebar-icon" />
                                                    <img class="right-icon-view-hover"
                                                        src="{{ asset('/assets/images/sidebar-icon/right-hover.png') }}"
                                                        alt="sidebar-icon" />
                                                    Team
                                                </a>
                                            </li>
                                        @endcan
                                        @can('view-members-sidebar')
                                            <li class="">
                                                <a href="{{ route('members') }}" id="reportListing2"
                                                    class="{{ Route::is('members') || Route::is('add.members') ? 'active' : '' }}">
                                                    <span class="sidebar-border2"></span>
                                                    <img class="right-icon-view"
                                                        src="{{ asset('/assets/images/sidebar-icon/right-icon.png') }}"
                                                        alt="sidebar-icon" />
                                                    <img class="right-icon-view-hover"
                                                        src="{{ asset('/assets/images/sidebar-icon/right-hover.png') }}"
                                                        alt="sidebar-icon" />
                                                    Members
                                                </a>
                                            </li>
                                        @endcan
                                    </div>
                                </div>
                            </div>
                        @endcan
                        @can('view-client-details-sidebar')
                            <li class="">
                                <a href="{{ route('clients') }}"
                                    class="{{ Route::is('clients') || Route::is('add.client') || Route::is('client.details') ? ' active' : '' }}">
                                    <span class="sidebar-border"></span>
                                    <img class="show_icon" src="{{ url('/assets/images/sidebar-icon/client.png') }}"
                                        alt="Dashboard-icon" />
                                    <img class="sidebar-hover-icon"
                                        src="{{ url('/assets/images/sidebar-icon/client-hover.png') }}"
                                        alt="Dashboard-icon" />
                                    Client Details
                                </a>
                            </li>
                        @endcan
                        @can('view-products-details-section-sidebar')
                            <div class="car">
                                <li class="card_drop" id="Settings">
                                    <a href="#" class="btn btn-header-link" data-toggle="collapse"
                                        data-target="#settingsDrop" aria-expanded="true" aria-controls="settingsDrop">
                                        <span class="sidebar-border"></span>
                                        <img class="show_icon" src="{{ url('/assets/images/sidebar-icon/product.png') }}"
                                            alt="settings-icon" />
                                        <img class="sidebar-hover-icon"
                                            src="{{ url('/assets/images/sidebar-icon/product-hover.png') }}"
                                            alt="Dashboard-icon" />
                                        Products
                                    </a>
                                </li>

                                <div id="settingsDrop" class="collapse" aria-labelledby="Settings" data-parent="#faq">
                                    <div class="card-body sidebar_drop_body">
                                        @can('view-products-sidebar')
                                            <li class="">
                                                <a href="{{ route('product.lists') }}" id="settingListing8"
                                                    class="{{ route::is('product.lists') ? ' active' : '' }}">
                                                    <span class="sidebar-border2"></span>
                                                    <img class="right-icon-view"
                                                        src="{{ asset('/assets/images/sidebar-icon/right-icon.png') }}"
                                                        alt="sidebar-icon" />
                                                    <img class="right-icon-view-hover"
                                                        src="{{ asset('/assets/images/sidebar-icon/right-hover.png') }}"
                                                        alt="sidebar-icon" />
                                                    Products
                                                </a>
                                            </li>
                                        @endcan
                                        @can('view-product-tracking-sidebar')
                                            <li class="">
                                                <a href="{{ route('product.track.lists') }}" id="settingListing1"
                                                    class="{{ route::is('product.track.lists') ? ' active' : '' }}">
                                                    <span class="sidebar-border2"></span>
                                                    <img class="right-icon-view"
                                                        src="{{ asset('/assets/images/sidebar-icon/right-icon.png') }}"
                                                        alt="sidebar-icon" />
                                                    <img class="right-icon-view-hover"
                                                        src="{{ asset('/assets/images/sidebar-icon/right-hover.png') }}"
                                                        alt="sidebar-icon" />
                                                    Products Tracking
                                                </a>
                                            </li>
                                        @endcan
                                    </div>
                                </div>
                            </div>
                        @endcan
                        @can('view-tasks-section-sidebar')
                            <div class="car">
                                <li class="card_drop" id="tasks">
                                    <a href="#" class="btn btn-header-link" data-toggle="collapse"
                                        data-target="#taskDrop" aria-expanded="true" aria-controls="taskDrop">
                                        <span class="sidebar-border"></span>
                                        <img class="show_icon" src="{{ url('/assets/images/sidebar-icon/task.png') }}"
                                            alt="Dashboard-icon" />
                                        <img class="sidebar-hover-icon"
                                            src="{{ url('/assets/images/sidebar-icon/task-hover.png') }}"
                                            alt="Dashboard-icon" />
                                        Tasks
                                    </a>
                                </li>

                                <div id="taskDrop" class="collapse" aria-labelledby="tasks" data-parent="#faq">
                                    <div class="card-body sidebar_drop_body">
                                        @can('view-all-tasks-sidebar')
                                            <li class="">
                                                <a href="{{ route('tasks') }}" id="settingListing8"
                                                    class="{{ route::is('tasks') ? 'active' : '' }}">
                                                    <span class="sidebar-border2"></span>
                                                    <img class="right-icon-view"
                                                        src="{{ asset('/assets/images/sidebar-icon/right-icon.png') }}"
                                                        alt="sidebar-icon" />
                                                    <img class="right-icon-view-hover"
                                                        src="{{ asset('/assets/images/sidebar-icon/right-hover.png') }}"
                                                        alt="sidebar-icon" />
                                                    All Tasks
                                                </a>
                                            </li>
                                        @endcan
                                        @if (Auth::user()->email != config('global.useremail'))
                                            @can('view-my-tasks-sidebar')
                                                <li class="">
                                                    <a href="{{ route('mytasks') }}" id="settingListing1"
                                                        class="{{ route::is('mytasks') ? 'active' : '' }}">
                                                        <span class="sidebar-border2"></span>
                                                        <img class="right-icon-view"
                                                            src="{{ asset('/assets/images/sidebar-icon/right-icon.png') }}"
                                                            alt="sidebar-icon" />
                                                        <img class="right-icon-view-hover"
                                                            src="{{ asset('/assets/images/sidebar-icon/right-hover.png') }}"
                                                            alt="sidebar-icon" />
                                                        My Tasks
                                                    </a>
                                                </li>
                                            @endcan
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endcan
                        @can('view-role-and-permission-section-sidebar')
                            <div class="car">
                                <li class="card_drop" id="Settings2">
                                    <a href="#" class="btn btn-header-link" data-toggle="collapse"
                                        data-target="#settingsDrop2" aria-expanded="true" aria-controls="settingsDrop2">
                                        <span class="sidebar-border"></span>
                                        <img class="show_icon" src="{{ url('/assets/images/sidebar-icon/role.png') }}"
                                            alt="Dashboard-icon" />
                                        <img class="sidebar-hover-icon"
                                            src="{{ url('/assets/images/sidebar-icon/role-hover.png') }}"
                                            alt="Dashboard-icon" />
                                        Roles & Permissions
                                    </a>
                                </li>

                                <div id="settingsDrop2" class="collapse" aria-labelledby="Settings2" data-parent="#faq">
                                    <div class="card-body sidebar_drop_body">
                                        <li class="">
                                            <a href="{{ route('roles') }}" id="settingListing8"
                                                class="{{ route::is('roles') ? ' active' : '' }}">
                                                <span class="sidebar-border2"></span>
                                                <img class="right-icon-view"
                                                    src="{{ asset('/assets/images/sidebar-icon/right-icon.png') }}"
                                                    alt="sidebar-icon" />
                                                <img class="right-icon-view-hover"
                                                    src="{{ asset('/assets/images/sidebar-icon/right-hover.png') }}"
                                                    alt="sidebar-icon" />
                                                Roles
                                            </a>
                                        </li>
                                    </div>
                                </div>
                            </div>
                        @endcan
                        @can('view-settings-section-sidebar')
                            <div class="car">
                                <li class="card_drop" id="Settings3">
                                    <a href="#" class="btn btn-header-link" data-toggle="collapse"
                                        data-target="#settingsDrop3" aria-expanded="true" aria-controls="settingsDrop3">
                                        <span class="sidebar-border"></span>
                                        <img class="right-icon-view"
                                            src="{{ asset('/assets/images/sidebar-icon/setting.png') }}"
                                            alt="sidebar-icon" />
                                        <img class="right-icon-view-hover"
                                            src="{{ asset('/assets/images/sidebar-icon/setting-hover.png') }}"
                                            alt="sidebar-icon" />
                                        Settings
                                    </a>
                                </li>

                                <div id="settingsDrop3" class="collapse" aria-labelledby="Settings3" data-parent="#faq">
                                    <div class="card-body sidebar_drop_body">
                                        @can('view-graphic-product-type-sidebar')
                                            <li class="">
                                                <a href="{{ route('graphic.product.types') }}" id="settingListing8"
                                                    class="{{ Route::is('graphic.product.types') ? ' active' : '' }}">
                                                    <span class="sidebar-border2"></span>
                                                    <img class="right-icon-view"
                                                        src="{{ asset('/assets/images/sidebar-icon/right-icon.png') }}"
                                                        alt="sidebar-icon" />
                                                    <img class="right-icon-view-hover"
                                                        src="{{ asset('/assets/images/sidebar-icon/right-hover.png') }}"
                                                        alt="sidebar-icon" />
                                                    Product's Graphic Type
                                                </a>
                                            </li>
                                        @endcan
                                        @can('view-master-product-track-sidebar')
                                            <li class="">
                                                <a href="{{ route('master.stages') }}" id="settingListing8"
                                                    class="{{ Route::is('master.stages') ? ' active' : '' }}">
                                                    <span class="sidebar-border2"></span>
                                                    <img class="right-icon-view"
                                                        src="{{ asset('/assets/images/sidebar-icon/right-icon.png') }}"
                                                        alt="sidebar-icon" />
                                                    <img class="right-icon-view-hover"
                                                        src="{{ asset('/assets/images/sidebar-icon/right-hover.png') }}"
                                                        alt="sidebar-icon" />
                                                    Master Product Track Stages
                                                </a>
                                            </li>
                                        @endcan
                                    </div>
                                </div>
                            </div>
                        @endcan
                    </div>
                </div>
            </div>
            <div class="bottom-content">
                <li class="">
                    <a href="{{ route('logout') }}" class="logout-btn">
                        <span class="sidebar-border"></span>
                        <img class="right-icon-view" src="{{ asset('/assets/images/sidebar-icon/logout.png') }}"
                            alt="sidebar-icon" />
                        <img class="right-icon-view-hover"
                            src="{{ asset('/assets/images/sidebar-icon/logout-hover.png') }}" alt="sidebar-icon" />
                        Logout
                    </a>
                </li>
            </div>
        </div>
    </ul>

</section>
