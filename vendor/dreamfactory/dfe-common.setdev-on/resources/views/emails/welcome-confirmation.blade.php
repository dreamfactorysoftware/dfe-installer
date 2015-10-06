@extend('email.layout');

@section('headTitle')
    DreamFactory Enterprise&trade; : Welcome
@stop

@section('contentHeader')
    Welcome to DreamFactory!
@stop

@section('contentBody')
    <p>
        {{ $firstName }},</p>

    <p>Your account on the DFE Admin Console has been successfully created.</p>

    <p>In order to complete the registration process, please click the link below confirming your registered email address.</p>

    <p>
        <a href="{{ $confirmationUrl }}"
            title="Click to confirm your email address">{{ $confirmationUrl }}</a>
    </p>

    <p>
        If you've got any questions, feel free to drop us a line at <a href="mailto:{{ $supportEmail }}">{{ $supportEmail }}</a>
    </p>

    <p>
        Have a great day!<br /> The Dream Team
    </p>
@stop
