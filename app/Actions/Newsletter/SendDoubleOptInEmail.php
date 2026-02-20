<?php

namespace App\Actions\Newsletter;

use App\Jobs\SendDoubleOptInEmailJob;
use App\Models\Subscriber;

class SendDoubleOptInEmail
{
    public function handle(Subscriber $subscriber): void
    {
        SendDoubleOptInEmailJob::dispatch($subscriber->id)->afterCommit();
    }
}
