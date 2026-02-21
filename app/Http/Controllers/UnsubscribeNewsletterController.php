<?php

namespace App\Http\Controllers;

use App\Actions\Newsletter\UnsubscribeNewsletterSubscription;
use Illuminate\Http\RedirectResponse;

class UnsubscribeNewsletterController extends Controller
{
    public function __invoke(
        string $token,
        UnsubscribeNewsletterSubscription $unsubscribeNewsletterSubscription,
    ): RedirectResponse {
        $unsubscribed = $unsubscribeNewsletterSubscription->handle($token);

        return redirect()
            ->route('blog.index')
            ->with(
                $unsubscribed ? 'status' : 'error',
                $unsubscribed
                    ? __('ui.newsletter.status.unsubscribed')
                    : __('ui.newsletter.status.unsubscribe_invalid'),
            );
    }
}
