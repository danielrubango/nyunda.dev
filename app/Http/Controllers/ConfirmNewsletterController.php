<?php

namespace App\Http\Controllers;

use App\Actions\Newsletter\ConfirmNewsletterSubscription;
use Illuminate\Http\RedirectResponse;

class ConfirmNewsletterController extends Controller
{
    public function __invoke(
        string $token,
        ConfirmNewsletterSubscription $confirmNewsletterSubscription,
    ): RedirectResponse {
        $confirmed = $confirmNewsletterSubscription->handle($token);

        return redirect()
            ->route('blog.index')
            ->with(
                $confirmed ? 'status' : 'error',
                $confirmed
                    ? __('ui.newsletter.status.confirmed')
                    : __('ui.newsletter.status.confirmation_invalid'),
            );
    }
}
