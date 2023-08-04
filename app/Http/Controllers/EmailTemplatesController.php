<?php

namespace App\Http\Controllers;

use App\Models\EmailTemplates;
use Illuminate\Http\Request;

class EmailTemplatesController extends Controller
{
    public function templateList(){
        $list = EmailTemplates::orderBy('id', 'asc')->get();
        return view('panel.email.list', compact('list'));
    }

    public function templateAddOrUpdate($id = null){
        if ($id == null){
            $template = null;
        }else{
            $template = EmailTemplates::where('id', $id)->firstOrFail();
        }

        return view('panel.email.form', compact('template'));
    }

    public function templateDelete($id = null){
        $template = EmailTemplates::where('id', $id)->firstOrFail();
        $template->delete();
        return back()->with(['message' => 'Deleted Successfully', 'type' => 'success']);
    }

    public function templateAddOrUpdateSave(Request $request){

        if ($request->template_id != 'undefined'){
            $template = EmailTemplates::where('id', $request->template_id)->firstOrFail();
        }else{
            $template = new EmailTemplates();
        }

        $template->title = $request->title;
        $template->subject = $request->subject;
        $template->content = $request->content;
        $template->save();
    }
    
}
