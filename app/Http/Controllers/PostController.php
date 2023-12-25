<?php

namespace App\Http\Controllers;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Post;
use Auth;
class PostController extends Controller
{
    public function index()
    {
        if(Auth::user()->role == 1)
        {
            $posts = Post::all();
        }
        else
        {
            $posts = Post::where('author_id',Auth::user()->id)->get();
        }

        return view('rtl.admin.modules.post.index',compact('posts'));
    }
    public function list()
    {
        $posts = Post::all();
        return view('rtl.admin.modules.post.list',compact('posts'));
    }
    public function create()
    {
        return view('rtl.admin.modules.post.create');
    }
    public function save(Request $request)
    {
          // Validate the form data
          $request->validate([
            'title' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'content' => 'required|string',
        ]);

        // Handle the file upload
        $image = $request->file('image');
        $image_name = 'post_image_' . Str::slug($request->title, '_') . '_' . time() . '.' . $request->file('image')->getClientOriginalExtension();
        $destinationPath = public_path('/uploads');
            $image->move($destinationPath, $image_name);


        // Create a new Post instance and save it to the database
        $post = new Post([
            'title' => $request->input('title'),
            'author_id' => Auth::user()->id,
            'image' => $image_name,
            'content' => $request->input('content'),
        ]);

        $post->save();

        // Optionally, you can redirect to a success page or do something else
        return redirect()->route('post-index')->with('success', 'تم إنشاء المنشور بنجاح');

    }
    public function edit($id)
    {
        $post = Post::findOrFail($id);
        return view('rtl.admin.modules.post.edit',compact('post'));
    }
    public function update(Request $request)
    {
          // Validate the form data
          $request->validate([
            'title' => 'required|string',
            'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'content' => 'required|string',
        ]);
        $post = Post::findOrFail($request->post_id);
        // Handle the file upload
        if($request->hasFile('image'))
        {
            $image = $request->file('image');
            $image_name = 'post_image_' . Str::slug($request->title, '_') . '_' . time() . '.' . $request->file('image')->getClientOriginalExtension();
            $destinationPath = public_path('/uploads');
            $image->move($destinationPath, $image_name);
            $post->image = $image_name;
        }



        // Create a new Post instance and save it to the database
        $post->title = $request->input('title');
        $post->author_id = Auth::user()->id;
        $post->content = $request->input('content');

        $post->save();

        // Optionally, you can redirect to a success page or do something else
        return redirect()->route('post-index')->with('success', 'تم تحديث المنشور بنجاح');

    }
    public function details($id)
    {
        $post = Post::findOrFail($id);
        return view('rtl.admin.modules.post.details',compact('post'));
    }
    public function delete($id)
    {
        $post = Post::findOrFail($id);
        $post->delete();
        return redirect()->route('post-index')->with('success', 'تم حذف المنشور بنجاح');

    }
}
