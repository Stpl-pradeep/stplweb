<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PlanRequest;
use App\Models\Plan;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Cviebrock\EloquentSluggable\Services\SlugService;

class PlanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $Title = "Plan List";
        $PlanList = Plan::where('del', 0)->orderBy('order_by')->paginate(10);
        return view('admin.plans.planslist', compact('PlanList', 'Title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $Title = "Add New Plan";
        return view('admin.plans.createnewplans', compact('Title'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PlanRequest $request)
    {
        $PlansPrice = new Plan;
        $PlansPrice->title = $request->input('title');
        $PlansPrice->price = $request->input('price');
        $PlansPrice->order_by = $request->input('order_by');
        $PlansPrice->description = $request->input('description');
        $PlansPrice->img_title = $request->input('img_title');
        $PlansPrice->img_alt = $request->input('img_alt');
        $PlansPrice->slug = SlugService::createSlug(Plan::class, 'slug', $request->title);
        $PlansPrice->created_at = Carbon::now();
        if ($request->hasfile('image')) {
            $file = $request->file('image');
            $extension = $file->getClientOriginalExtension(); //getting image extension
            $filename = time() . '.' . $extension;
            $file->move(public_path('plans/'), $filename);
            $PlansPrice->image = $filename;
        } else {
            $PlansPrice->image = null;
        }
        $PlansPrice->save();
        return redirect('admin/plans/create')->with('msg', 'Your plans has been added successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Plan  $Plan
     * @return \Illuminate\Http\Response
     */
    public function show(Plan $Plan)
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
        $Title = "Edit Plans";
        $EditSlug = Plan::where(['del' => 0, 'id' => $id])->first();
        if (!$EditSlug) {
            $request->session()->flash('errorMsg', 'Record Not Found');
            return redirect()->back();
        }
        $Editplans = Plan::where('id', $id)->first();
        return view('admin.plans.editplans', compact('Editplans', 'Title'));
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
            $file->move(public_path('plans/'), $filename);
            // $Updbanner->image = $filename;
        } else {
            $filename = $request->input('old_image');
        }

        Plan::where('id', $id)->update([
            'title' => $request->input('title'),
            'price' => $request->input('price'),
            'order_by' => $request->input('order_by'),
            'description' => $request->input('description'),
            'img_title' => $request->input('img_title'),
            'img_alt' => $request->input('img_alt'),
            'slug' => SlugService::createSlug(Plan::class, 'slug', $request->title),
            'image' => $filename,
        ]);

        return redirect('admin/plans')->with('msg', 'Your plans has been update successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $delSlug = Plan::where(['id' => $id])->first();
        if (!$delSlug) {
            $request->session()->flash('errorMsg', 'Record Not Found');
            return redirect()->back();
        }
        $Plans = Plan::where('id', $id)->update(['del' => 1]);
        return redirect('admin/plans')->with('errormsg', 'Your plans has been deleted successfully!');
    }

    public function plansStatus(Request $request)
    {
        $data = $request->all();
        Plan::where(['del' => 0, 'id' => $data['id']])->update(['status' => $data['status']]);
    }
}
