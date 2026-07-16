<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class JobApplicationController extends Controller
{
    private const APPLICATIONS_PER_PAGE = 20;

    private function ensureAdmin(): void
    {
        if (! auth()->user()?->hasRole('admin')) {
            abort(404);
        }
    }

    public function index(Request $request)
    {
        $this->ensureAdmin();

        $query = JobApplication::query();

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('job_title', 'like', '%' . $search . '%')
                    ->orWhere('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('phone', 'like', '%' . $search . '%')
                    ->orWhere('cover_letter', 'like', '%' . $search . '%')
                    ->orWhere('cv_name', 'like', '%' . $search . '%');
            });
        }

        $applications = $query
            ->orderByDesc('id')
            ->paginate(self::APPLICATIONS_PER_PAGE)
            ->withQueryString();

        return view('admin.job-applications.index', compact('applications'));
    }

    public function downloadCv(JobApplication $jobApplication): StreamedResponse
    {
        $this->ensureAdmin();

        if (! $jobApplication->hasCv()
            || ! Storage::disk('public')->exists($jobApplication->cv_path)) {
            abort(404);
        }

        return Storage::disk('public')->download(
            $jobApplication->cv_path,
            $jobApplication->cv_name ?: basename($jobApplication->cv_path)
        );
    }

    public function destroy(JobApplication $jobApplication)
    {
        $this->ensureAdmin();

        if ($jobApplication->hasCv()) {
            Storage::disk('public')->delete($jobApplication->cv_path);
        }

        $jobApplication->delete();

        return redirect()
            ->route('admin.job-applications.index')
            ->with('success', 'Candidature supprimée avec succès.');
    }
}
