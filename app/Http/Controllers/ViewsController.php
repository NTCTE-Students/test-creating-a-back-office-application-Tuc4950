<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Blog\Post;
use Illuminate\Support\Facades\Auth;

class ViewsController extends Controller
{
    public function index()
    {
        return view("index", ['posts' => Post::where('draft', false)->get()]);
    }
    public function register()
    {
        return view("pages.register");
    }
    public function login()
    {
        return view("pages.login");
    }
    public function create_post()
    {
        return view("pages.posts.create");
    }
    public function drafts_post()
    {
        return view("pages.posts.drafts", ['posts' => Auth::user()->posts()->where('draft', 'on')->get()]);
    }
    public function get_post(int $post_id)
    {
        $post = Post::findOrFail($post_id);
        return view('pages.posts.post', ['post'=> $post]);
    }
    public function edit_post(int $post_id)
    {
        $post = Post::findOrFail($post_id);
        return view("pages.posts.edit", [
            'post' => $post,
        ]);
    }
}
