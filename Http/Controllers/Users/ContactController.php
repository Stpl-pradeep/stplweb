<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Http\Requests\SupportContactReq;
use App\Models\Contact;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function supportContact(Request $request){
        $Title = "Support";
        return view('users.pages.supportcontact',compact('Title'));
    }

    public function saveSupportContact(SupportContactReq $request)
    {
        Contact::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'mobile' => $request->input('mobile'),
            'subject' => $request->input('subject'),
            'message' => $request->input('message'),
            'created_at' => Carbon::now(),
        ]);

        return redirect()->back()->with('msg', 'Your enquiry has been send successfully!');
    }
}
