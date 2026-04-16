<!DOCTYPE html>
<html lang="{{ $locale }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name') }} Newsletter</title>
        <style>
            body { font-family: Arial, sans-serif; color: #18181b; line-height: 1.6; margin: 0; padding: 0; background: #f4f4f5; }
            .wrapper { max-width: 600px; margin: 0 auto; background: #ffffff; }
            .header { padding: 32px 32px 16px; border-bottom: 1px solid #e4e4e7; }
            .header h1 { font-size: 20px; font-weight: 700; margin: 0; color: #18181b; }
            .intro { padding: 24px 32px; font-size: 15px; color: #52525b; }
            .articles { padding: 0 32px 24px; }
            .article { border: 1px solid #e4e4e7; border-radius: 6px; padding: 16px; margin-bottom: 16px; }
            .article-type { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: #71717a; margin: 0 0 6px; }
            .article-title { font-size: 16px; font-weight: 600; margin: 0 0 8px; }
            .article-title a { color: #18181b; text-decoration: none; }
            .article-title a:hover { text-decoration: underline; }
            .article-excerpt { font-size: 14px; color: #52525b; margin: 0 0 12px; }
            .article-cta a { display: inline-block; font-size: 13px; font-weight: 600; color: #18181b; text-decoration: none; border: 1px solid #a1a1aa; border-radius: 4px; padding: 6px 12px; }
            .footer { padding: 24px 32px; border-top: 1px solid #e4e4e7; font-size: 12px; color: #a1a1aa; text-align: center; }
            .footer a { color: #71717a; }
        </style>
    </head>
    <body>
        <div class="wrapper">
            <div class="header">
                <h1>{{ config('app.name') }}</h1>
            </div>

            @if ($intro)
                <div class="intro">
                    <p>{{ $intro }}</p>
                </div>
            @endif

            <div class="articles">
                @foreach ($rows as $row)
                    @php
                        /** @var \App\Models\ContentItem $item */
                        $item = $row['content_item'];
                        /** @var \App\Models\ContentTranslation $translation */
                        $translation = $row['translation'];

                        $isInternal = $item->isInternalPost();
                        $url = $isInternal
                            ? route('blog.show', ['locale' => $translation->locale, 'slug' => $translation->slug])
                            : ($translation->external_url ?? '#');

                        $typeLabel = $isInternal
                            ? ($locale === 'en' ? 'Article' : 'Article')
                            : ($locale === 'en' ? 'External link' : 'Lien externe');

                        $ctaLabel = $isInternal
                            ? ($locale === 'en' ? 'Read article →' : 'Lire l\'article →')
                            : ($locale === 'en' ? 'Visit link →' : 'Voir le lien →');
                    @endphp

                    <div class="article">
                        <p class="article-type">{{ $typeLabel }}</p>
                        <p class="article-title">
                            <a href="{{ $url }}" target="_blank">{{ $translation->title }}</a>
                        </p>
                        @if ($translation->excerpt)
                            <p class="article-excerpt">{{ $translation->excerpt }}</p>
                        @endif
                        <div class="article-cta">
                            <a href="{{ $url }}" target="_blank">{{ $ctaLabel }}</a>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="footer">
                <p>
                    {{ $locale === 'en' ? 'You are receiving this because you subscribed to the' : 'Vous recevez cet email car vous êtes abonné à la' }}
                    {{ config('app.name') }} newsletter.
                </p>
                <p>
                    <a href="{{ $unsubscribeUrl }}">
                        {{ $locale === 'en' ? 'Unsubscribe' : 'Se désabonner' }}
                    </a>
                </p>
            </div>
        </div>
    </body>
</html>
