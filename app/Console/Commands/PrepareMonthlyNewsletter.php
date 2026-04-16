<?php

namespace App\Console\Commands;

use App\Actions\Newsletter\SelectNewsletterArticles;
use App\Models\NewsletterEdition;
use Carbon\Carbon;
use Illuminate\Console\Command;

class PrepareMonthlyNewsletter extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'newsletter:prepare-monthly
                            {--limit=6 : Number of articles to include}
                            {--day-range=30 : Look back N days for published articles (0 = all time)}
                            {--force : Create even if a draft already exists for this month}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prepare the monthly newsletter edition (creates a draft with the best recent articles).';

    public function handle(SelectNewsletterArticles $selectArticles): int
    {
        $now = Carbon::now();
        $monthLabel = $now->translatedFormat('F Y'); // ex: "February 2026"
        $monthLabelFr = $now->locale('fr')->translatedFormat('F Y'); // ex: "février 2026"

        // Vérifier si un brouillon existe déjà pour ce mois-ci
        if (! $this->option('force')) {
            $alreadyExists = NewsletterEdition::query()
                ->where('status', 'draft')
                ->whereYear('created_at', $now->year)
                ->whereMonth('created_at', $now->month)
                ->exists();

            if ($alreadyExists) {
                $this->warn("Un brouillon de newsletter existe déjà pour {$monthLabelFr}. Utilisez --force pour en créer un autre.");

                return self::FAILURE;
            }
        }

        $limit = max((int) $this->option('limit'), 1);
        $dayRange = max((int) $this->option('day-range'), 0);

        $this->info("Sélection des {$limit} derniers articles (fenêtre {$dayRange} jours)...");

        $rows = $selectArticles->handle(limit: $limit, dayRange: $dayRange);

        if ($rows->isEmpty()) {
            $this->warn('Aucun article publié trouvé avec traduction. Brouillon non créé.');

            return self::FAILURE;
        }

        $contentItemIds = $rows->pluck('content_item')->pluck('id')->all();
        $articleCount = count($contentItemIds);

        $edition = NewsletterEdition::query()->create([
            'subject_fr' => "Newsletter NYUNDA.DEV – {$monthLabelFr}",
            'subject_en' => "NYUNDA.DEV Newsletter – {$monthLabel}",
            'intro_fr' => null,
            'intro_en' => null,
            'content_item_ids' => $contentItemIds,
            'status' => 'draft',
            'recipients_count' => 0,
            'sent_count' => 0,
        ]);

        $this->info("✓ Brouillon #{$edition->id} créé avec {$articleCount} article(s) pour {$monthLabelFr}.");
        $this->line("  Sujet FR : {$edition->subject_fr}");
        $this->line("  Sujet EN : {$edition->subject_en}");
        $this->newLine();
        $this->comment("Rendez-vous dans l'admin Filament → Newsletter → Éditions pour relire et envoyer.");

        return self::SUCCESS;
    }
}
