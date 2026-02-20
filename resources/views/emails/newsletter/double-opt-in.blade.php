<!DOCTYPE html>
<html lang="{{ $locale === 'en' ? 'en' : 'fr' }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name') }} Newsletter</title>
    </head>
    <body style="font-family: Arial, sans-serif; color: #18181b; line-height: 1.5;">
        @if ($locale === 'en')
            <h1>Confirm your newsletter subscription</h1>
            <p>Click the button below to confirm your monthly newsletter subscription.</p>
            <p>
                <a href="{{ $confirmUrl }}" style="display:inline-block;padding:10px 16px;border:1px solid #a1a1aa;color:#18181b;text-decoration:none;border-radius:6px;">
                    Confirm my subscription
                </a>
            </p>
            <p>If you did not request this subscription, you can unsubscribe:</p>
            <p><a href="{{ $unsubscribeUrl }}">{{ $unsubscribeUrl }}</a></p>
        @else
            <h1>Confirmez votre inscription newsletter</h1>
            <p>Cliquez sur le bouton ci-dessous pour confirmer votre inscription mensuelle.</p>
            <p>
                <a href="{{ $confirmUrl }}" style="display:inline-block;padding:10px 16px;border:1px solid #a1a1aa;color:#18181b;text-decoration:none;border-radius:6px;">
                    Confirmer mon inscription
                </a>
            </p>
            <p>Si vous n etes pas a l origine de cette demande, vous pouvez vous desinscrire:</p>
            <p><a href="{{ $unsubscribeUrl }}">{{ $unsubscribeUrl }}</a></p>
        @endif
    </body>
</html>
