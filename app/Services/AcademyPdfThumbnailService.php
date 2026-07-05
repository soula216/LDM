<?php

namespace App\Services;

use App\Models\VitrineBlock;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AcademyPdfThumbnailService
{
    public const PREVIEW_STORAGE_DIR = 'vitrine/academy/previews';

    public function generateFromStoragePath(string $storageRelativePath): ?string
    {
        $storageRelativePath = str_replace('\\', '/', trim($storageRelativePath));

        if ($storageRelativePath === '' || ! str_ends_with(strtolower($storageRelativePath), '.pdf')) {
            return null;
        }

        if (! Storage::disk('public')->exists($storageRelativePath)) {
            return null;
        }

        $pdfPath = Storage::disk('public')->path($storageRelativePath);
        Storage::disk('public')->makeDirectory(self::PREVIEW_STORAGE_DIR);

        $outputFilename = 'preview_' . time() . '_' . uniqid() . '.jpg';
        $outputRelative = self::PREVIEW_STORAGE_DIR . '/' . $outputFilename;
        $outputPath = Storage::disk('public')->path($outputRelative);

        $command = sprintf(
            'gs -dSAFER -dBATCH -dNOPAUSE -sDEVICE=jpeg -dFirstPage=1 -dLastPage=1 -r144 -dJPEGQ=85 -sOutputFile=%s %s 2>&1',
            escapeshellarg($outputPath),
            escapeshellarg($pdfPath)
        );

        exec($command, $output, $exitCode);

        if ($exitCode !== 0 || ! is_file($outputPath)) {
            Log::warning('Échec génération aperçu PDF Academy', [
                'pdf' => $storageRelativePath,
                'exit_code' => $exitCode,
                'output' => implode("\n", $output),
            ]);

            if (is_file($outputPath)) {
                @unlink($outputPath);
            }

            return null;
        }

        return VitrineBlock::resolveImageUrl('/storage/' . $outputRelative);
    }

    public function storagePathFromFileUrl(?string $fileUrl): ?string
    {
        if (! $fileUrl) {
            return null;
        }

        $normalized = VitrineBlock::resolveImageUrl($fileUrl);
        $path = parse_url($normalized, PHP_URL_PATH) ?? $normalized;

        if (preg_match('#/storage/(.+)$#', $path, $matches)) {
            return $matches[1];
        }

        if (str_starts_with($fileUrl, 'vitrine/academy/')) {
            return $fileUrl;
        }

        return null;
    }
}
