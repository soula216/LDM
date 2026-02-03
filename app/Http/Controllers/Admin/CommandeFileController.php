<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Commande;
use App\Models\CommandeFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;

class CommandeFileController extends Controller
{
    public function store(Request $request, Commande $commande)
    {
        // Validation personnalisée pour les empreintes (STL et JPY)
        // Utiliser validation par extension car Laravel peut ne pas reconnaître le MIME type des fichiers STL
        if ($request->input('type') === 'empreinte') {
            $validated = $request->validate([
                'type' => 'required|in:empreinte,image',
                'files' => 'required|array',
                'files.*' => [
                    'file',
                    'max:50000',
                    function ($attribute, $value, $fail) {
                        if ($value) {
                            $extension = strtolower($value->getClientOriginalExtension());
                            if (!in_array($extension, ['stl', 'jpy'])) {
                                $fail('L\'extension .' . $extension . ' n\'est pas autorisée. Seuls les fichiers STL et JPY sont acceptés.');
                            }
                        }
                    },
                ],
            ]);
        } else {
            $validated = $request->validate([
                'type' => 'required|in:empreinte,image',
                'files' => 'required|array',
                'files.*' => 'file|max:5000|mimes:png,jpg,jpeg',
            ]);
        }

        foreach ($request->file('files', []) as $file) {
            // Préserver l'extension originale du fichier
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $fileName = pathinfo($originalName, PATHINFO_FILENAME);
            $uniqueFileName = $fileName . '_' . time() . '_' . uniqid() . '.' . $extension;
            
            $path = $file->storeAs("commandes/{$commande->id}", $uniqueFileName, 'public');

            CommandeFile::create([
                'commande_id' => $commande->id,
                'type' => $validated['type'],
                'path' => $path,
                'original_name' => $originalName,
                'mime' => $file->getMimeType(),
                'size' => $file->getSize(),
                'uploaded_by' => auth()->id(),
            ]);
        }

        Cache::forget("admin.commandes.show.{$commande->id}");

        return back()->with('success', 'Fichiers uploadés avec succès');
    }

    public function destroy(Commande $commande, CommandeFile $file)
    {
        Storage::disk('public')->delete($file->path);
        $file->delete();

        Cache::forget("admin.commandes.show.{$commande->id}");

        return back()->with('success', 'Fichier supprimé');
    }
}
