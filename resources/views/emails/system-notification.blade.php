<x-mail::message>
# {{ $title }}

{{ $message }}

<x-mail::button :url="$url">
عرض التفاصيل
</x-mail::button>

شكراً,<br>
فريق عمل {{ config('app.name') }}
</x-mail::message>
