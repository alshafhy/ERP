<!DOCTYPE html>
<html lang="ar" dir="rtl" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تجربة ERP - @yield('title')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Cairo', sans-serif; }
    </style>
</head>
<body class="h-full">
    <div x-data="{ sidebarOpen: false }" class="flex h-screen bg-gray-50">
        <!-- Sidebar -->
        <div :class="sidebarOpen ? 'translate-x-0' : 'translate-x-full'" class="fixed inset-y-0 right-0 z-30 w-64 transition duration-300 transform bg-indigo-900 lg:translate-x-0 lg:static lg:inset-0">
            <div class="flex items-center justify-center mt-8">
                <div class="flex items-center">
                    <span class="text-2xl font-bold text-white">تجربة ERP</span>
                </div>
            </div>

            <nav class="mt-10">
                <a class="flex items-center px-6 py-2 mt-4 text-gray-100 hover:bg-indigo-800 {{ request()->is('/') || request()->is('dashboard') ? 'bg-indigo-800' : '' }}" href="{{ route('dashboard') }}">
                    <i class="fas fa-chart-line w-6"></i>
                    <span class="mx-3">لوحة التحكم</span>
                </a>

                <a class="flex items-center px-6 py-2 mt-4 text-gray-100 hover:bg-indigo-800 {{ request()->is('suppliers*') ? 'bg-indigo-800' : '' }}" href="{{ route('suppliers.index') }}">
                    <i class="fas fa-truck w-6"></i>
                    <span class="mx-3">الموردون</span>
                </a>

                <a class="flex items-center px-6 py-2 mt-4 text-gray-100 hover:bg-indigo-800 {{ request()->is('vehicles*') ? 'bg-indigo-800' : '' }}" href="{{ route('vehicles.index') }}">
                    <i class="fas fa-car w-6"></i>
                    <span class="mx-3">مخزون السيارات</span>
                </a>

                <a class="flex items-center px-6 py-2 mt-4 text-gray-100 hover:bg-indigo-800 {{ request()->is('customers*') ? 'bg-indigo-800' : '' }}" href="{{ route('customers.index') }}">
                    <i class="fas fa-users w-6"></i>
                    <span class="mx-3">العملاء</span>
                </a>

                <a class="flex items-center px-6 py-2 mt-4 text-gray-100 hover:bg-indigo-800 {{ request()->is('leads*') ? 'bg-indigo-800' : '' }}" href="{{ route('leads.index') }}">
                    <i class="fas fa-filter w-6"></i>
                    <span class="mx-3">الفرص البيعية (CRM)</span>
                </a>

                <a class="flex items-center px-6 py-2 mt-4 text-gray-100 hover:bg-indigo-800 {{ request()->is('deals*') ? 'bg-indigo-800' : '' }}" href="{{ route('deals.index') }}">
                    <i class="fas fa-handshake w-6"></i>
                    <span class="mx-3">عقود المبيعات والصفقات</span>
                </a>

                <a class="flex items-center px-6 py-2 mt-4 text-gray-100 hover:bg-indigo-800 {{ request()->is('finance*') ? 'bg-indigo-800' : '' }}" href="{{ route('finance.index') }}">
                    <i class="fas fa-wallet w-6"></i>
                    <span class="mx-3">المالية والحسابات</span>
                </a>

                @if(auth()->user()->hasAnyRole(['super_admin', 'branch_manager']))
                    <a class="flex items-center px-6 py-2 mt-4 text-gray-100 hover:bg-indigo-800 {{ request()->is('users*') ? 'bg-indigo-800' : '' }}" href="{{ route('users.index') }}">
                        <i class="fas fa-users-cog w-6"></i>
                        <span class="mx-3">الموظفون والصلاحيات</span>
                    </a>
                @endif

                <a class="flex items-center px-6 py-2 mt-4 text-gray-100 hover:bg-indigo-800 {{ request()->is('reports*') ? 'bg-indigo-800' : '' }}" href="{{ route('reports.index') }}">
                    <i class="fas fa-chart-bar w-6"></i>
                    <span class="mx-3">التقارير والتحليلات</span>
                </a>

                <a class="flex items-center px-6 py-2 mt-4 text-gray-100 hover:bg-indigo-800 {{ request()->is('purchase-orders*') ? 'bg-indigo-800' : '' }}" href="{{ route('purchase-orders.index') }}">
                    <i class="fas fa-file-invoice-dollar w-6"></i>
                    <span class="mx-3">أوامر الشراء</span>
                </a>

                <a class="flex items-center px-6 py-2 mt-4 text-gray-100 hover:bg-indigo-800 {{ request()->is('inventory-movements*') ? 'bg-indigo-800' : '' }}" href="{{ route('inventory-movements.index') }}">
                    <i class="fas fa-exchange-alt w-6"></i>
                    <span class="mx-3">حركات المخزون</span>
                </a>

                <a class="flex items-center px-6 py-2 mt-4 text-gray-100 hover:bg-indigo-800 {{ request()->is('journal-entries*') ? 'bg-indigo-800' : '' }}" href="{{ route('journal-entries.index') }}">
                    <i class="fas fa-book w-6"></i>
                    <span class="mx-3">القيود المحاسبية</span>
                </a>
            </nav>
        </div>

        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="flex items-center justify-between px-6 py-4 bg-white border-b-4 border-indigo-600">
                <div class="flex items-center">
                    <button @click="sidebarOpen = true" class="text-gray-500 focus:outline-none lg:hidden">
                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M4 6H20M4 12H20M4 18H11" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                    </button>

                    <div class="relative mx-4 lg:mx-0">
                        <span class="absolute inset-y-0 right-0 flex items-center pr-3">
                            <svg class="w-5 h-5 text-gray-500" viewBox="0 0 24 24" fill="none">
                                <path d="M21 21L15 15M17 10C17 13.866 13.866 17 10 17C6.13401 17 3 13.866 3 10C3 6.13401 6.13401 3 10 3C13.866 3 17 6.13401 17 10Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                        </span>

                        <form action="{{ url()->current() }}" method="GET">
                            <input class="w-32 pr-10 pl-4 rounded-md form-input sm:w-64 focus:border-indigo-600 text-right" type="text" name="search" value="{{ request('search') }}" placeholder="بحث...">
                        </form>
                    </div>
                </div>

                <div class="flex items-center">
                    <div x-data="{ dropdownOpen: false }" class="relative">
                        <button @click="dropdownOpen = ! dropdownOpen" class="relative block w-8 h-8 overflow-hidden rounded-full shadow focus:outline-none">
                            <img class="object-cover w-full h-full" src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=random" alt="Your avatar">
                        </button>

                        <div x-show="dropdownOpen" @click.away="dropdownOpen = false" class="fixed inset-0 z-10 w-full h-full" style="display: none;"></div>

                        <div x-show="dropdownOpen" class="absolute left-0 z-10 w-48 mt-2 overflow-hidden bg-white rounded-md shadow-xl text-right" style="display: none;">
                            <a href="{{ route('profile.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-indigo-600 hover:text-white">الملف الشخصي</a>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="block w-full text-right px-4 py-2 text-sm text-gray-700 hover:bg-indigo-600 hover:text-white">تسجيل الخروج</button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50">
                <div class="container mx-auto px-6 py-8">
                    @if(session('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </main>
        </div>
    </div>
</body>
</html>
