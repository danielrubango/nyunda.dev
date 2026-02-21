<?php

namespace App\Console\Commands;

use App\Models\ContentRead;
use Illuminate\Console\Command;

class PruneContentReads extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'content-reads:prune {--days=90 : Number of days to keep detailed reads history}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prune old content read logs while keeping aggregated reads counters.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $days = (int) $this->option('days');
        $retentionDays = $days > 0 ? $days : 90;
        $threshold = now()->subDays($retentionDays);
        $deletedCount = 0;

        do {
            $ids = ContentRead::query()
                ->where('counted_at', '<', $threshold)
                ->orderBy('id')
                ->limit(1000)
                ->pluck('id');

            if ($ids->isEmpty()) {
                break;
            }

            $deletedCount += ContentRead::query()
                ->whereIn('id', $ids)
                ->delete();
        } while (true);

        $this->info(sprintf(
            'Pruned %d content read logs older than %d days.',
            $deletedCount,
            $retentionDays,
        ));

        return self::SUCCESS;
    }
}
