<?php

namespace App\Http\Controllers\Dashboard;

use App\Actions\Content\CreateDashboardContentSubmission;
use App\Actions\Content\UpdateDashboardContentSubmission;
use App\Enums\ContentStatus;
use App\Enums\ContentType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\StoreDashboardContentRequest;
use App\Http\Requests\Dashboard\UpdateDashboardContentRequest;
use App\Models\Comment;
use App\Models\ContentItem;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;

class UserContentController extends Controller
{
    public function index(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();

        $statusFilter = $request->query('status');
        $typeFilter = $request->query('type');
        $searchFilter = trim((string) $request->query('q', ''));

        $query = ContentItem::query()
            ->where('author_id', $user->id)
            ->with(['translations', 'author'])
            ->withCount(['comments', 'likes', 'linkVotes'])
            ->latest('created_at');

        if (is_string($statusFilter) && in_array($statusFilter, collect(ContentStatus::cases())->map(fn (ContentStatus $status): string => $status->value)->all(), true)) {
            $query->where('status', $statusFilter);
        }

        if (is_string($typeFilter) && in_array($typeFilter, collect(ContentType::cases())->map(fn (ContentType $type): string => $type->value)->all(), true)) {
            $query->where('type', $typeFilter);
        }

        if ($searchFilter !== '') {
            $query->whereHas('translations', function (Builder $builder) use ($searchFilter): void {
                $builder->where('title', 'like', '%'.$searchFilter.'%')
                    ->orWhere('excerpt', 'like', '%'.$searchFilter.'%');
            });
        }

        /** @var LengthAwarePaginator<int, ContentItem> $contentItems */
        $contentItems = $query->paginate(12)->withQueryString();

        $rows = $contentItems->getCollection()
            ->map(function (ContentItem $item): array {
                $translation = $item->translations->first();
                $interactionCount = $item->isInternalPost()
                    ? (int) $item->likes_count
                    : (int) $item->link_votes_count;

                return [
                    'item' => $item,
                    'title' => $translation?->title ?? 'Sans titre',
                    'status_label' => $this->resolveStatusLabel($item),
                    'status_variant' => $this->resolveStatusVariant($item),
                    'comments_count' => (int) $item->comments_count,
                    'interaction_count' => $interactionCount,
                    'reads_count' => (int) $item->reads_count,
                    'edit_url' => route('dashboard.content.edit', ['contentItem' => $item]),
                ];
            });

        $contentItems->setCollection($rows);

        $stats = [
            'total' => ContentItem::query()->where('author_id', $user->id)->count(),
            'published' => ContentItem::query()->where('author_id', $user->id)->where('status', ContentStatus::Published->value)->count(),
            'pending' => ContentItem::query()->where('author_id', $user->id)->where('status', ContentStatus::Pending->value)->count(),
            'rejected' => ContentItem::query()->where('author_id', $user->id)->where('status', ContentStatus::Rejected->value)->count(),
            'reads' => (int) ContentItem::query()->where('author_id', $user->id)->sum('reads_count'),
            'comments' => (int) Comment::query()
                ->whereHas('contentItem', fn (Builder $builder): Builder => $builder->where('author_id', $user->id))
                ->count(),
            'interactions' => (int) ContentItem::query()->where('author_id', $user->id)->withCount(['likes', 'linkVotes'])->get()
                ->sum(fn (ContentItem $item): int => (int) $item->likes_count + (int) $item->link_votes_count),
        ];

        $recentComments = Comment::query()
            ->where('user_id', $user->id)
            ->with('contentItem.translations')
            ->latest('created_at')
            ->limit(10)
            ->get();

        return view('dashboard.content.index', [
            'contentItems' => $contentItems,
            'stats' => $stats,
            'recentComments' => $recentComments,
            'statusFilter' => $statusFilter,
            'typeFilter' => $typeFilter,
            'searchFilter' => $searchFilter,
            'statusOptions' => collect(ContentStatus::cases())->mapWithKeys(fn (ContentStatus $status): array => [
                $status->value => ucfirst($status->value),
            ])->all(),
            'typeOptions' => collect(ContentType::cases())->mapWithKeys(fn (ContentType $type): array => [
                $type->value => ucfirst(str_replace('_', ' ', $type->value)),
            ])->all(),
        ]);
    }

    public function create(): View
    {
        /** @var User $user */
        $user = auth()->user();

        return view('dashboard.content.create', [
            'supportedLocales' => config('app.supported_locales', ['fr', 'en']),
            'defaultLocale' => $user->preferred_locale,
        ]);
    }

    public function edit(Request $request, ContentItem $contentItem): View
    {
        $contentItem = $this->resolveOwnedContentItem($request, $contentItem);
        $supportedLocales = config('app.supported_locales', ['fr', 'en']);

        /** @var User $user */
        $user = $request->user();

        $editableTranslation = $contentItem->translations
            ->firstWhere('locale', $user->preferred_locale)
            ?? $contentItem->translations->first();

        return view('dashboard.content.edit', [
            'contentItem' => $contentItem,
            'translation' => $editableTranslation,
            'supportedLocales' => $supportedLocales,
            'defaultLocale' => $editableTranslation?->locale ?? $user->preferred_locale,
        ]);
    }

    public function store(
        StoreDashboardContentRequest $request,
        CreateDashboardContentSubmission $createDashboardContentSubmission,
    ): RedirectResponse {
        /** @var User $user */
        $user = $request->user();

        $createDashboardContentSubmission->handle(
            author: $user,
            submission: $request->submissionData(),
        );

        return redirect()
            ->route('dashboard')
            ->with('status', 'Contenu soumis avec succes.');
    }

    public function update(
        UpdateDashboardContentRequest $request,
        ContentItem $contentItem,
        UpdateDashboardContentSubmission $updateDashboardContentSubmission,
    ): RedirectResponse {
        $contentItem = $this->resolveOwnedContentItem($request, $contentItem);

        $updateDashboardContentSubmission->handle(
            contentItem: $contentItem,
            submission: $request->submissionData(),
        );

        return redirect()
            ->route('dashboard.content.index')
            ->with('status', 'Contenu mis a jour avec succes.');
    }

    protected function resolveStatusLabel(ContentItem $contentItem): string
    {
        if ($contentItem->status === ContentStatus::Published) {
            return 'Accepte et publie';
        }

        if ($contentItem->status === ContentStatus::Pending && $contentItem->published_at !== null && $contentItem->published_at->isFuture()) {
            return 'Accepte et planifie';
        }

        if ($contentItem->status === ContentStatus::Pending) {
            return 'En attente d acceptation';
        }

        if ($contentItem->status === ContentStatus::Rejected) {
            return 'Rejete';
        }

        return 'Brouillon';
    }

    protected function resolveStatusVariant(ContentItem $contentItem): string
    {
        if ($contentItem->status === ContentStatus::Published) {
            return 'success';
        }

        if ($contentItem->status === ContentStatus::Pending && $contentItem->published_at !== null && $contentItem->published_at->isFuture()) {
            return 'info';
        }

        if ($contentItem->status === ContentStatus::Pending) {
            return 'warning';
        }

        if ($contentItem->status === ContentStatus::Rejected) {
            return 'danger';
        }

        return 'neutral';
    }

    protected function resolveOwnedContentItem(Request $request, ContentItem $contentItem): ContentItem
    {
        /** @var User $user */
        $user = $request->user();

        abort_unless($contentItem->author_id === $user->id, 404);

        return $contentItem->load('translations');
    }
}
