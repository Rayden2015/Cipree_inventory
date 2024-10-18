<DOCTYPE html>
    <html lang="en-US">

    <head>
        <meta charset="utf-8">
    </head>

    <body>
        <h3>Hello, your account has been logged in, if it not you, kindly reset your password.</h3>
        <div class="mt-5">
            @if($currentUserInfo)
            <h4>IP: {{ $currentUserInfo->ip }}</h4>
            <h4>Country Name: {{ $currentUserInfo->countryName }}</h4>
            <h4>Country Code: {{ $currentUserInfo->countryCode }}</h4>
            <h4>Region Code: {{ $currentUserInfo->regionCode }}</h4>
            <h4>Region Name: {{ $currentUserInfo->regionName }}</h4>
            <h4>City Name: {{ $currentUserInfo->cityName }}</h4>
            <h4>Zip Code: {{ $currentUserInfo->zipCode }}</h4>
            <h4>Latitude: {{ $currentUserInfo->latitude }}</h4>
            <h4>Longitude: {{ $currentUserInfo->longitude }}</h4>
        @endif
                </div>
            <p><a href="{{ route('login') }}">Click here to login.</a></p>
            <p id="p1"><a href="{{ route('password.request') }}" class="ml-auto">Reset Password!</a> </p>

    </body>

    </html>
