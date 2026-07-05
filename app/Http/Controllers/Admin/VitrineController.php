<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VitrineBlock;
use App\Services\AcademyPdfThumbnailService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class VitrineController extends Controller
{
    private const SLIDER_STORAGE_PREFIX = 'vitrine/slider/';

    private const GALLERY_STORAGE_PREFIX = 'vitrine/gallery/';

    private const LOGO_STORAGE_PREFIX = 'vitrine/logo/';

    private const SOCIAL_STORAGE_PREFIX = 'vitrine/social/';

    private const SERVICE_STORAGE_PREFIX = 'vitrine/services/';

    private const ACADEMY_STORAGE_PREFIX = 'vitrine/academy/';

    private const ACADEMY_COVER_STORAGE_PREFIX = 'vitrine/academy/covers/';

    private const ACADEMY_CATEGORIES = ['catalogue', 'guide', 'protocole', 'notice'];

    public function index(Request $request): View
    {
        $this->authorize('manage_vitrine');

        $blocks = VitrineBlock::query()->orderBy('sort_order')->get();
        $activeTab = $request->query('tab', $blocks->first()?->key ?? 'hero');

        if (! $blocks->contains('key', $activeTab)) {
            $activeTab = $blocks->first()?->key ?? 'hero';
        }

        return view('admin.vitrine.index', compact('blocks', 'activeTab'));
    }

    public function update(Request $request, VitrineBlock $vitrineBlock): RedirectResponse
    {
        $this->authorize('manage_vitrine');

        $existingContent = $vitrineBlock->content ?? [];
        $content = $this->sanitizeContent($request->input('content', []));

        if ($vitrineBlock->key === 'hero') {
            $content = $this->processHeroSlides($request, $content, $existingContent);
        }

        if ($vitrineBlock->key === 'gallery') {
            $content = $this->processGalleryItems($request, $content, $existingContent);
        }

        if ($vitrineBlock->key === 'services') {
            $content = $this->processServiceItems($request, $content, $existingContent);
        }

        if ($vitrineBlock->key === 'academy') {
            $content = $this->processAcademyDocuments($request, $content, $existingContent);
        }

        if ($vitrineBlock->key === 'process') {
            $content = $this->processProcessSteps($content);
        }

        if ($vitrineBlock->key === 'contact') {
            $content = $this->processContactItems($content);
        }

        if ($vitrineBlock->key === 'header') {
            $content = $this->processBlockLogo($request, $content, $existingContent);
        }

        if ($vitrineBlock->key === 'footer') {
            $content = $this->processBlockLogo($request, $content, $existingContent);
            $content = $this->processFooterSocialLinks($request, $content, $existingContent);
        }

        $vitrineBlock->update([
            'content' => $content,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()
            ->route('admin.vitrine.index', ['tab' => $vitrineBlock->key])
            ->with('success', "Le bloc « {$vitrineBlock->label} » a été mis à jour.");
    }

    private function processHeroSlides(Request $request, array $content, array $existingContent): array
    {
        $incomingSlides = $content['slides'] ?? [];
        $existingSlides = $existingContent['slides'] ?? [];
        $processed = [];

        foreach ($incomingSlides as $index => $slide) {
            $sourceType = ($slide['source_type'] ?? 'url') === 'upload' ? 'upload' : 'url';
            $imageUrl = trim((string) ($slide['image_url'] ?? ''));
            $existingUrl = trim((string) ($existingSlides[$index]['image_url'] ?? ''));

            if ($request->hasFile("slide_uploads.$index")) {
                $file = $request->file("slide_uploads.$index");
                $this->validateSlideUpload($file, $index);

                if ($existingUrl !== '') {
                    $this->deleteVitrineImageIfStored($existingUrl);
                }

                $imageUrl = $this->storeSliderImage($file);
                $sourceType = 'upload';
            } elseif ($sourceType === 'upload') {
                if ($imageUrl === '' && $existingUrl !== '') {
                    $imageUrl = $existingUrl;
                }
            } elseif ($sourceType === 'url' && $existingUrl !== '' && $this->isStoredVitrineImage($existingUrl) && $existingUrl !== $imageUrl) {
                $this->deleteVitrineImageIfStored($existingUrl);
            }

            if ($imageUrl === '') {
                continue;
            }

            $processed[] = [
                'image_url' => VitrineBlock::resolveImageUrl($imageUrl),
                'source_type' => $sourceType,
            ];
        }

        $newUrls = collect($processed)->pluck('image_url')->all();
        foreach ($existingSlides as $oldSlide) {
            $oldUrl = trim((string) ($oldSlide['image_url'] ?? ''));
            if ($oldUrl !== '' && ! in_array($oldUrl, $newUrls, true)) {
                $this->deleteVitrineImageIfStored($oldUrl);
            }
        }

        $content['slides'] = array_values($processed);

        return $content;
    }

    private function processGalleryItems(Request $request, array $content, array $existingContent): array
    {
        $incomingItems = $content['items'] ?? [];
        $existingItems = $existingContent['items'] ?? [];
        $processed = [];

        foreach ($incomingItems as $index => $item) {
            $sourceType = ($item['source_type'] ?? 'url') === 'upload' ? 'upload' : 'url';
            $imageUrl = trim((string) ($item['image_url'] ?? ''));
            $existingUrl = trim((string) ($existingItems[$index]['image_url'] ?? ''));

            if ($request->hasFile("gallery_uploads.$index")) {
                $file = $request->file("gallery_uploads.$index");
                $this->validateGalleryUpload($file, $index);

                if ($existingUrl !== '') {
                    $this->deleteVitrineImageIfStored($existingUrl);
                }

                $imageUrl = $this->storeGalleryImage($file);
                $sourceType = 'upload';
            } elseif ($sourceType === 'upload') {
                if ($imageUrl === '' && $existingUrl !== '') {
                    $imageUrl = $existingUrl;
                }
            } elseif ($sourceType === 'url' && $existingUrl !== '' && $this->isStoredVitrineImage($existingUrl) && $existingUrl !== $imageUrl) {
                $this->deleteVitrineImageIfStored($existingUrl);
            }

            if ($imageUrl === '' && trim((string) ($item['title'] ?? '')) === '' && trim((string) ($item['description'] ?? '')) === '') {
                continue;
            }

            $processed[] = [
                'image_url' => $imageUrl !== '' ? VitrineBlock::resolveImageUrl($imageUrl) : '',
                'source_type' => $sourceType,
                'title' => trim((string) ($item['title'] ?? '')),
                'description' => trim((string) ($item['description'] ?? '')),
            ];
        }

        $newUrls = collect($processed)->pluck('image_url')->filter()->all();
        foreach ($existingItems as $oldItem) {
            $oldUrl = trim((string) ($oldItem['image_url'] ?? ''));
            if ($oldUrl !== '' && ! in_array(VitrineBlock::resolveImageUrl($oldUrl), $newUrls, true)) {
                $this->deleteVitrineImageIfStored($oldUrl);
            }
        }

        $content['items'] = array_values($processed);

        return $content;
    }

    private function processServiceItems(Request $request, array $content, array $existingContent): array
    {
        $incomingItems = $content['items'] ?? [];
        $existingItems = $existingContent['items'] ?? [];
        $processed = [];
        $usedSlugs = [];

        foreach ($incomingItems as $index => $item) {
            $title = trim((string) ($item['title'] ?? ''));
            $description = trim((string) ($item['description'] ?? ''));
            $contentHtml = (string) ($item['content_html'] ?? '');
            $imageSourceType = ($item['image_source_type'] ?? $item['icon_source_type'] ?? 'url') === 'upload' ? 'upload' : 'url';
            $imageUrl = trim((string) ($item['image_url'] ?? $item['icon_url'] ?? ''));
            $existingUrl = trim((string) ($existingItems[$index]['image_url'] ?? $existingItems[$index]['icon_url'] ?? ''));

            if ($request->hasFile("service_image_uploads.$index")) {
                $file = $request->file("service_image_uploads.$index");
                $this->validateServiceImageUpload($file, $index);

                if ($existingUrl !== '') {
                    $this->deleteVitrineImageIfStored($existingUrl);
                }

                $imageUrl = $this->storeServiceIconImage($file);
                $imageSourceType = 'upload';
            } elseif ($request->hasFile("service_icon_uploads.$index")) {
                $file = $request->file("service_icon_uploads.$index");
                $this->validateServiceIconUpload($file, $index);

                if ($existingUrl !== '') {
                    $this->deleteVitrineImageIfStored($existingUrl);
                }

                $imageUrl = $this->storeServiceIconImage($file);
                $imageSourceType = 'upload';
            } elseif ($imageSourceType === 'upload') {
                if ($imageUrl === '' && $existingUrl !== '') {
                    $imageUrl = $existingUrl;
                }
            } elseif ($imageSourceType === 'url' && $existingUrl !== '' && $this->isStoredVitrineImage($existingUrl) && $existingUrl !== $imageUrl) {
                $this->deleteVitrineImageIfStored($existingUrl);
            }

            if ($title === '' && $description === '' && $contentHtml === '' && $imageUrl === '') {
                continue;
            }

            $slug = \Illuminate\Support\Str::slug(trim((string) ($item['slug'] ?? '')) ?: $title);
            if ($slug === '') {
                $slug = 'service-' . ($index + 1);
            }
            $baseSlug = $slug;
            $suffix = 2;
            while (in_array($slug, $usedSlugs, true)) {
                $slug = $baseSlug . '-' . $suffix;
                $suffix++;
            }
            $usedSlugs[] = $slug;

            $isActive = filter_var($item['is_active'] ?? true, FILTER_VALIDATE_BOOLEAN);

            $processedItem = [
                'title' => $title,
                'slug' => $slug,
                'description' => $description,
                'content_html' => $contentHtml,
                'image_source_type' => $imageSourceType,
                'is_active' => $isActive,
            ];

            if ($imageUrl !== '') {
                $processedItem['image_url'] = VitrineBlock::resolveImageUrl($imageUrl);
            }

            $processed[] = $processedItem;
        }

        $newUrls = collect($processed)->pluck('image_url')->filter()->all();
        foreach ($existingItems as $oldItem) {
            $oldUrl = trim((string) ($oldItem['image_url'] ?? $oldItem['icon_url'] ?? ''));
            if ($oldUrl !== '' && ! in_array(VitrineBlock::resolveImageUrl($oldUrl), $newUrls, true)) {
                $this->deleteVitrineImageIfStored($oldUrl);
            }
        }

        $content['items'] = array_values($processed);

        return $content;
    }

    private function processAcademyDocuments(Request $request, array $content, array $existingContent): array
    {
        $incoming = $content['documents'] ?? [];
        $existing = $existingContent['documents'] ?? [];
        $processed = [];

        foreach ($incoming as $index => $doc) {
            $title = trim((string) ($doc['title'] ?? ''));
            $description = trim((string) ($doc['description'] ?? ''));
            $category = (string) ($doc['category'] ?? 'catalogue');
            if (! in_array($category, self::ACADEMY_CATEGORIES, true)) {
                $category = 'catalogue';
            }

            $fileUrl = trim((string) ($doc['file_url'] ?? ''));
            $fileName = trim((string) ($doc['file_name'] ?? ''));
            $existingUrl = trim((string) ($existing[$index]['file_url'] ?? ''));
            $existingName = trim((string) ($existing[$index]['file_name'] ?? ''));

            $coverSourceType = ($doc['cover_image_source_type'] ?? 'url') === 'upload' ? 'upload' : 'url';
            $coverImageUrl = trim((string) ($doc['cover_image_url'] ?? ''));
            $existingCoverUrl = trim((string) ($existing[$index]['cover_image_url'] ?? ''));
            $existingPreviewUrl = trim((string) ($existing[$index]['pdf_preview_url'] ?? ''));
            $pdfPreviewUrl = $existingPreviewUrl;
            $pdfWasReplaced = false;

            if ($request->hasFile("academy_cover_uploads.$index")) {
                $file = $request->file("academy_cover_uploads.$index");
                $this->validateAcademyCoverUpload($file, $index);

                if ($existingCoverUrl !== '') {
                    $this->deleteVitrineImageIfStored($existingCoverUrl);
                }

                $coverImageUrl = $this->storeAcademyCoverImage($file);
                $coverSourceType = 'upload';
            } elseif ($coverSourceType === 'upload') {
                if ($coverImageUrl === '' && $existingCoverUrl !== '') {
                    $coverImageUrl = $existingCoverUrl;
                }
            } elseif ($coverSourceType === 'url' && $existingCoverUrl !== '' && $this->isStoredVitrineImage($existingCoverUrl) && $existingCoverUrl !== $coverImageUrl) {
                $this->deleteVitrineImageIfStored($existingCoverUrl);
            }

            if ($request->hasFile("academy_pdf_uploads.$index")) {
                $file = $request->file("academy_pdf_uploads.$index");
                $this->validateAcademyPdfUpload($file, $index);

                if ($existingUrl !== '') {
                    $this->deleteVitrineImageIfStored($existingUrl);
                }

                $fileUrl = $this->storeAcademyPdf($file);
                $fileName = $file->getClientOriginalName() ?: basename($fileUrl);
                $pdfWasReplaced = true;

                if ($existingPreviewUrl !== '') {
                    $this->deleteVitrineImageIfStored($existingPreviewUrl);
                    $pdfPreviewUrl = '';
                }
            } elseif ($fileUrl === '' && $existingUrl !== '') {
                $fileUrl = $existingUrl;
                if ($fileName === '') {
                    $fileName = $existingName;
                }
            }

            if ($title === '' && $description === '' && $fileUrl === '') {
                continue;
            }

            if ($fileUrl === '') {
                continue;
            }

            if ($pdfPreviewUrl === '' || $pdfWasReplaced) {
                $generatedPreview = $this->generateAcademyPdfPreview($fileUrl);
                if ($generatedPreview !== '') {
                    $pdfPreviewUrl = $generatedPreview;
                }
            }

            $processed[] = [
                'title' => $title !== '' ? $title : ($fileName !== '' ? $fileName : 'Document PDF'),
                'category' => $category,
                'description' => $description,
                'file_url' => VitrineBlock::resolveImageUrl($fileUrl),
                'file_name' => $fileName,
                'cover_image_source_type' => $coverSourceType,
                'cover_image_url' => $coverImageUrl !== '' ? VitrineBlock::resolveImageUrl($coverImageUrl) : '',
                'pdf_preview_url' => $pdfPreviewUrl !== '' ? VitrineBlock::resolveImageUrl($pdfPreviewUrl) : '',
            ];
        }

        $newUrls = collect($processed)->pluck('file_url')->filter()->all();
        $newCoverUrls = collect($processed)->pluck('cover_image_url')->filter()->all();
        $newPreviewUrls = collect($processed)->pluck('pdf_preview_url')->filter()->all();
        foreach ($existing as $oldDoc) {
            $oldUrl = trim((string) ($oldDoc['file_url'] ?? ''));
            if ($oldUrl !== '' && ! in_array(VitrineBlock::resolveImageUrl($oldUrl), $newUrls, true)) {
                $this->deleteVitrineImageIfStored($oldUrl);
            }

            $oldCoverUrl = trim((string) ($oldDoc['cover_image_url'] ?? ''));
            if ($oldCoverUrl !== '' && ! in_array(VitrineBlock::resolveImageUrl($oldCoverUrl), $newCoverUrls, true)) {
                $this->deleteVitrineImageIfStored($oldCoverUrl);
            }

            $oldPreviewUrl = trim((string) ($oldDoc['pdf_preview_url'] ?? ''));
            if ($oldPreviewUrl !== '' && ! in_array(VitrineBlock::resolveImageUrl($oldPreviewUrl), $newPreviewUrls, true)) {
                $this->deleteVitrineImageIfStored($oldPreviewUrl);
            }
        }

        $content['documents'] = array_values($processed);

        return $content;
    }

    private function processProcessSteps(array $content): array
    {
        $steps = $content['steps'] ?? [];
        $processed = [];

        foreach ($steps as $step) {
            $title = trim((string) ($step['title'] ?? ''));
            $description = trim((string) ($step['description'] ?? ''));

            if ($title === '' && $description === '') {
                continue;
            }

            $processed[] = [
                'title' => $title,
                'description' => $description,
            ];
        }

        $content['steps'] = array_values($processed);

        return $content;
    }

    private function processContactItems(array $content): array
    {
        $items = $content['items'] ?? [];
        $processed = [];

        foreach ($items as $item) {
            $icon = trim((string) ($item['icon'] ?? ''));
            $title = trim((string) ($item['title'] ?? ''));
            $value1 = trim((string) ($item['value_1'] ?? $item['value'] ?? ''));
            $value2 = trim((string) ($item['value_2'] ?? ''));

            if ($icon === '' && $title === '' && $value1 === '' && $value2 === '') {
                continue;
            }

            $processed[] = [
                'icon' => $icon,
                'title' => $title,
                'value_1' => $value1,
                'value_2' => $value2,
            ];
        }

        $content['items'] = array_values($processed);
        unset($content['form_options']);

        return $content;
    }

    private function processFooterSocialLinks(Request $request, array $content, array $existingContent): array
    {
        $incoming = $content['social_links'] ?? [];
        $existing = $existingContent['social_links'] ?? [];
        $processed = [];

        foreach ($incoming as $social) {
            $label = trim((string) ($social['label'] ?? ''));
            $url = trim((string) ($social['url'] ?? ''));
            $icon = trim((string) ($social['icon'] ?? ''));

            if ($label === '' && $url === '' && $icon === '') {
                continue;
            }

            $processed[] = [
                'label' => $label,
                'url' => $url,
                'icon' => $icon,
            ];
        }

        foreach ($existing as $oldSocial) {
            $oldUrl = trim((string) ($oldSocial['icon_url'] ?? ''));
            if ($oldUrl !== '') {
                $this->deleteVitrineImageIfStored($oldUrl);
            }
        }

        $content['social_links'] = array_values($processed);

        return $content;
    }

    private function processBlockLogo(Request $request, array $content, array $existingContent): array
    {
        $sourceType = ($content['logo_source_type'] ?? 'url') === 'upload' ? 'upload' : 'url';
        $logoUrl = trim((string) ($content['logo_url'] ?? ''));
        $existingUrl = trim((string) ($existingContent['logo_url'] ?? ''));

        if ($request->hasFile('logo_upload')) {
            $file = $request->file('logo_upload');
            $this->validateLogoUpload($file);

            if ($existingUrl !== '') {
                $this->deleteVitrineImageIfStored($existingUrl);
            }

            $logoUrl = $this->storeLogoImage($file);
            $sourceType = 'upload';
        } elseif ($sourceType === 'upload') {
            if ($logoUrl === '' && $existingUrl !== '') {
                $logoUrl = $existingUrl;
            }
        } elseif ($sourceType === 'url' && $existingUrl !== '' && $this->isStoredVitrineImage($existingUrl) && $existingUrl !== $logoUrl) {
            $this->deleteVitrineImageIfStored($existingUrl);
        }

        if ($logoUrl !== '') {
            $content['logo_url'] = VitrineBlock::resolveImageUrl($logoUrl);
            $content['logo_source_type'] = $sourceType;
        } else {
            unset($content['logo_url'], $content['logo_source_type']);
        }

        return $content;
    }

    private function validateLogoUpload(UploadedFile $file): void
    {
        request()->validate([
            'logo_upload' => 'required|file|mimes:jpeg,jpg,png,webp,gif,svg|max:5120',
        ], [
            'logo_upload.mimes' => 'Formats acceptés : JPEG, PNG, WebP, GIF, SVG.',
            'logo_upload.max' => 'Le logo ne doit pas dépasser 5 Mo.',
        ]);
    }

    private function storeLogoImage(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension() ?: 'png';
        $filename = 'logo_' . time() . '_' . uniqid() . '.' . strtolower($extension);
        $path = $file->storeAs('vitrine/logo', $filename, 'public');

        return VitrineBlock::resolveImageUrl('/storage/' . str_replace('\\', '/', $path));
    }

    private function validateGalleryUpload(UploadedFile $file, int $index): void
    {
        request()->validate([
            "gallery_uploads.$index" => 'required|image|mimes:jpeg,jpg,png,webp,gif|max:5120',
        ], [
            "gallery_uploads.$index.image" => 'Le fichier doit être une image valide.',
            "gallery_uploads.$index.mimes" => 'Formats acceptés : JPEG, PNG, WebP, GIF.',
            "gallery_uploads.$index.max" => 'L\'image ne doit pas dépasser 5 Mo.',
        ]);
    }

    private function storeGalleryImage(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension() ?: 'jpg';
        $filename = 'gallery_' . time() . '_' . uniqid() . '.' . strtolower($extension);
        $path = $file->storeAs('vitrine/gallery', $filename, 'public');

        return VitrineBlock::resolveImageUrl('/storage/' . str_replace('\\', '/', $path));
    }

    private function validateSlideUpload(UploadedFile $file, int $index): void
    {
        request()->validate([
            "slide_uploads.$index" => 'required|image|mimes:jpeg,jpg,png,webp,gif|max:5120',
        ], [
            "slide_uploads.$index.image" => 'Le fichier doit être une image valide.',
            "slide_uploads.$index.mimes" => 'Formats acceptés : JPEG, PNG, WebP, GIF.',
            "slide_uploads.$index.max" => 'L\'image ne doit pas dépasser 5 Mo.',
        ]);
    }

    private function storeSliderImage(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension() ?: 'jpg';
        $filename = 'slide_' . time() . '_' . uniqid() . '.' . strtolower($extension);
        $path = $file->storeAs('vitrine/slider', $filename, 'public');

        return VitrineBlock::resolveImageUrl('/storage/' . str_replace('\\', '/', $path));
    }

    private function validateAcademyPdfUpload(UploadedFile $file, int $index): void
    {
        request()->validate([
            "academy_pdf_uploads.$index" => 'required|file|mimes:pdf|max:20480',
        ], [
            "academy_pdf_uploads.$index.mimes" => 'Seuls les fichiers PDF sont acceptés.',
            "academy_pdf_uploads.$index.max" => 'Le PDF ne doit pas dépasser 20 Mo.',
        ]);
    }

    private function storeAcademyPdf(UploadedFile $file): string
    {
        $filename = 'academy_' . time() . '_' . uniqid() . '.pdf';
        $path = $file->storeAs('vitrine/academy', $filename, 'public');

        return VitrineBlock::resolveImageUrl('/storage/' . str_replace('\\', '/', $path));
    }

    private function validateAcademyCoverUpload(UploadedFile $file, int $index): void
    {
        request()->validate([
            "academy_cover_uploads.$index" => 'required|file|mimes:jpeg,jpg,png,webp,gif|max:5120',
        ], [
            "academy_cover_uploads.$index.mimes" => 'Formats acceptés : JPEG, PNG, WebP, GIF.',
            "academy_cover_uploads.$index.max" => 'L\'image ne doit pas dépasser 5 Mo.',
        ]);
    }

    private function storeAcademyCoverImage(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension() ?: 'jpg';
        $filename = 'academy_cover_' . time() . '_' . uniqid() . '.' . strtolower($extension);
        $path = $file->storeAs('vitrine/academy/covers', $filename, 'public');

        return VitrineBlock::resolveImageUrl('/storage/' . str_replace('\\', '/', $path));
    }

    private function generateAcademyPdfPreview(string $pdfUrl): string
    {
        $service = new AcademyPdfThumbnailService;
        $storagePath = $service->storagePathFromFileUrl($pdfUrl);

        if (! $storagePath) {
            return '';
        }

        $previewUrl = $service->generateFromStoragePath($storagePath);

        return $previewUrl ? VitrineBlock::resolveImageUrl($previewUrl) : '';
    }

    private function validateServiceIconUpload(UploadedFile $file, int $index): void
    {
        request()->validate([
            "service_icon_uploads.$index" => 'required|file|mimes:jpeg,jpg,png,webp,gif,svg|max:5120',
        ], [
            "service_icon_uploads.$index.mimes" => 'Formats acceptés : JPEG, PNG, WebP, GIF, SVG.',
            "service_icon_uploads.$index.max" => 'L\'icône ne doit pas dépasser 5 Mo.',
        ]);
    }

    private function validateServiceImageUpload(UploadedFile $file, int $index): void
    {
        request()->validate([
            "service_image_uploads.$index" => 'required|file|mimes:jpeg,jpg,png,webp,gif|max:5120',
        ], [
            "service_image_uploads.$index.mimes" => 'Formats acceptés : JPEG, PNG, WebP, GIF.',
            "service_image_uploads.$index.max" => 'L\'image ne doit pas dépasser 5 Mo.',
        ]);
    }

    private function storeServiceIconImage(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension() ?: 'png';
        $filename = 'service_' . time() . '_' . uniqid() . '.' . strtolower($extension);
        $path = $file->storeAs('vitrine/services', $filename, 'public');

        return VitrineBlock::resolveImageUrl('/storage/' . str_replace('\\', '/', $path));
    }

    private function isStoredVitrineImage(string $imageUrl): bool
    {
        return $this->storagePathFromImageUrl($imageUrl) !== null;
    }

    private function deleteVitrineImageIfStored(?string $imageUrl): void
    {
        if (! $imageUrl) {
            return;
        }

        $path = $this->storagePathFromImageUrl($imageUrl);
        if ($path && (
            str_starts_with($path, self::SLIDER_STORAGE_PREFIX)
            || str_starts_with($path, self::GALLERY_STORAGE_PREFIX)
            || str_starts_with($path, self::LOGO_STORAGE_PREFIX)
            || str_starts_with($path, self::SOCIAL_STORAGE_PREFIX)
            || str_starts_with($path, self::SERVICE_STORAGE_PREFIX)
            || str_starts_with($path, self::ACADEMY_STORAGE_PREFIX)
            || str_starts_with($path, self::ACADEMY_COVER_STORAGE_PREFIX)
        )) {
            Storage::disk('public')->delete($path);
        }
    }

    private function storagePathFromImageUrl(string $imageUrl): ?string
    {
        $normalized = VitrineBlock::resolveImageUrl($imageUrl);
        $path = parse_url($normalized, PHP_URL_PATH) ?? $normalized;

        if (preg_match('#/storage/(.+)$#', $path, $matches)) {
            return $matches[1];
        }

        if (str_starts_with($imageUrl, self::SLIDER_STORAGE_PREFIX)
            || str_starts_with($imageUrl, self::GALLERY_STORAGE_PREFIX)
            || str_starts_with($imageUrl, self::LOGO_STORAGE_PREFIX)
            || str_starts_with($imageUrl, self::SOCIAL_STORAGE_PREFIX)
            || str_starts_with($imageUrl, self::SERVICE_STORAGE_PREFIX)
            || str_starts_with($imageUrl, self::ACADEMY_STORAGE_PREFIX)
            || str_starts_with($imageUrl, self::ACADEMY_COVER_STORAGE_PREFIX)) {
            return $imageUrl;
        }

        return null;
    }

    private function sanitizeContent(array $content): array
    {
        return $this->sanitizeValue($content);
    }

    private function sanitizeValue(mixed $value): mixed
    {
        if (is_array($value)) {
            return array_map(fn ($item) => $this->sanitizeValue($item), $value);
        }

        if (is_string($value)) {
            return trim($value);
        }

        return $value;
    }
}
