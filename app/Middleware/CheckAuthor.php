<?php

namespace App\Http\Middleware;

use App\Models\Blog\Post;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckAuthor
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $post_id = intval($request->route('post_id'));
        if(Post::findOrFail($post_id)->author->id == Auth::id())
            return $next($request);
        else
            return redirect('/');
    }
}
