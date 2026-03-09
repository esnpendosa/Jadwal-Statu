<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;

class EmailTemplateController extends Controller
{
    public function index()
    {
        $templates = EmailTemplate::all();
        return view('admin.email-templates.index', compact('templates'));
    }

    public function edit(EmailTemplate $emailTemplate)
    {
        return view('admin.email-templates.edit', ['template' => $emailTemplate]);
    }

    public function update(Request $request, EmailTemplate $emailTemplate)
    {
        $data = $request->validate([
            'subject.id' => 'required|string|max:255',
            'subject.en' => 'required|string|max:255',
            'subject.zh' => 'nullable|string|max:255',
            'body.id'    => 'required|string',
            'body.en'    => 'required|string',
            'body.zh'    => 'nullable|string',
        ]);

        $emailTemplate->update([
            'subject' => $data['subject'],
            'body'    => $data['body'],
        ]);

        return redirect()->route('admin.email-templates.index')
            ->with('success', __('admin.email_template_updated'));
    }
}
