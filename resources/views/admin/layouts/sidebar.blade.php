<div id="sidebar-menu">
    <ul class="metismenu list-unstyled" id="side-menu">
        @if (authUserRole() == 'admin')
            <li>
                <a href="{{ route('admin.dashboard') }}" class="waves-effect">
                    <i class="bx bx-home-circle"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <li class="{{ isMainMenuActive('attendance-logs') }}">
                <a href="{{ route('admin.attendance-logs') }}" class="waves-effect">
                    <i class="bx bx-timer"></i>
                    <span>Attendance Logs</span>
                </a>
            </li>

            <li class="{{ isMainMenuActive('staffs') }}">
                <a href="javascript: void(0);" class="has-arrow waves-effect">
                    <i class="bx bx-user"></i>
                    <span>Employee/Staff</span>
                </a>
                <ul class="sub-menu" aria-expanded="false">
                    <li>
                        <a href="{{ route('admin.staffs.index') }}" class="{{ isSubMenuActive('staffs') }}">
                            <i class="bx bx-chevron-right"></i> Staff List
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.staffs.create') }}" class="{{ isSubMenuActive('staffs') }}">
                            <i class="bx bx-chevron-right"></i> Create Staff
                        </a>
                    </li>
                </ul>
            </li>
            <li class="{{ isMainMenuActive('designations,departments,branches') }}">
                <a href="javascript: void(0);" class="has-arrow waves-effect">
                    <i class="bx bx-certification"></i>
                    <span>HR Configuration</span>
                </a>
                <ul class="sub-menu" aria-expanded="false">
                    <li>
                        <a href="{{ route('admin.designations.index') }}" class="{{ isSubMenuActive('designations') }}">
                            <i class="bx bx-chevron-right"></i> Designations
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.departments.index') }}" class="{{ isSubMenuActive('departments') }}">
                            <i class="bx bx-chevron-right"></i> Departments
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.branches.index') }}" class="{{ isSubMenuActive('branches') }}">
                            <i class="bx bx-chevron-right"></i> Branches
                        </a>
                    </li>
                </ul>
            </li>

            <li class="{{ isMainMenuActive('site-settings') }}">
                <a href="javascript: void(0);" class="has-arrow waves-effect">
                    <i class="bx bx-cog"></i>
                    <span>Settings</span>
                </a>
                <ul class="sub-menu" aria-expanded="false">
                    <li>
                        <a href="{{ route('admin.site-settings') }}" class="{{ isSubMenuActive('site-settings') }}">
                            <i class="bx bx-chevron-right"></i> Site Settings
                        </a>
                    </li>

                </ul>
            </li>
        @endif

        @if (authUserRole() === 'employee')
            <li class="{{ isMainMenuActive('attendance-summery') }}">
                <a href="{{ route('admin.attendance-summery') }}" class="waves-effect">
                    <i class="bx bx-timer"></i>
                    <span>Attendance Summery</span>
                </a>
            </li>
        @endif
    </ul>
</div>
