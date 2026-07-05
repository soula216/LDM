@pushOnce('styles')
<style>
    .service-html-editor-field .tox-tinymce {
        border-radius: 0.75rem;
        border-color: rgb(226 232 240);
        box-shadow: none;
        overflow: hidden;
        transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .service-html-editor-field .tox-tinymce:focus-within {
        border-color: rgb(59 130 246 / 0.45);
        box-shadow: 0 0 0 3px rgb(59 130 246 / 0.12);
    }

    .service-html-editor-field .tox .tox-toolbar__primary,
    .service-html-editor-field .tox .tox-toolbar-overlord {
        background: rgb(248 250 252);
    }

    .service-html-editor-field .tox .tox-statusbar {
        border-top-color: rgb(226 232 240);
    }
</style>
@endPushOnce

@pushOnce('scripts')
<script src="https://cdn.jsdelivr.net/npm/tinymce@7.6.1/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    (function () {
        const tinymceBaseUrl = 'https://cdn.jsdelivr.net/npm/tinymce@7.6.1';

        window.__vitrineServiceHtmlEditorConfig = {
            license_key: 'gpl',
            base_url: tinymceBaseUrl,
            suffix: '.min',
            height: 360,
            min_height: 280,
            menubar: 'edit view insert format tools table',
            plugins: [
                'advlist', 'autolink', 'lists', 'link', 'image', 'charmap',
                'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                'insertdatetime', 'media', 'table', 'wordcount', 'autoresize',
            ],
            toolbar: [
                'undo redo | blocks | bold italic underline strikethrough',
                'forecolor backcolor | alignleft aligncenter alignright alignjustify',
                'bullist numlist outdent indent | link image media table',
                'removeformat code fullscreen',
            ].join(' | '),
            branding: false,
            promotion: false,
            language: 'fr_FR',
            language_url: 'https://cdn.jsdelivr.net/npm/tinymce-i18n@25.2.7/langs7/fr_FR.js',
            skin: 'oxide',
            content_style: 'body { font-family: Manrope, Inter, system-ui, sans-serif; font-size: 15px; line-height: 1.65; color: #1e293b; }',
            autoresize_bottom_margin: 16,
            resize: true,
            convert_urls: false,
            relative_urls: false,
            link_default_target: '_blank',
            paste_data_images: true,
            block_formats: 'Paragraphe=p; Titre 2=h2; Titre 3=h3; Titre 4=h4',
        };

        window.__vitrineServiceHtmlEditorReady = new Promise((resolve) => {
            const waitForTinyMce = (attempts = 50) => {
                if (window.tinymce?.init) {
                    resolve(window.tinymce);
                    return;
                }
                if (attempts <= 0) {
                    resolve(null);
                    return;
                }
                setTimeout(() => waitForTinyMce(attempts - 1), 100);
            };
            waitForTinyMce();
        });

        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('form[action*="/admin/vitrine/"]').forEach((form) => {
                form.addEventListener('submit', () => {
                    if (window.tinymce) {
                        window.tinymce.triggerSave();
                    }
                });
            });
        });
    })();
</script>
@endPushOnce
