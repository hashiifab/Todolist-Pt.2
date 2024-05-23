<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PostController extends Controller
{
    public function index(): View
    {
        $posts = Post::latest()->paginate(5);
        return view('posts.index', compact('posts'));
    }

    public function create(): View
    {
        return view('posts.create');
    }

    // Validasi Form

    public function store(Request $request)
    {
        $this->validate($request, [
            'image' => 'required|image|mimes:jpeg,jpg,png|max:2048',
            'title' => 'required|min:5',
            'content'   => 'required|min:10'
        ]);

        // uplaod image
        $image = $request->file('image');
        $image->storeAs('public/posts', $image->hashName());

        // create data

        Post::create([
            'image' => $image->hashName(),
            'title' => $request->title,
            'content'   => $request->content
        ]);

        return redirect()->route('posts.index')->with(['success' => 'Data Saved !']);
    }

    public function show(string $id): View 
    {
        $post = Post::findOrFail($id);

        return view('posts.show', compact('post'));
    }

    public function destroy($id): RedirectResponse
    {
        $post = Post::findOrFail($id);

        Storage::delete('public/posts/'. $post->image);

        return redirect()->route('posts.index')->with(['succes'=> 'Data Deleted']);

    }

}
