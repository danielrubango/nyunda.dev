<?php

namespace App\Actions\Content;

use App\Models\ContentItem;
use App\Models\User;

class ToggleLinkVote
{
    /**
     * Toggle le vote d'un utilisateur sur un lien.
     * Retourne true si le vote a été ajouté, false s'il a été retiré.
     */
    public function handle(User $user, ContentItem $contentItem): bool
    {
        $existingVote = $contentItem->linkVotes()
            ->where('user_id', $user->id)
            ->first();

        if ($existingVote !== null) {
            $existingVote->delete();

            return false;
        }

        $contentItem->linkVotes()->create([
            'user_id' => $user->id,
        ]);

        return true;
    }
}
