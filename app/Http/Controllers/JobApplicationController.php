<?php

namespace App\Http\Controllers;

use App\Models\JobApplication;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class JobApplicationController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'job_title' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:50',
            'cover_letter' => 'nullable|string|max:10000',
            'cv' => 'required|file|mimes:pdf,doc,docx|max:10240',
        ], [
            'job_title.required' => 'L’offre d’emploi est obligatoire.',
            'name.required' => 'Votre nom est obligatoire.',
            'email.required' => 'Votre e-mail est obligatoire.',
            'email.email' => 'L’adresse e-mail n’est pas valide.',
            'phone.required' => 'Le numéro de téléphone est obligatoire.',
            'cv.required' => 'Veuillez téléverser votre CV.',
            'cv.mimes' => 'Le CV doit être un fichier PDF ou Word (DOC, DOCX).',
            'cv.max' => 'Le CV ne doit pas dépasser 10 Mo.',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('vitrine.recrutement')
                ->withErrors($validator)
                ->withInput()
                ->with('job_application_job_title', $request->input('job_title'));
        }

        $data = $validator->validated();
        unset($data['cv']);

        if ($request->hasFile('cv')) {
            $file = $request->file('cv');
            $extension = strtolower($file->getClientOriginalExtension() ?: 'pdf');
            $filename = 'cv_' . time() . '_' . uniqid() . '.' . $extension;

            $data['cv_path'] = $file->storeAs('job-applications', $filename, 'public');
            $data['cv_name'] = $file->getClientOriginalName();
        }

        JobApplication::create($data);

        return redirect()
            ->route('vitrine.recrutement')
            ->with('job_application_success', 'Votre candidature a bien été envoyée. Nous vous recontacterons rapidement.');
    }
}
