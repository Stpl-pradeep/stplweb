<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\GalleryRequest;
use App\Models\Gallery;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $Title = "Gallery List";
        $galleryList = Gallery::where('del', 0)->orderBy('id','DESC')->paginate(10);
        return view('admin.gallery.gallerylist',compact('Title', 'galleryList'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $Title = "Add New Gallery";
        return view('admin.gallery.createnewgallery',compact('Title'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(GalleryRequest $request)
    {
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $extension = $file->getClientOriginalExtension();
            $filename = time() . '.' . $extension;
            $file->move(public_path('gallery/'), $filename);                                                                                                                                           
        }else {
            $filename = null;
        }
        Gallery::create([
            'title' => $request->input('title'),
            'type' => $request->input('type'),
            'video'=> $request->input('video'),
            'image'=> $filename 
        ]);
        return redirect()->back()->with('msg', 'Your gallery has been added successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $Title = "Edit Gallery";
        $editGallery = Gallery::where('del', 0)->where('id',$id)->first();
        if (empty($editGallery)) {
           return redirect()->back()->with('errormsg','Record not found');
        }
        return view('admin.gallery.editgallery',compact('Title','editGallery'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if ($request->hasfile('image')) {
            $file = $request->file('image');
            $extension = $file->getClientOriginalExtension(); //getting image extension
            $filename = time() . '.' . $extension;
            $file->move(public_path('gallery/'), $filename);
        } else {
            $filename = $request->input('old_image');
        }

        Gallery::where('id', $id)->update(['title' => $request->input('title'),
            'type' => $request->input('type'),
            'video' => $request->input('video'),
            'image' => $filename 
        ]);
        return redirect('admin/gallery')->with('msg', 'Your gallery has been update successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $delSlug = Gallery::where(['del' => 0, 'id' => $id])->first();
        if (!$delSlug) {
            return redirect()->back()->with('errormsg', 'Record not found');
        }
        Gallery::where('id', $id)->update(['del' => 1]);
        return redirect('admin/gallery')->with('errormsg', 'Your gallery has been deleted successfully!');
    }

    public function Gallerystatus(Request $request)
    {
        $data = $request->all();
        Gallery::where(['del' => 0, 'id' => $data['id']])->update(['status' => $data['status']]);
    }
}
