{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<rss version="2.0">
    <channel>
        <title>{{ config('app.name') }}</title>
        <link>{{ config('site.production_url') }}</link>
        <description>New York City news, events, guides, and cinematic stories by Marina Kapler.</description>
        <language>en-US</language>
        @foreach ($posts as $post)
            <item>
                <title>{{ $post->title }}</title>
                <link>{{ config('site.production_url').route('posts.show', $post->slug, false) }}</link>
                <guid isPermaLink="true">{{ config('site.production_url').route('posts.show', $post->slug, false) }}</guid>
                <pubDate>{{ $post->published_at->toRfc2822String() }}</pubDate>
                <description>{{ $post->excerpt }}</description>
            </item>
        @endforeach
    </channel>
</rss>
