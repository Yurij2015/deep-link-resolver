<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Deep Link OpenGraph Stub</title>

    @if(isset($meta) && is_array($meta))
        <meta property="og:type" content="{{ $meta['type'] ?? 'website' }}"/>
        @if(!empty($meta['title']))
            <meta property="og:title" content="{{ $meta['title'] }}">
        @endif
        @if(!empty($meta['description']))
            <meta property="og:description" content="{{ $meta['description'] }}">
        @endif
        @if(!empty($meta['image']))
            <meta property="og:image" content="{{ $meta['image'] }}">
        @endif
        @if(!empty($meta['url']))
            <meta property="og:url" content="{{ $meta['url'] }}">
        @endif
    @endif
</head>
<body>
<p>Deep link OG stub</p>
</body>
</html>

