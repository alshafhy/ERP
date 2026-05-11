<aside class="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo">
            <img src="{{ asset('images/company-logo.png') }}" alt="Logo">
        </div>
        <i class="fas fa-bars" style="color: #999; cursor: pointer;"></i>
    </div>

    <nav>
        <div class="menu-label">الرئيسية</div>
        <ul class="sidebar-menu">
            <li class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <a href="{{ route('dashboard') }}"><i class="fas fa-th-large"></i> لوحة التحكم</a>
            </li>
        </ul>
    </nav>

    <div class="sidebar-footer">
        <div class="menu-label" style="padding-left: 0; padding-right: 0;">أخرى</div>
        <ul class="sidebar-menu" style="margin-bottom: 20px;">
            <li class="{{ request()->routeIs('profile.*') ? 'active' : '' }}">
                <a href="{{ route('profile.index') }}"><i class="fas fa-user-circle"></i> الملف الشخصي</a>
            </li>
        </ul>
        <div class="night-mode">
            <div style="display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-moon"></i> الوضع الليلي
            </div>
            <label class="switch">
                <input type="checkbox" id="darkModeToggle">
                <span class="slider"></span>
            </label>
        </div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const darkModeToggle = document.getElementById('darkModeToggle');
        const root = document.documentElement;

        // Check for saved dark mode preference
        if (localStorage.getItem('darkMode') === 'enabled') {
            root.classList.add('dark-mode');
            darkModeToggle.checked = true;
        }

        darkModeToggle.addEventListener('change', () => {
            if (darkModeToggle.checked) {
                root.classList.add('dark-mode');
                localStorage.setItem('darkMode', 'enabled');
            } else {
                root.classList.remove('dark-mode');
                localStorage.setItem('darkMode', 'disabled');
            }
        });
    });
</script>
@endpush
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" style="background: none; border: none; padding: 0; width: 100%; text-align: right;">
                <div class="logout-link" style="color: var(--primary-red); cursor: pointer;">
                    <i class="fas fa-sign-out-alt"></i> تسجيل الخروج
                </div>
            </button>
        </form>
    </div>
</aside>
