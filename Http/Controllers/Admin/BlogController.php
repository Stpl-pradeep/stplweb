<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Requests\BlogRequest;
use Carbon\Carbon;
use Cviebrock\EloquentSluggable\Services\SlugService;
use Illuminate\Support\Facades\Auth;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $Title = "Post List";
        $BlogList = Blog::where('del', 0)->orderBy('id', 'DESC')->paginate(10);
        $CategoryList = Category::where('del', 0)->orderBy('id', 'DESC')->get();
        return view('admin.blog.bloglist', compact('BlogList', 'Title','CategoryList'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $Title = "Add New Post";
        $CategoryList = Category::where('del', 0)->orderBy('id', 'DESC')->get();
        return view('admin.blog.createnewblog', compact('Title', 'CategoryList'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BlogRequest $request)
    {
        if ($request->hasfile('image')) {
            $file = $request->file('image');
            $extension = $file->getClientOriginalExtension(); //getting image extension
            $filename = time() . '.' . $extension;
            $file->move(public_path('blog/'), $filename);
            // $Updbanner->image = $filename;
        } else {
            $filename = null;
        }
        $UserId = Auth::guard('admin')->user()->id;
        Blog::create([
            'title' => $request->input('title'),
            // 'url' => $request->input('url'),
            'cat_id' => $request->input('cat_id'),
            'img_title' => $request->input('img_title'),
            'img_alt' => $request->input('img_alt'),
            'slug' => SlugService::createSlug(Blog::class, 'slug', $request->title),
            'image' => $filename,
            'short_description' => $request->input('short_description'),
            'description' => $request->input('description'),
            'user_id' => $UserId,
            'created_at' => Carbon::now(),
        ]);

        return redirect('admin/blog/create')->with('msg', 'Your blog has been added successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Blog  $blog
     * @return \Illuminate\Http\Response
     */
    public function show(Blog $blog)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $slug)
    {
        $Title = "Edit Post";
        $EditSlug = Blog::where(['del' => 0, 'slug' => $slug])->first();
        if (!$EditSlug) {
            $request->session()->flash('errorMsg', 'Record Not Found');
            return redirect()->back();
        }
        $EditBlog = Blog::where('slug', $slug)->first();
        $CategoryList = Category::where('del', 0)->orderBy('id', 'DESC')->get();
        return view('admin.blog.editblog', compact('EditBlog', 'CategoryList', 'Title'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $slug)
    {
        if ($request->hasfile('image')) {
            $file = $request->file('image');
            $extension = $file->getClientOriginalExtension(); //getting image extension
            $filename = time() . '.' . $extension;
            $file->move(public_path('blog/'), $filename);
            // $Updbanner->image = $filename;
        } else {
            $filename = $request->input('old_image');
        }

        $UserId = Auth::guard('admin')->user()->id;

        Blog::where('slug', $slug)->update([
            'title' => $request->input('title'),
            // 'url' => $request->input('url'),
            'cat_id' => $request->input('cat_id'),
            'img_title' => $request->input('img_title'),
            'img_alt' => $request->input('img_alt'),
            'slug' => SlugService::createSlug(Blog::class, 'slug', $request->title),
            'image' => $filename,
            'short_description' => $request->input('short_description'),
            'description' => $request->input('description'),
            'user_id' => $UserId,
        ]);
        return redirect('admin/blog')->with('msg', 'Your blog has been update successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $slug)
    {
        $delSlug = Blog::where(['del' => 0, 'slug' => $slug])->first();
        if (!$delSlug) {
            $request->session()->flash('errorMsg', 'Record Not Found');
            return redirect()->back();
        }
        $Pages = Blog::where('slug', $slug)->update(['del' => 1]);
        return redirect('admin/blog')->with('errormsg', 'Your blog has been deleted successfully!');
    }

    public function blogstatus(Request $request)
    {
        $data = $request->all();
        Blog::where(['del' => 0, 'id' => $data['id']])->update(['status' => $data['status']]);
    }

    public function SearchBloglist(Request $request)
    {
        $data = $request->all();
        if ($request->ismethod('get')) {

            if (!empty($data['title'])) {
                $title = $data['title'];
            }

            if (!empty($data['cat_id'])) {
                $cat_id = $data['cat_id'];
            }

            $BlogList = Blog::query();
            if (!empty($title)) {
                $BlogList = $BlogList->where('title', 'LIKE', "%{$title}%");
            }

            if (!empty($cat_id)) {
                $BlogList = $BlogList->where('cat_id', $cat_id);
            }

            $BlogList = $BlogList->where(['del'=>0,'status'=>0])->paginate(10);
            $BlogList->appends($request->all());
            $Title = "Blog List";
            $CategoryList = Category::where('del', 0)->orderBy('id', 'DESC')->get();
            return view('admin.blog.bloglist', compact('BlogList', 'Title','CategoryList'));
        }
    }
}
