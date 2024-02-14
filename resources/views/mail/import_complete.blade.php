Hello,<br/><br/>

Import DHCP entries has completed.<br/><br/>

@if(count($errors) > 0)
    The following errors were found:<br/>

    @foreach($errors as $error)
        {{ $error }}<br/>
    @endforeach
@endif

<br/>
Thanks,<br>
{{ config('app.name') }}
