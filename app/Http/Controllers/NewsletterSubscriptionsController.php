<?php

namespace App\Http\Controllers;

use App\Actions\Newsletter\SendDoubleOptInEmail;
use App\Actions\Newsletter\SubscribeToNewsletter;
use App\Http\Requests\Newsletter\StoreNewsletterSubscriptionRequest;
use Illuminate\Http\RedirectResponse;

class NewsletterSubscriptionsController extends Controller
{
    public function store(
        StoreNewsletterSubscriptionRequest $request,
        SubscribeToNewsletter $subscribeToNewsletter,
        SendDoubleOptInEmail $sendDoubleOptInEmail,
    ): RedirectResponse {
        $result = $subscribeToNewsletter->handle(
            email: $request->email(),
            locale: $request->locale(),
        );

        if ($result['should_send_confirmation']) {
            $sendDoubleOptInEmail->handle($result['subscriber']);
        }

        return redirect()
            ->back()
            ->with('status', __('ui.newsletter.status.confirmation_sent'));
    }
}
