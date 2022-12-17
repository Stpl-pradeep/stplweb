<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SettingsModel;
// use Carbon\Carbon;
// use Cviebrock\EloquentSluggable\Services\SlugService;

class SiteSettingsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
    public function edit(Request $request,$id)
    {
         $EditSlug = SettingsModel::where(['del' => 0, 'id' => $id])->first();
        if (!$EditSlug) {
            $request->session()->flash('errorMsg', 'Record Not Found');
            return redirect()->back();
        }
         $EditSettings = SettingsModel::where('id', $id)->first();

         $Title = "Edit Settings";
        
        return view('admin.setting.editsetting', compact('Title','EditSettings'));
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
        if ($request->hasfile('logo')) {
        	$file = $request->file('logo');
        	$extension = $file->getClientOriginalExtension(); //getting logo extension
        	$filename = time() . '.' . $extension;
            $file->move(public_path('logo/') , $filename);
    	    // $Updbanner->logo = $filename;
        }else{
        	$filename = $request->input('old_logo');
        }
        if ($request->hasfile('favicon')) {
        	$file = $request->file('favicon');
        	$extension = $file->getClientOriginalExtension(); //getting favicon extension
        	$filename1 = time() . '.' . $extension;
            $file->move(public_path('logo/') , $filename1);
    	    // $Updbanner->favicon = $filename;
        }else{
        	$filename1 = $request->input('old_favicon');
        }

        SettingsModel::where('id', $id)->update([
            'title' => $request->input('title'),
            'keywords' => $request->input('keywords'),
            'description' => $request->input('description'),
            'top_email' => $request->input('top_email'),
            'top_address' => $request->input('top_address'),
            'logo' => $filename,
            'favicon' => $filename1,
        ]);
        // dd($data);
        return redirect('admin/settings/1/edit')->with('msg', 'Your settings has been update successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
