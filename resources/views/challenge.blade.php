<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
</head>
<body>

<div>
    <div>
        {{ __('tfa-confirmation::translations.challenge.message') }}
    </div>

    <form method="POST" action="{{ route('auth.two-factor-authentication.confirm') }}">
        @csrf

        <div>
            <label for="code">
                {{ __('tfa-confirmation::translations.challenge.input_label') }}
            </label>
            <input type="text" name="code" id="code" inputmode="numeric" autofocus autocomplete="one-time-code">

            @error('code')
            <p>{{ $message }}</p>
            @enderror
        </div>

        <button type="submit">
            {{ __('tfa-confirmation::translations.challenge.button_label') }}
        </button>
    </form>
</div>

</body>
</html>


