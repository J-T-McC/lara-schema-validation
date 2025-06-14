<?php

namespace Workbench\App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Workbench\App\Http\Controllers\BaseController;
use Workbench\App\Http\Resources\CommentResource;
use Workbench\App\Models\Comment;

class CommentController extends BaseController
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $type = match ($request->input('type', 'all')) {
            'all' => 'get',
            'paginate' => 'paginate',
            'simplePaginate' => 'simplePaginate',
        };

        return CommentResource::collection(
            Comment::query()->{$type}()
        );
    }

    public function all(): AnonymousResourceCollection
    {
        return CommentResource::collection(
            Comment::query()->paginate(10)
        );
    }

    public function show(Comment $comment): CommentResource
    {
        return CommentResource::make($comment);
    }
}
