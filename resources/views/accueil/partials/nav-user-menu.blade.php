@php
    $navUser = Auth::user();
    $navUserName = $navUser->full_name ?: $navUser->name;
    $navUserPhoto = $navUser->profile_photo_path
        ? $navUser->profile_photo_url
        : asset('images/default-user.svg');
@endphp
<div class="nav-user-menu {{ $wrapperClass ?? '' }}" data-nav-user-menu>
  <button
    type="button"
    class="nav-user-menu__toggle"
    data-nav-user-menu-toggle
    aria-expanded="false"
    aria-haspopup="true"
    aria-label="Menu compte — {{ $navUserName }}"
  >
    <span class="nav-user-menu__avatar-wrap">
      <img
        src="{{ $navUserPhoto }}"
        alt="{{ $navUserName }}"
        class="nav-user-avatar"
        width="40"
        height="40"
      >
      <span class="nav-user-menu__chevron" aria-hidden="true">
        <i class="fas fa-chevron-down"></i>
      </span>
    </span>
  </button>

  <div class="nav-user-menu__dropdown" data-nav-user-menu-dropdown role="menu">
    <a href="{{ route('dashboard') }}" class="nav-user-menu__item" role="menuitem">
      <i class="fas fa-gauge-high" aria-hidden="true"></i>
      <span>Dashboard</span>
    </a>
    <form method="POST" action="{{ route('logout') }}" class="nav-user-menu__logout-form">
      @csrf
      <button type="submit" class="nav-user-menu__item nav-user-menu__item--danger" role="menuitem">
        <i class="fas fa-right-from-bracket" aria-hidden="true"></i>
        <span>Se déconnecter</span>
      </button>
    </form>
  </div>
</div>
