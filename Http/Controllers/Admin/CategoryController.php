<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Requests\CategoryRequest;
use Carbon\Carbon;
use Cviebrock\EloquentSluggable\Services\SlugService;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $Title = "Category List";
        $CategoryList = Category::where('del', 0)->orderBy('order_by')->paginate(10);
        return view('admin.category.categorylist', compact('CategoryList', 'Title'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $Title = "Add New Category";
        return view('admin.category.createnewcategory', compact('Title'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CategoryRequest $request)
    {
        if ($request->hasfile('image')) {
            $file = $request->file('image');
            $extension = $file->getClientOriginalExtension(); //getting image extension
            $filename = time() . '.' . $extension;
            $file->move(public_path('category/'), $filename);
        } else {
            $filename = null;
        }
        Category::create([
            'name' => $request->input('name'),
            'order_by' => $request->input('order_by'),
            'img_title' => $request->input('img_title'),
            'img_alt' => $request->input('img_alt'),
            'short_description' => $request->input('short_description'),
            'slug' => SlugService::createSlug(Category::class, 'slug', $request->name),
            'image' => $filename,
            'created_at' => Carbon::now(),
        ]);

        return redirect('admin/category')->with('msg', 'Your category has been added successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function show(Category $document)
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
        $EditSlug = Category::where(['del' => 0, 'slug' => $slug])->first();
        if (!$EditSlug) {
            $request->session()->flash('errorMsg', 'Record Not Found');
            return redirect()->back();
        }
        $Title = "Edit Category";
        $EditCategory = Category::where('slug', $slug)->first();
        return view('admin.category.editcategory', compact('EditCategory', 'Title'));
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
            $file->move(public_path('category/'), $filename);
        } else {
            $filename = $request->input('old_image');
        }

        Category::where('slug', $slug)->update([
            'name' => $request->input('name'),
            'order_by' => $request->input('order_by'),
            'img_title' => $request->input('img_title'),
            'img_alt' => $request->input('img_alt'),
            'short_description' => $request->input('short_description'),
            'slug' => SlugService::createSlug(Category::class, 'slug', $request->name),
            'image' => $filename,
        ]);
        return redirect('admin/category')->with('msg', 'Your category has been update successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $slug)
    {
        $delSlug = Category::where(['del' => 0, 'slug' => $slug])->first();
        if (!$delSlug) {
            $request->session()->flash('errorMsg', 'Record Not Found');
            return redirect()->back();
        }
        Category::where('slug', $slug)->update(['del' => 1]);
        return redirect('admin/category')->with('errormsg', 'Your category has been deleted successfully!');
    }

    public function Categorystatus(Request $request)
    {
        $data = $request->all();
        Category::where(['del' => 0, 'id' => $data['id']])->update(['status' => $data['status']]);
    }

    public function SearchCategory(Request $request)
    {
        $data = $request->all();
        if ($request->ismethod('get')) {

            if (!empty($data['name'])) {
                $name = $data['name'];
            }

            $CategoryList = Category::query();
            if (!empty($name)) {
                $CategoryList = $CategoryList->where('name', 'LIKE', "%{$name}%");
            }

            $CategoryList = $CategoryList->where('del', 0)->paginate(10);
            $CategoryList->appends($request->all());
            $Title = "Category List";
            return view('admin.category.categorylist', compact('CategoryList', 'Title'));
        }
    }
}
