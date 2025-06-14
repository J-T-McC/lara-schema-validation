<?php

namespace Workbench\App\Http\Controllers\Api;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Workbench\App\Http\Controllers\BaseController;
use Workbench\App\Http\Resources\PostResource;
use Workbench\App\Models\Post;

class PostController extends BaseController
{
    public function index(): AnonymousResourceCollection
    {
        return PostResource::collection(
            Post::query()->with('comments')->paginate(10)
        );
    }

    public function show(Post $post): PostResource
    {
        return PostResource::make($post);
    }

    public function empty(): PostResource
    {
        $post = new Post;
        $post->mergeCasts([
            'published_at' => 'string',
            'created_at' => 'string',
            'updated_at' => 'string',
            'tags' => 'string',
            'meta' => 'string',
        ]);

        $post->id = '1';
        $post->uuid = 'abc123';
        $post->title = 'Empty Post';
        $post->slug = null;
        $post->status = 'invalid';
        $post->tags = '';
        $post->meta = '{}';
        $post->published_at = '01/01/1970 12:00:00 AM';
        $post->created_at = '01/01/1970 12:00:00 AM';
        $post->updated_at = '01/01/1970 12:00:00 AM';

        return PostResource::make($post);
    }
}
