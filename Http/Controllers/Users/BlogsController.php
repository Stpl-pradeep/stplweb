<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Blog;
use App\Models\Category;

class BlogsController extends Controller
{
  public function BlogList($slug=null)
  {
    $Title = "Blogs";
    if (!empty($slug)) {
      $Category = Category::where('slug',$slug)->OrderBy('id', 'DESC')->first();
      if (!empty($Category->id)) {
         $Blog = Blog::where(['cat_id'=>$Category->id,'del'=>0,'status'=>0])->OrderBy('id', 'DESC')->paginate(12);
       return view('users.blog.blog', compact('Blog','Title','Category'));
      }elseif($slug=="all"){
        if (!empty($Category->id)) {
         $Blog = Blog::where('cat_id','!=',2)->where(['cat_id'=>$Category->id,'del'=>0,'status'=>0])->OrderBy('id', 'DESC')->paginate(12);
       return view('users.blog.blog', compact('Blog', 'Title'));
        }
      }

    }
    $Blog = Blog::where(['del'=>0,'status'=>0])->OrderBy('id', 'DESC')->paginate(12);
    return view('users.blog.blog', compact('Blog', 'Title'));
  }

  public function BlogDetail($slug)
  {
    $Blog = Blog::where(['del'=>0,'status'=>0])->OrderBy('id', 'DESC')->limit(6)->get();
    $BlogDetail = Blog::where(['del'=>0,'status'=>0,'slug'=>$slug])->firstOrFail();
    $Category = Category::where(['del' => 0, 'status' => 0])->OrderBy('id', 'DESC')->limit(6)->get();
    return view('users.blog.single', compact('BlogDetail','Blog', 'Category'));
  }
}