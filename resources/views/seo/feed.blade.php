<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<rss version="2.0">
    <channel>
        <title>{{ config('app.name') }}</title>
        <link>{{ route('blog.index') }}</link>
        <description>Flux RSS des contenus publies sur {{ config('app.name') }}.</description>
        <language>{{ str_replace('_', '-', app()->getLocale()) }}</language>
        <lastBuildDate>{{ now()->toRssString() }}</lastBuildDate>
@foreach ($feedItems as $item)
        <item>
            <title>{{ $item['title'] }}</title>
            <link>{{ $item['link'] }}</link>
            <guid>{{ $item['guid'] }}</guid>
            <pubDate>{{ $item['published_at']->toRssString() }}</pubDate>
            <description>{{ $item['description'] }}</description>
        </item>
@endforeach
    </channel>
</rss>
