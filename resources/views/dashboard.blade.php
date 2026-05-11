@extends('layouts.app')

@section('title', 'الرئيسية')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-4 p-4">
                <div class="text-center py-5">
                    <h2 class="fw-bold mb-3">مرحباً بك في النظام الجديد</h2>
                    <p class="text-muted fs-5">تم تصفير النظام بنجاح مع الاحتفاظ بالقالب الأساسي.</p>
                    <div class="mt-4">
                        <img src="{{ asset('images/welcome.svg') }}" alt="Welcome" style="max-width: 300px; opacity: 0.8;">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
