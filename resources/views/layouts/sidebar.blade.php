<section class="main_sidebar_section" id="sidebarScroll">
    <img id="close-menu" class="close-icon" src="{{ url('/assets/images/close.svg') }}" alt="Close Menu" />


    {{-- Admin Routes --}}
    <ul class="sidebar_menu">
        <div class="sidebar-box">
            <div class="top-content">
                <div class="header_left_box">
                    <img class="logo" src="{{ url('/assets/images/logo.png') }}" alt="Website Logo" />
                </div>
                <li class="">
                    <a href="{{ route('dashboard') }}" class="{{ Route::is('dashboard') ? 'active' : '' }}">
                        <span class="sidebar-border"></span>
                        <img class="show_icon" src="{{ url('/assets/images/sidebar-icon/dashboard-hover.png') }}"
                            alt="Dashboard-icon" />
                        <img class="sidebar-hover-icon"
                            src="{{ url('/assets/images/sidebar-icon/dashboard-hover.png') }}" alt="Dashboard-icon" />
                        Dashboard
                    </a>
                </li>
                <div id="main">
                    <div class="accordion" id="faq">
                        <div class="car">
                            <li class="card_drop" id="ReportTab">
                                <a class="btn btn-header-link" data-toggle="collapse" data-target="#Reports"
                                    aria-expanded="true" aria-controls="Reports" id="rep_a">
                                    <span class="sidebar-border"></span>
                                    <img class="show_icon"
                                        src="{{ url('/assets/images/sidebar-icon/team-lead-hover.png') }}"
                                        alt="Reports-icon" />
                                    <img class="sidebar-hover-icon"
                                        src="{{ url('/assets/images/sidebar-icon/team-lead-hover.png') }}"
                                        alt="Dashboard-icon" />
                                    Team Details
                                </a>
                            </li>
                            <div id="Reports" class="collapse" aria-labelledby="ReportTab" data-parent="#faq">
                                <div class="card-body sidebar_drop_body">
                                    <li class="">
                                        <a href="{{ route('teams') }}"
                                            class="{{ Route::is('teams') || Route::is('add.teams') ? 'active' : '' }}">
                                            <span class="sidebar-border2"></span>
                                            <img class="right-icon-view"
                                                src="{{ asset('/assets/images/sidebar-icon/right-hover.png') }}"
                                                alt="sidebar-icon" />
                                            <img class="right-icon-view-hover"
                                                src="{{ asset('/assets/images/sidebar-icon/right-hover.png') }}"
                                                alt="sidebar-icon" />
                                            Team
                                        </a>
                                    </li>
                                    <li class="">
                                        <a href="{{ route('members') }}" id="reportListing2"
                                            class="{{ Route::is('members') || Route::is('add.members') ? 'active' : '' }}">
                                            <span class="sidebar-border2"></span>
                                            <img class="right-icon-view"
                                                src="{{ asset('/assets/images/sidebar-icon/right-hover.png') }}"
                                                alt="sidebar-icon" />
                                            <img class="right-icon-view-hover"
                                                src="{{ asset('/assets/images/sidebar-icon/right-hover.png') }}"
                                                alt="sidebar-icon" />
                                            Members
                                        </a>
                                    </li>
                                </div>
                            </div>
                        </div>
                        <div class="car">
                            <li class="card_drop" id="Settings3">
                                <a href="#" class="btn btn-header-link" data-toggle="collapse"
                                    data-target="#settingsDrop3" aria-expanded="true" aria-controls="settingsDrop3">
                                    <span class="sidebar-border"></span>
                                    <img class="right-icon-view"
                                        src="{{ asset('/assets/images/sidebar-icon/setting-hover.png') }}"
                                        alt="sidebar-icon" />
                                    <img class="right-icon-view-hover"
                                        src="{{ asset('/assets/images/sidebar-icon/setting-hover.png') }}"
                                        alt="sidebar-icon" />
                                    Services
                                </a>
                            </li>

                            <div id="settingsDrop3" class="collapse" aria-labelledby="Settings3" data-parent="#faq">
                                <div class="card-body sidebar_drop_body">
                                    <li class="">
                                        <a href="{{ route('services.list') }}" id="settingListing8"
                                            class="{{ Route::is('services.list') ? ' active' : '' }}">
                                            <span class="sidebar-border2"></span>
                                            <img class="right-icon-view"
                                                src="{{ asset('/assets/images/sidebar-icon/right-hover.png') }}"
                                                alt="sidebar-icon" />
                                            <img class="right-icon-view-hover"
                                                src="{{ asset('/assets/images/sidebar-icon/right-hover.png') }}"
                                                alt="sidebar-icon" />
                                            Service Details
                                        </a>
                                    </li>
                                </div>
                            </div>
                        </div>
                        <li class="">
                            <a href="{{ route('enquiry') }}" class="{{ Route::is('enquiry') ? ' active' : '' }}">
                                <span class="sidebar-border"></span>
                                <img class="show_icon"
                                    src="{{ url('/assets/images/sidebar-icon/client-hover.png') }}"
                                    alt="Dashboard-icon" />
                                <img class="sidebar-hover-icon"
                                    src="{{ url('/assets/images/sidebar-icon/client-hover.png') }}"
                                    alt="Dashboard-icon" />
                                Enquiry
                            </a>
                        </li>
                        <li class="">
                            <a href="{{ route('projects') }}"
                                class="{{ Route::is('projects') || Route::is('add.project') || Route::is('edit.project.details') ? ' active' : '' }}">
                                <span class="sidebar-border"></span>
                                <img class="show_icon"
                                    src="{{ url('/assets/images/sidebar-icon/role-hover.png') }}"
                                    alt="Dashboard-icon" />
                                <img class="sidebar-hover-icon"
                                    src="{{ url('/assets/images/sidebar-icon/role-hover.png') }}"
                                    alt="Dashboard-icon" />
                                Projects
                            </a>
                        </li>
                        <li class="">
                            <a href="{{ route('blogs') }}"
                                class="{{ Route::is('blogs') || Route::is('add.blog') || Route::is('edit.blog.details') ? ' active' : '' }}">
                                <span class="sidebar-border"></span>
                                <img class="show_icon"
                                    src="{{ url('/assets/images/sidebar-icon/client-hover.png') }}"
                                    alt="Dashboard-icon" />
                                <img class="sidebar-hover-icon"
                                    src="{{ url('/assets/images/sidebar-icon/client-hover.png') }}"
                                    alt="Dashboard-icon" />
                                Blogs
                            </a>
                        </li>
                        <div class="car">
                            <li class="card_drop" id="Settings2">
                                <a href="#" class="btn btn-header-link" data-toggle="collapse"
                                    data-target="#settingsDrop2" aria-expanded="true" aria-controls="settingsDrop2">
                                    <span class="sidebar-border"></span>
                                    <img class="show_icon"
                                        src="{{ url('/assets/images/sidebar-icon/role-hover.png') }}"
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
                                                src="{{ asset('/assets/images/sidebar-icon/right-hover.png') }}"
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
                    </div>
                </div>
            </div>
            <div class="bottom-content">
                <li class="">
                    <a href="{{ route('logout') }}" class="logout-btn">
                        <span class="sidebar-border"></span>
                        <img class="right-icon-view"
                            src="{{ asset('/assets/images/sidebar-icon/logout-hover.png') }}" alt="sidebar-icon" />
                        <img class="right-icon-view-hover"
                            src="{{ asset('/assets/images/sidebar-icon/logout-hover.png') }}" alt="sidebar-icon" />
                        Logout
                    </a>
                </li>
            </div>
        </div>
    </ul>

</section>
