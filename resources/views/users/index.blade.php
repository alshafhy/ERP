@extends('layouts.admin')

@section('title', 'إدارة الموظفين والصلاحيات')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center bg-white p-6 rounded-2xl shadow-sm border border-gray-100 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">هيكل الموظفين والصلاحيات</h1>
            <p class="text-sm text-gray-500 mt-1">عرض وتعيين الصلاحيات وتخصيص الفروع لموظفي معرض عماد الدين</p>
        </div>
        
        <a href="{{ route('users.create') }}" class="inline-flex items-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-sm transition gap-1.5">
            <i class="fas fa-plus"></i>
            <span>إضافة موظف جديد</span>
        </a>
    </div>

    <!-- Users Table card -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden text-right">
        @if($users->isEmpty())
            <div class="text-center py-16 text-gray-400 text-xs">
                لا يوجد موظفون مسجلون حالياً.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-right border-collapse text-xs">
                    <thead class="bg-gray-50 text-gray-550 font-bold">
                        <tr>
                            <th class="px-6 py-3">الموظف</th>
                            <th class="px-6 py-3">البريد الإلكتروني</th>
                            <th class="px-6 py-3">رقم الجوال</th>
                            <th class="px-6 py-3">المسمى الوظيفي</th>
                            <th class="px-6 py-3">الفرع المخصص</th>
                            <th class="px-6 py-3">مستوى الصلاحية</th>
                            <th class="px-6 py-3 text-left">التحكم</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition">
                                <td class="px-6 py-4 font-bold text-gray-800 flex items-center gap-3">
                                    <img class="w-8 h-8 rounded-full shadow" src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=random" alt="{{ $user->name }}">
                                    <span>{{ $user->name }}</span>
                                </td>
                                <td class="px-6 py-4 font-mono font-bold text-gray-500">
                                    {{ $user->email }}
                                </td>
                                <td class="px-6 py-4 font-mono text-gray-600">
                                    {{ $user->phone ?? '-' }}
                                </td>
                                <td class="px-6 py-4 font-bold text-gray-700">
                                    {{ $user->position }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($user->branch)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[10px] font-bold bg-indigo-50 text-indigo-700">
                                            <i class="fas fa-map-marker-alt ml-1"></i>
                                            {{ $user->branch->name }} ({{ $user->branch->city }})
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[10px] font-bold bg-gray-50 text-gray-500">
                                            <i class="fas fa-globe ml-1"></i>
                                            كل الفروع (سيادي)
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @foreach($user->roles as $role)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[9px] font-bold capitalize bg-emerald-50 text-emerald-700 border border-emerald-100">
                                            {{ strtoupper($role->name) }}
                                        </span>
                                    @endforeach
                                </td>
                                <td class="px-6 py-4 text-left">
                                    <div class="inline-flex gap-1.5">
                                        <a href="{{ route('users.edit', $user) }}" class="px-2.5 py-1 bg-gray-50 hover:bg-indigo-50 text-gray-650 hover:text-indigo-650 text-[10px] font-bold rounded-lg border border-gray-150 transition">
                                            تعديل الصلاحيات
                                        </a>

                                        @if(auth()->user()->hasRole('super_admin') && $user->id !== auth()->id())
                                            <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="return confirm('تأكيد حذف حساب الموظف وسحب كافة الصلاحيات؟')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-2.5 py-1 bg-red-50 hover:bg-red-600 text-red-650 hover:text-white text-[10px] font-bold rounded-lg border border-red-150 transition">
                                                    حذف الموظف
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($users->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $users->links() }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection
