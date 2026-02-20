<?php

namespace App\Http\Controllers;

use App\Actions\Comments\AddCommentToContentItem;
use App\Actions\Comments\DeleteComment;
use App\Actions\Comments\UpdateCommentVisibility;
use App\Http\Requests\Comment\StoreCommentRequest;
use App\Http\Requests\Comment\UpdateCommentRequest;
use App\Models\Comment;
use App\Models\ContentItem;
use Illuminate\Http\RedirectResponse;

class CommentsController extends Controller
{
    public function store(
        StoreCommentRequest $request,
        ContentItem $contentItem,
        AddCommentToContentItem $addCommentToContentItem,
    ): RedirectResponse {
        $this->authorize('comment', $contentItem);

        $addCommentToContentItem->handle(
            user: $request->user(),
            contentItem: $contentItem,
            bodyMarkdown: $request->bodyMarkdown(),
        );

        return redirect()->back();
    }

    public function update(
        UpdateCommentRequest $request,
        Comment $comment,
        UpdateCommentVisibility $updateCommentVisibility,
    ): RedirectResponse {
        $this->authorize('update', $comment);

        $updateCommentVisibility->handle(
            comment: $comment,
            isHidden: $request->isHidden(),
            hiddenById: $request->user()->id,
        );

        return redirect()->back();
    }

    public function destroy(Comment $comment, DeleteComment $deleteComment): RedirectResponse
    {
        $this->authorize('delete', $comment);

        $deleteComment->handle($comment);

        return redirect()->back();
    }
}
