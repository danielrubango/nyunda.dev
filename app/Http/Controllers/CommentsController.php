<?php

namespace App\Http\Controllers;

use App\Actions\Comments\AddCommentToContentItem;
use App\Actions\Comments\DeleteComment;
use App\Actions\Comments\UpdateCommentVisibility;
use App\Http\Requests\Comment\StoreCommentRequest;
use App\Http\Requests\Comment\UpdateCommentRequest;
use App\Models\Comment;
use App\Models\ContentItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

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
            parentId: $request->parentId(),
        );

        return redirect()
            ->back()
            ->with('status', __('ui.flash.comment_added'));
    }

    public function update(
        UpdateCommentRequest $request,
        Comment $comment,
        UpdateCommentVisibility $updateCommentVisibility,
    ): RedirectResponse|JsonResponse {
        $this->authorize('update', $comment);

        $updateCommentVisibility->handle(
            comment: $comment,
            isHidden: $request->isHidden(),
            hiddenById: $request->user()->id,
        );

        $message = $request->isHidden()
            ? __('ui.flash.comment_hidden')
            : __('ui.flash.comment_shown');

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'ok',
                'message' => $message,
            ]);
        }

        return redirect()
            ->back()
            ->with('status', $message);
    }

    public function destroy(Request $request, Comment $comment, DeleteComment $deleteComment): RedirectResponse|JsonResponse
    {
        $this->authorize('delete', $comment);

        $deleteComment->handle($comment);

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'ok',
                'message' => __('ui.flash.comment_deleted'),
            ]);
        }

        return redirect()
            ->back()
            ->with('status', __('ui.flash.comment_deleted'));
    }
}
