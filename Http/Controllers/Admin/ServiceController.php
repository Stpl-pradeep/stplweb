<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Http\Requests\ServiceRequest;
use Carbon\Carbon;
use Cviebrock\EloquentSluggable\Services\SlugService;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $Title = "Service List";
        $ServiceList = Service::where('del', 0)->orderBy('order_by')->paginate(10);
        return view('admin.services.servicelist', compact('ServiceList', 'Title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $Title = "Add New Service";
        return view('admin.services.createnewservice', compact('Title'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ServiceRequest $request)
    {
        $Service = new Service;
        $Service->title = $request->input('title');
        $Service->order_by = $request->input('order_by');
        $Service->short_description = $request->input('short_description');
        $Service->description = $request->input('description');
        $Service->img_title = $request->input('img_title');
        $Service->img_alt = $request->input('img_alt');
        $Service->slug = SlugService::createSlug(Service::class, 'slug', $request->title);
        $Service->created_at = Carbon::now();
        if ($request->hasfile('image')) {
            $file = $request->file('image');
            $extension = $file->getClientOriginalExtension(); //getting image extension
            $filename = time() . '.' . $extension;
            $file->move(public_path('service/'), $filename);
            $Service->image = $filename;
        } else {
            $Service->image = null;
        }
        $Service->save();
        return redirect('admin/service/create')->with('msg', 'Your service has been added successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Banner  $banner
     * @return \Illuminate\Http\Response
     */
    public function show(Service $banner)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $Title = "Edit Service";
        $EditSlug = Service::where(['del' => 0, 'id' => $id])->first();
        if (!$EditSlug) {
            $request->session()->flash('errorMsg', 'Record Not Found');
            return redirect()->back();
        }
        $EditService = Service::where('id', $id)->first();
        return view('admin.services.editservice', compact('EditService', 'Title'));
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
            $file->move(public_path('service/'), $filename);
            // $Updbanner->image = $filename;
        } else {
            $filename = $request->input('old_image');
        }

        Service::where('id', $id)->update([
            'title' => $request->input('title'),
            'order_by' => $request->input('order_by'),
            'short_description' => $request->input('short_description'),
            'description' => $request->input('description'),
            'img_title' => $request->input('img_title'),
            'img_alt' => $request->input('img_alt'),
            'slug' => SlugService::createSlug(Service::class, 'slug', $request->title),
            'image' => $filename,
        ]);

        return redirect('admin/service')->with('msg', 'Your service has been update successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $delSlug = Service::where(['id' => $id])->first();
        if (!$delSlug) {
            $request->session()->flash('errorMsg', 'Record Not Found');
            return redirect()->back();
        }
        $Service = Service::where('id', $id)->update(['del' => 1]);
        return redirect('admin/service')->with('errormsg', 'Your service has been deleted successfully!');
    }

    public function serviceStatus(Request $request)
    {
        $data = $request->all();
        Service::where(['del' => 0, 'id' => $data['id']])->update(['status' => $data['status']]);
    }
}
