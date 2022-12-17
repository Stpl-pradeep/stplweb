<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use App\Http\Requests\PagesRequest;
use Carbon\Carbon;
use Cviebrock\EloquentSluggable\Services\SlugService;


class PagesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   
        $Title = "Page List";
        $PagesList = Page::where('del', 0)->orderBy('id', 'DESC')->paginate(10);
        return view('admin.pages.pageslist', compact('PagesList','Title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $Title = "Add New Page";
        return view('admin.pages.createnewpages', compact('Title'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PagesRequest $request)
    {
        if ($request->hasfile('image')) {
        	$file = $request->file('image');
        	$extension = $file->getClientOriginalExtension(); //getting image extension
        	$filename = time() . '.' . $extension;
            $file->move(public_path('pages/') , $filename);
    	    // $Updbanner->image = $filename;
        }else{
        	$filename = null;
        }

        Page::create([
            'title' => $request->input('title'),
            'img_title' => $request->input('img_title'),
            'img_alt' => $request->input('img_alt'),
            'slug' => SlugService::createSlug(page::class, 'slug', $request->title),
            'image' => $filename,
            'short_description' => $request->input('short_description'),
            'description' => $request->input('description'),
            'created_at' => Carbon::now(),
        ]);

        return redirect('admin/pages/create')->with('msg', 'Your page has been added successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Page  $page
     * @return \Illuminate\Http\Response
     */
    public function show(Page $page)
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
        $EditSlug = Page::where(['del' => 0, 'slug' => $slug])->first();
        if (!$EditSlug) {
            $request->session()->flash('errorMsg', 'Record Not Found');
            return redirect()->back();
        }
        $EditPages = Page::where('slug', $slug)->first();
        return view('admin.pages.editpages', compact('EditPages'));
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
            $file->move(public_path('pages/') , $filename);
    	    // $Updbanner->image = $filename;
        }else{
        	$filename = $request->input('old_image');
        }

        Page::where('slug', $slug)->update([
            'title' => $request->input('title'),
            'img_title' => $request->input('img_title'),
            'img_alt' => $request->input('img_alt'),
            'slug' => SlugService::createSlug(Page::class, 'slug', $request->title),
            'image' => $filename,
            'short_description' => $request->input('short_description'),
            'description' => $request->input('description'),
        ]);
        return redirect('admin/pages')->with('msg', 'Your pages has been update successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$slug)
    {
        $delSlug = Page::where(['del' => 0, 'slug' => $slug])->first();
        if (!$delSlug) {
            $request->session()->flash('errorMsg', 'Record Not Found');
            return redirect()->back();
        }
        $Pages = Page::where('slug', $slug)->update(['del' => 1]);
        return redirect('admin/pages')->with('errormsg', 'Your pages has been deleted successfully!');
    }

    public function pagesstatus(Request $request)
    {
        $data = $request->all();
        Page::where(['del' => 0, 'id' => $data['id']])->update(['status' => $data['status']]);
    }

    public function SearchPageist(Request $request)
   {
      $data = $request->all();
      if ($request->ismethod('get')) {

         if (!empty($data['title'])) {
            $title = $data['title'];
         }

         $PagesList = Page::query();
         if (!empty($title)) {
            $PagesList = $PagesList->where('title', 'LIKE', "%{$title}%");
         }

         $PagesList = $PagesList->paginate(10);
         $PagesList->appends($request->all());
         $Title = "Page List";
         return view('admin.pages.pageslist', compact('PagesList','Title'));
      }
   }

}