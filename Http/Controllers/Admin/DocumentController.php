<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\DocumentRequest;
use App\Models\Document;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $Title = "Document List";
        $DocumentList = Document::where('del', 0)->orderBy('id', 'DESC')->paginate(10);
        return view('admin.document.documentlist', compact('Title', 'DocumentList'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $Title = "Add New Document";
        return view('admin.document.createnewdocument', compact('Title'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DocumentRequest $request)
    {
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $extension = $file->getClientOriginalExtension();
            $filenameImage = time() . '.' . $extension;
            $file->move(public_path('document/'), $filenameImage);
        } else {
            $filenameImage = null;
        }
        if ($request->hasFile('document_file')) {
            $file = $request->file('document_file');
            $extension = $file->getClientOriginalExtension();
            $filenameDocument = time() . '.' . $extension;
            $file->move(public_path('document/'), $filenameDocument);
        } else {
            $filenameDocument = null;
        }
        Document::create([
            'title' => $request->input('title'),
            'image' => $filenameImage,
            'document_file' => $filenameDocument
        ]);
        return redirect()->back()->with('msg', 'Your document has been added successfully!');
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
        $editDocument = Document::where('del', 0)->where('id', $id)->first();
        if (empty($editDocument)) {
            return redirect()->back()->with('errormsg', 'Record not found');
        }
        return view('admin.document.editdocument', compact('Title', 'editDocument'));
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
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $extension = $file->getClientOriginalExtension();
            $filenameImage = time() . '.' . $extension;
            $file->move(public_path('document/'), $filenameImage);
        } else {
            $filenameImage = $request->input('old_image');
        }
        if ($request->hasFile('document_file')) {
            $file = $request->file('document_file');
            $extension = $file->getClientOriginalExtension();
            $filenameDocument = time() . '.' . $extension;
            $file->move(public_path('document/'), $filenameDocument);
        } else {
            $filenameDocument = $request->input('old_document_file');
        }
        Document::where('id', $id)->update([
            'title' => $request->input('title'),
            'image' => $filenameImage,
            'document_file' => $filenameDocument
        ]);
        return redirect('admin/document')->with('msg', 'Your document has been update successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $delSlug = Document::where(['del' => 0, 'id' => $id])->first();
        if (!$delSlug) {
            return redirect()->back()->with('errormsg', 'Record not found');
        }
        Document::where('id', $id)->update(['del' => 1]);
        return redirect('admin/document')->with('errormsg', 'Your document has been deleted successfully!');
    }

    public function Documentstatus(Request $request)
    {
        $data = $request->all();
        Document::where(['del' => 0, 'id' => $data['id']])->update(['status' => $data['status']]);
    }
}
