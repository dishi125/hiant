<?php
// $currentRole = \App\Models\Role::getRoleName();
 //$permissionList = \App\Models\PermissionUser::getPermission();

    $user = \Auth::user();
    $fontColor = "style=color:#ea4c89";
    //dd($user);
?>
<aside id="sidebar-wrapper">
<!--    <div class="sidebar-brand">
        <a href="{!! env('app.url') !!}">
            <img src="{!! asset('img/MeAround-white.png') !!}" width="150" height="auto" alt="{!! config('app.name') !!}">
        </a>
    </div>-->
    <div class="sidebar-brand sidebar-brand-sm">
        <a href="{!! env('app.url') !!}">
            <img src="{!! asset('img/logo.png') !!}" width="60" height="60" alt="{!! config('app.name') !!}">
        </a>
    </div>
    <ul class="sidebar-menu mb-3">
        <li class="menu-header">{{ __('menu.dashboard') }}</li>
        <li class="{{ Request::segment(2) == 'dashboard' ? 'active' : '' }}">
            <a href="{{ route('admin.dashboard.index') }}" class="nav-link"><i class="fas fa-home"></i>
                <span>{{ __('menu.dashboard') }}</span></a>
        </li>

        @if ($user->hasRole("Admin"))
        <li class="{{ Request::segment(2) == 'keyword' ? 'active' : '' }}">
            <a href="{{ route('admin.keyword.index') }}" class="nav-link"><i class="fas fa-list"></i>
                <span>{{ __('menu.keyword') }}</span></a>
        </li>
        @endif

        @if ($user->hasRole("Admin"))
            <li class="{{ Request::segment(2) == 'category' ? 'active' : '' }}">
                <a href="{{ route('admin.category.index') }}" class="nav-link"><i class="fas fa-list"></i>
                    <span>{{ __('menu.category') }}</span></a>
            </li>
        @endif

        @if ($user->hasRole("Admin"))
            <li class="{{ Request::segment(2) == 'news-websites' ? 'active' : '' }}">
                <a href="{{ route('admin.news-websites.index') }}" class="nav-link"><i class="fas fa-list"></i>
                    <span>{{ __('menu.news_websites') }}</span></a>
            </li>
        @endif

        @if ($user->hasRole("Admin"))
            <li class="{{ Request::segment(2) == 'category-posts' ? 'active' : '' }}">
                <a href="{{ route('admin.category-posts.index') }}" class="nav-link"><i class="fas fa-list"></i>
                    <span>{{ __('menu.main_page_post') }}</span></a>
            </li>
        @endif

        @if ($user->can("important-custom-list"))
            <li class="{{ Request::segment(2) == 'important-setting' ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('admin.important-setting.links.index') }}"><i class="fas fa-chalkboard-teacher"></i>
                    <span>{{ __('menu.important_settings') }}</span></a>
            </li>
        @endif

        @if ($user->hasRole("Admin"))
            <li class="{{ Request::segment(2) == 'reasons-delete-account' ? 'active' : '' }}">
                <a href="{{ route('admin.reasons-delete-account.index') }}" class="nav-link"><i class="fas fa-list"></i>
                    <span>{{ __('menu.reasons_delete_account') }} <span class="reasons_delete_account_unread_count unread_count">0</span></span>
                </a>
            </li>
        @endif

        @if ($user->can("user-list"))
            <li class="{{ Request::segment(2) == 'users' ? 'active' : '' }}">
                <a href="{{ route('admin.user.index') }}" class="nav-link"><i class="fas fa-users"></i>
                    <span>{{ __('menu.user') }} <span class="user_unread_count unread_count">0</span></span></a>
            </li>
        @endif

        @if ($user->hasRole("Admin"))
            <li class="{{ Request::segment(2) == 'group-chat' ? 'active' : '' }}">
                <a href="{{ route('admin.group-chat.index') }}" class="nav-link"><i class="fas fa-list"></i>
                    <span>{{ __('menu.group-chat') }}</span></a>
            </li>
        @endif

        @if ($user->hasRole("Admin"))
            <li class="{{ Request::segment(2) == 'reported-post' ? 'active' : '' }}">
                <a href="{{ route('admin.reported-post.index') }}" class="nav-link"><i class="fas fa-list"></i>
                    <span>{{ __('menu.reported-post') }}</span></a>
            </li>
        @endif

        @if ($user->hasRole("Admin"))
            <li class="{{ Request::segment(2) == 'reported-users' ? 'active' : '' }}">
                <a href="{{ route('admin.reported-users.index') }}" class="nav-link"><i class="fas fa-list"></i>
                    <span>{{ __('menu.reported-users') }}</span></a>
            </li>
        @endif
    </ul>
</aside>
