<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title> @yield('title') | {{ getSuperAdminSettingKeyValue('app_name') }}</title>
    <meta name="description" content="Hospital management system">
    <meta name="keyword" content="hospital,doctor,patient,fever,MD,MS,MBBS">
    <link rel="icon" href="{{ asset(getSuperAdminSettingKeyValue('favicon')) }}" type="image/png">
    <link rel="canonical" href="{{ route('landing-home') }}" />
    <link rel="stylesheet" href="{{ asset('favicon.ico') }}" type="image/png">
    <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Poppins:300,400,500,600,700" />
    <link href="{{ mix('assets/css/style.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ mix('/assets/css/custom-auth.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ mix('assets/css/third-party.css') }}" rel="stylesheet" type="text/css" />
    @yield('css')
</head>
<?php
$style = 'style=';
?>

<body id=""
    class="header-fixed header-tablet-and-mobile-fixed toolbar-enabled toolbar-fixed toolbar-tablet-and-mobile-fixed aside-enabled aside-fixed">
    <div class="d-flex flex-column flex-root">
        @yield('content')
    </div>
    {{ Form::hidden('invalidNumber', __('messages.common.invalid_number'), ['class' => 'invalidNumber']) }}
    {{ Form::hidden('invalidCountryNumber', __('messages.common.invalid_country_number'), ['class' => 'invalidCountryNumber']) }}
    {{ Form::hidden('tooShort', __('messages.common.too_short'), ['class' => 'tooShort']) }}
    {{ Form::hidden('tooLong', __('messages.common.too_long'), ['class' => 'tooLong']) }}
    <!-- Scripts -->
    <script src="{{ mix('assets/js/third-party.js') }}"></script>
    @yield('scripts')
    <script>
        $(document).ready(function() {
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 3000);
        });
        $(document).on('click', '.language-select', function() {
            let languageName = $(this).data('id');
            $.ajax({
                type: 'get',
                url: 'set-language',
                data: {
                    languageName: languageName
                },
                success: function() {
                    location.reload();
                },
            });
        })

    </script>
</body>

</html>
