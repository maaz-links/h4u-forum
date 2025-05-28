@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => config('app.url')])
            
        @endcomponent
    @endslot

    {{-- Body --}}
    # Hello!

    Thank you for registering with {{ config('app.name') }}. Please click the button below to verify your email address.

    @component('mail::button', ['url' => $url, 'color' => 'primary'])
        Verify Email Address
    @endcomponent

    If you did not create an account, no further action is required.

    {{-- Footer --}}
    @slot('footer')
        @component('mail::footer')
            © {{ date('Y') }} Hostess4You. All rights reserved.
        @endcomponent
    @endslot
@endcomponent