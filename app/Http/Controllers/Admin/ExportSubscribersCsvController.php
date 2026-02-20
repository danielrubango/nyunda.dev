<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Subscriber;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportSubscribersCsvController extends Controller
{
    public function __invoke(Request $request): StreamedResponse
    {
        $user = $request->user();
        abort_if($user === null || ! $user->hasRole(UserRole::Admin), 403);

        return response()->streamDownload(function (): void {
            $stream = fopen('php://output', 'w');
            if ($stream === false) {
                return;
            }

            fputcsv($stream, ['email', 'status', 'locale', 'confirmed_at', 'created_at']);

            Subscriber::query()
                ->orderByDesc('created_at')
                ->orderByDesc('id')
                ->chunk(500, function ($subscribers) use ($stream): void {
                    foreach ($subscribers as $subscriber) {
                        fputcsv($stream, [
                            $subscriber->email,
                            $subscriber->status->value,
                            $subscriber->locale,
                            $subscriber->confirmed_at?->toIso8601String(),
                            $subscriber->created_at?->toIso8601String(),
                        ]);
                    }
                });

            fclose($stream);
        }, 'subscribers-'.now()->format('Ymd-His').'.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
