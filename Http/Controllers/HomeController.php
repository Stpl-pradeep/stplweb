<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Banner;
use App\Models\Blog;
use App\Models\Page;
use App\Models\Service;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function Home()
    {
        $BannersList = Banner::where(['del' => 0, 'status' => 0])->orderBy('order_by')->get();
        $ServiceList = Service::where(['del' => 0, 'status' => 0])->orderBy('order_by')->limit(6)->get();
        $Blog = Blog::where(['del' => 0, 'status' => 0])->OrderBy('id', 'DESC')->limit(3)->get();
        $Pages = Page::where(['del' => 0, 'status' => 0])->first();
        return view('home', compact('BannersList','ServiceList', 'Blog', 'Pages'));
    }

}
