<aside class="main-sidebar sidebar-dark-primary elevation-4" style="min-height: 917px;">
    <!-- Brand Logo -->
    <a href="#" class="brand-link">
        <span class="brand-text font-weight-light">{{ env('APP_NAME') }}</span>
    </a>
    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user (optional) -->
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar nav-child-indent flex-column" data-widget="treeview" role="menu"
                data-accordion="false">
                <li class="nav-item">
                    <form id="tenant-selector" action="{{ route('admin.tenants.active') }}" method="POST">
                        @csrf
                        <input type="hidden" name="next_url" value="{{ url()->current() }}">
                        {{-- <select class="form-control" name="company_id"
                            onchange="document.getElementById('tenant-selector').submit();">
                            <option value="">All Companies</option>
                            @foreach (tenancy()->getCompanies() as $company)
                                <option {{ tenancy()->activeCompanyIs($company) ? 'selected' : '' }}
                                    value="{{ $company->id }}">{{ ucfirst($company->name) }}</option>
                            @endforeach
                        </select> --}}
                        <input type="hidden" name="next_url" value="{{ url()->current() }}">
                        <select class="form-control" name="channel_id"
                            onchange="document.getElementById('tenant-selector').submit();">
                            <option value="">All Channels</option>
                            {{-- @if (tenancy()->getActiveCompany())
                                @foreach (tenancy()->getActiveCompany()->companyChannels as $tenant)
                                    <option {{ tenancy()->activeTenantIs($tenant) ? 'selected' : '' }}
                                        value="{{ $tenant->id }}">{{ ucfirst($tenant->name) }}</option>
                                @endforeach
                            @else --}}
                                @foreach (tenancy()->getTenants() as $tenant)
                                    <option {{ tenancy()->activeTenantIs($tenant) ? 'selected' : '' }}
                                        value="{{ $tenant->id }}">{{ ucfirst($tenant->name) }}</option>
                                @endforeach
                            {{-- @endif --}}
                        </select>
                    </form>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.home') }}">
                        <i class="fas fa-fw fa-tachometer-alt nav-icon">
                        </i>
                        <p>
                            {{ trans('global.dashboard') }}
                        </p>
                    </a>
                </li>
                @can('user_management_access')
                    <li
                        class="nav-item has-treeview {{ request()->is('admin/permissions*') ? 'menu-open' : '' }} {{ request()->is('admin/roles*') ? 'menu-open' : '' }} {{ request()->is('admin/users*') ? 'menu-open' : '' }} {{ request()->is('admin/audit-logs*') ? 'menu-open' : '' }} {{ request()->is('admin/user-alerts*') ? 'menu-open' : '' }}">
                        <a class="nav-link nav-dropdown-toggle" href="#">
                            <i class="fa-fw nav-icon fas fa-users">

                            </i>
                            <p>
                                {{ trans('cruds.userManagement.title') }}
                                <i class="right fa fa-fw fa-angle-left nav-icon"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('permission_access')
                                <li class="nav-item">
                                    <a href="{{ route('admin.permissions.index') }}"
                                        class="nav-link {{ request()->is('admin/permissions') || request()->is('admin/permissions/*') ? 'active' : '' }}">
                                        <i class="fa-fw nav-icon fas fa-unlock-alt">

                                        </i>
                                        <p>
                                            {{ trans('cruds.permission.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('role_access')
                                <li class="nav-item">
                                    <a href="{{ route('admin.roles.index') }}"
                                        class="nav-link {{ request()->is('admin/roles') || request()->is('admin/roles/*') ? 'active' : '' }}">
                                        <i class="fa-fw nav-icon fas fa-briefcase">

                                        </i>
                                        <p>
                                            {{ trans('cruds.role.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            @can('user_access')
                                <li class="nav-item">
                                    <a href="{{ route('admin.users.index') }}"
                                        class="nav-link {{ request()->is('admin/users') || request()->is('admin/users/*') ? 'active' : '' }}">
                                        <i class="fa-fw nav-icon fas fa-user">

                                        </i>
                                        <p>
                                            {{ trans('cruds.user.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan
                            {{-- @can('audit_log_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.audit-logs.index") }}"
                                       class="nav-link {{ request()->is("admin/audit-logs") || request()->is("admin/audit-logs/*") ? "active" : "" }}">
                                        <i class="fa-fw nav-icon fas fa-file-alt">

                                        </i>
                                        <p>
                                            {{ trans('cruds.auditLog.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan --}}
                            {{-- @can('user_alert_access')
                                <li class="nav-item">
                                    <a href="{{ route('admin.user-alerts.index') }}"
                                        class="nav-link {{ request()->is('admin/user-alerts') || request()->is('admin/user-alerts/*') ? 'active' : '' }}">
                                        <i class="fa-fw nav-icon fas fa-bell">

                                        </i>
                                        <p>
                                            {{ trans('cruds.userAlert.title') }}
                                        </p>
                                    </a>
                                </li>
                            @endcan --}}
                        </ul>
                    </li>
                @endcan
                @foreach (\App\Services\MenuService::menu() as $menu)
                    @can($menu->permission)
                        <li
                            class="nav-item has-treeview {{ request()->is(...$menu->getAllSubmenuRoutes()) ? 'menu-open' : '' }} ">
                            <a class="nav-link nav-dropdown-toggle" href="#">
                                <i class="fa-fw nav-icon {{ $menu->icon }}">

                                </i>
                                <p>
                                    {{ $menu->title }}
                                    <i class="right fa fa-fw fa-angle-left nav-icon"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                @foreach ($menu->submenus as $submenu)
                                    @can($submenu->permission)
                                        <li class="nav-item" data-toggle="tooltip" data-placement="top" title="Coming Soon">
                                            <a href="{{ $submenu->disabled ? '#' : $submenu->url }}" disabled
                                                class="nav-link {{ request()->is($submenu->path . '*') ? 'active' : '' }}">
                                                <i class="fa-fw nav-icon {{ $submenu->icon }}">

                                                </i>
                                                <p>
                                                    {{ $submenu->title }}
                                                    @if ($submenu->disabled)
                                                        <i class="fas fa-info-circle">
                                                        </i>
                                                    @endif
                                                </p>
                                            </a>
                                        </li>
                                    @endcan
                                @endforeach
                            </ul>
                        </li>
                    @endcan
                @endforeach
                @if (tenancy()->getUser()->is_dev)
                    <li class="nav-item has-treeview">
                        <a class="nav-link nav-dropdown-toggle" href="#">
                            <i class="fa-fw nav-icon fas fa-cogs">

                            </i>
                            <p>
                                {{ trans('cruds.developer.title') }}
                                <i class="right fa fa-fw fa-angle-left nav-icon"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ url('/admin/log-reader') }}" class="nav-link">
                                    <i class="fa-fw nav-icon fas fa-server">

                                    </i>
                                    <p>Log Reader</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                @endif
                {{-- @php($unread = \App\Models\QaMessageUser::unreadCount())
                <li class="nav-item">
                    <a href="{{ route('admin.messenger.index') }}"
                        class="{{ request()->is('admin/messenger') || request()->is('admin/messenger/*') ? 'active' : '' }} nav-link">
                        <i class="fa-fw fa fa-envelope nav-icon">

                        </i>
                        <p>{{ trans('global.messages') }}</p>
                        @if ($unread > 0)
                            <strong>( {{ $unread }} )</strong>
                        @endif

                    </a>
                </li> --}}
                @if (file_exists(app_path('Http/Controllers/Auth/ChangePasswordController.php')))
                    @can('profile_password_edit')
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('profile/password') || request()->is('profile/password/*') ? 'active' : '' }}"
                                href="{{ route('profile.password.edit') }}">
                                <i class="fa-fw fas fa-key nav-icon">
                                </i>
                                <p>
                                    {{ trans('global.change_password') }}
                                </p>
                            </a>
                        </li>
                    @endcan
                @endif
                <li class="nav-item">
                    <a href="#" class="nav-link"
                        onclick="event.preventDefault(); document.getElementById('logoutform').submit();">
                        <p>
                            <i class="fas fa-fw fa-sign-out-alt nav-icon">

                            </i>
                            <p>{{ trans('global.logout') }}</p>
                        </p>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
