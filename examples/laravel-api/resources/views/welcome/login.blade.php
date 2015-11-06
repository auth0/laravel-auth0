
@extends('layouts.master')

@section('content')

    <div id="a0-container"></div>

    <script src="https://cdn.auth0.com/js/lock-7.min.js"></script>
    <script type="text/javascript">

        var lock = new Auth0Lock('{{ $auth0Config['client_id'] }}', '{{ $auth0Config['domain'] }}');

        lock.show({
            callbackURL: '{{ $auth0Config['redirect_uri'] }}'
            , responseType: 'code'
            , authParams: {
                scope: 'openid name email picture'
            }
            , container: 'a0-container'
        });

    </script>

@stop
