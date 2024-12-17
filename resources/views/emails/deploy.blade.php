<x-mail::message>
# Introduction

Deploy n°{{ $webhookCall->id }} {{ $failed ? 'Fail' : 'Success' }}

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
