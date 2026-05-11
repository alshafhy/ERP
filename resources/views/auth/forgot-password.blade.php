<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نسيت كلمة المرور</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="{{ asset('css/login.css') }}" rel="stylesheet">
</head>
<body>
    <div class="login-container">
        <div class="login-form-container">
            <div class="login-form">
                <div class="login-header">
                    <div class="logo">
                        <img src="{{ asset('images/company-logo.png') }}" alt="Logo" class="w-25">
                    </div>
                    <h1>نسيت كلمة المرور</h1>
                    <p>أدخل بريدك الإلكتروني لإرسال رابط إعادة تعيين كلمة المرور</p>
                </div>

                @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf

                    <div class="form-group">
                        <label for="email">البريد الإلكتروني</label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            placeholder="mail@abc.com"
                            value="{{ old('email') }}"
                            required
                        >
                        @error('email')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn-login">إرسال</button>
                    
                    <div class="form-footer mt-3 justify-content-center">
                        <a href="{{ route('login') }}" class="forgot-password">العودة لتسجيل الدخول</a>
                    </div>
                </form>
            </div>
        </div>
        <div class="login-image"></div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
