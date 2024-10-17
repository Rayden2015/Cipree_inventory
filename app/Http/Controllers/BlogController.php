<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $blog = Blog::latest()->paginate(6);
        return view('blog', compact('blog'));
    }

    public function allblogs(){
        $allblogs = Blog::all();
        return view('blogs.index',compact('allblogs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('blog.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'photo' => 'sometimes|nullable|image|mimes:jpeg,gif,png,jpg|max:9048',
        ]);
        $auth = Auth::id();

        $blog = new Blog();
        $blog->headline = $request->headline;
        $blog->body = $request->body;

        $blog->user_id = $auth;
        $imageName = time() . '.' . $request->image->extension();

        $request->image->move(public_path('images/blog'), $imageName);
        $blog->image = $imageName;
        $blog->save();

        // if ($request->image) {
        //     $imageName = UploadHelper::upload($request->image, 'media-' . $media->id, 'images/media');
        //     $media->image = $imageName;
        //     $media->save();
        // }


        return redirect()->route('blog.allnews');
    }

    public function update(Request $request, $id)
    {
        $blog = Blog::find($id);
        $request->validate([
            'headline' => 'string',
            'body' => 'string',
        ]);
        $auth = Auth::id();

        $blog->headline = $request->headline;
        $blog->body = $request->body;
        // $imageName = time() . '.' . $request->image->extension();

        // $request->image->move(public_path('images'), $imageName);
        // $blog->image = $imageName;

         if ($image = $request->file('image')) {
            $destinationPath = 'images/blog/';
            $profileImage = time() . "." . $image->getClientOriginalExtension();
            $image->move($destinationPath, $profileImage);
            $blog['image'] = "$profileImage";
        }else{
            unset($blog['images']);
        }
        $blog->user_id = $auth;
        $blog->save();
        return redirect()->route('blog.allnews');
    }
    public function show($id)
    {
        $blog = Blog::find($id);
        return view('blog.show', compact('blog'));
    }

    public function delete($id)
    {
        $blog = Blog::find($id);
        // if (File::exists('public/images'.$blog->image)) {
        //     File::delete('public/images'.$blog->image);
        // }
        // unlink('images/' . $blog->image_id);
        $path = public_path()."/images/blog".$blog->image;
unlink($path);
        $blog->delete();

        return redirect()->route('blog.allnews');
    }



    public function tinycreate()
    {
        return view('media.tinycreate');
    }

    public function tinystore(Request $request){
        $request->validate([
            'photo' => 'sometimes|nullable|image|mimes:jpeg,gif,png,jpg|max:9048',
        ]);
        $auth = Auth::id();

        $blog = new Blog();
        $blog->headline = $request->headline;
        $blog->body = $request->body;

        $blog->user_id = $auth;
        $imageName = time() . '.' . $request->image->extension();

        $request->image->move(public_path('images'), $imageName);
        $blog->image = $imageName;
        $blog->save();

        return redirect()->route('blog.allnews');
    }
}
