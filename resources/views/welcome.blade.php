<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Webhook Deployer</title>
</head>

<body>
    <h1>Webhook Deployer</h1>
    @use('\Spatie\WebhookClient\Models\WebhookCall')

    @foreach (WebhookCall::query()->latest()->take(10)->all() as $webhook)
        <div>#{{ $webhook->id }} : {{ $webhook->url }}</div>
    @endforeach
</body>

</html>
