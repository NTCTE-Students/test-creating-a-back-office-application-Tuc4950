<?php

namespace App\Http\Controllers;

use App\Models\Blog\Comment;
use App\Models\User;
use App\Models\Blog\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActionsController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'user.name' => 'required',
            'user.email' => 'required|email|unique:users,email',
            'user.password' => 'required|min:8|alpha_dash|confirmed',
        ], [
            'user.name.required' => 'Поле "Имя" обязательно для заполнения',
            'user.email.reqired' => 'Поле "Электронная почта" обязательно для заполнения',
            'user.email.email'=> 'Поле "Электронная почта" обязательно для заполнения',
            'user.password.required'=> 'Поле "Пароль" обязательно для заполнения',
            'user.password.min'=> 'Поле "Пароль" должно быть не менее, чем 8 символов',
            'user.password.alpha_dash'=> 'Поле "Пароль" должно содержать только строчные и прописные символы латиницы, цифры',
            'user.password.confirmed'=> 'Поле "Пароль" и "Повторите пароль" не совпадает',
        ]);

        $user = User::create($request -> input('user'));
        Auth::login($user);
        return redirect('/');
    }
    public function logout()
    {
        Auth::logout();
        return redirect('/');
    }
    public function login(Request $request)
    {
        $request->validate([
            'user.email'=> 'required|email',
            'user.password'=> 'required|min:8|alpha_dash',
        ], [
            'user.email.reqired' => 'Поле "Электронная почта" обязательно для заполнения',
            'user.email.email'=> 'Поле "Электронная почта" должно быть предоставлено в виде валидного адреса электронной почты',
            'user.password.required'=> 'Поле "Пароль" обязательно для заполнения',
            'user.password.min'=> 'Поле "Пароль" должно быть не менее, чем 8 символов',
            'user.password.alpha_dash'=> 'Поле "Пароль" должно содержать только строчные и прописные символы латиницы, цифры',
        ]);
        if(Auth::attempt($request -> input('user'))) {
            return redirect('/');
        } else {
            return back() -> withErrors([
                'user.email' => 'Предоставленная почта или пароль не подходят'
            ]);
        }
    }

    public function create_post(Request $request)
    {
        $request ->validate([
            'post.title'=> 'required',
            'post.content'=> 'required',
        ], [
            'post.title.required' => 'Поле "Заголовок" не может быть пустым',
            'post.content.required' => 'Поле "Текст" не может быть пустым',
        ]);

        Post::create( array_merge($request -> input('post'), ['user_id' => Auth::id()]));
        return redirect('/');
    }

    public function edit_post(Request $request, int $post_id)
    {
        $post = Post::findOrFail($post_id);
        if (Auth::id() == $post -> user_id) {
            $post->fill($request -> input('post'))->save();
            return redirect('post/'.$post_id.'/edit');
        }
        return redirect('/');
    }

    public function add_like(Request $request, Post $post)
    {
        $like = $post->likes()->where('user_id', auth()->id());
    
        if ($like->exists()) {
            $like->delete();
        } else {
            $post->likes()->create([
                'user_id' => auth()->id(),
            ]);
        }
    
        return back();
    }
    
    public function add_comment(Request $request, Post $post)
    {
        $post->comments()->create([
            'user_id' => auth()->id(),
            'content' => $request->input('content'),
        ]);

        return back();
    }
}
