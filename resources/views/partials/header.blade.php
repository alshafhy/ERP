<header>
    <div class="search-bar">
        <input type="text" placeholder="البحث...">
        <i class="fas fa-search"></i>
    </div>

    <div class="header-left">
        <div class="dropdown">
            <div class="notification-btn" id="notifDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="far fa-bell"></i>
                @if(auth()->user()->unreadNotifications->count() > 0)
                    <span class="notification-badge">{{ auth()->user()->unreadNotifications->count() }}</span>
                @endif
            </div>
            <div class="dropdown-menu dropdown-menu-end notification-dropdown" aria-labelledby="notifDropdown">
                <div class="notif-header">
                    <h6>التنبيهات</h6>
                    @if(auth()->user()->unreadNotifications->count() > 0)
                        <form action="{{ route('notifications.read-all') }}" method="POST">
                            @csrf
                            <button type="submit" style="background: none; border: none; color: #2563eb; font-size: 12px; cursor: pointer;">تمت القراءة للكل</button>
                        </form>
                    @endif
                </div>
                <div class="notif-list">
                    @forelse(auth()->user()->unreadNotifications->take(5) as $notification)
                        <a href="{{ $notification->data['url'] ?? '#' }}" class="notif-item unread">
                            <div class="notif-icon">
                                <i class="fas fa-info-circle"></i>
                            </div>
                            <div class="notif-content">
                                <div class="notif-title">{{ $notification->data['title'] }}</div>
                                <div class="notif-text">{{ $notification->data['message'] }}</div>
                                <div class="notif-time">{{ $notification->created_at->diffForHumans() }}</div>
                            </div>
                        </a>
                    @empty
                        <div class="p-4 text-center text-muted" style="font-size: 13px;">
                            لا توجد تنبيهات جديدة
                        </div>
                    @endforelse
                </div>
                <div class="notif-footer">
                    <a href="{{ route('notifications.index') }}">عرض كل التنبيهات</a>
                </div>
            </div>
        </div>
        <a href="{{ route('dashboard') }}" class="user-profile-btn">
            <img src="https://ui-avatars.com/api/?name=Ahmed+ansour&background=random" alt="User">
        </a>
    </div>
</header>
