<header>
    <div class="search-bar">
        <input type="text" placeholder="البحث...">
        <i class="fas fa-search"></i>
    </div>

    <div class="header-left">
        <a href="{{ route('dashboard') }}" class="user-profile-btn">
            <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=random" alt="User">
        </a>
    </div>
</header>
