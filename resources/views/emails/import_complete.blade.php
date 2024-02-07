<x-mail::message>
# Introduction

@if(count($errors) > 0)
    The following errors were found:
    @foreach($errors as $error)
        - {{ $error }}
    @endforeach
@endif

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
