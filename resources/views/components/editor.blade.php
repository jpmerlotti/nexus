@props(['id' => 'markdown-editor-' . uniqid()])

<div x-data="{
        value: @entangle($attributes->wire('model')),
        easyMDE: null,
        initEditor() {
            this.easyMDE = new EasyMDE({
                element: this.$refs.textarea,
                initialValue: this.value || '',
                spellChecker: false,
                autosave: {
                    enabled: false,
                },
                status: false,
                placeholder: '{{ $attributes->get('placeholder', '') }}',
                toolbar: [
                    'bold', 'italic', 'heading', '|',
                    'quote', 'unordered-list', 'ordered-list', '|',
                    'link', '|',
                    'preview', 'side-by-side', 'fullscreen', '|',
                    'guide'
                ],
                renderingConfig: {
                    singleLineBreaks: false,
                    codeSyntaxHighlighting: true,
                },
                /* 
                   We use a single class here to avoid 'DOMTokenList.add' errors with spaces.
                   Additional styling is handled in the <style> block below.
                */
                previewClass: 'prose',
            });

            this.easyMDE.codemirror.on('change', () => {
                this.value = this.easyMDE.value();
            });

            this.$watch('value', (val) => {
                if (val !== this.easyMDE.value()) {
                    this.easyMDE.value(val || '');
                }
            });
        }
    }" x-init="initEditor()" {{ $attributes->whereDoesntStartWith('wire:model')->class(['relative']) }} wire:ignore>
    <!-- EasyMDE CSS -->
    <link rel="stylesheet" href="https://unpkg.com/easymde/dist/easymde.min.css">
    <!-- EasyMDE & CodeMirror JS -->
    <script src="https://unpkg.com/easymde/dist/easymde.min.js"></script>

    <div
        class="rounded-xl border border-stone-200 dark:border-stone-800 bg-white dark:bg-stone-900 shadow-sm transition-all duration-200">
        <textarea x-ref="textarea" class="hidden"></textarea>
    </div>

    <style>
        .EasyMDEContainer .CodeMirror {
            border: 0 !important;
            min-height: 300px !important;
            background: white !important;
            color: #44403c !important;
            /* stone-700 */
            font-family: inherit;
            z-index: 1;
        }

        .dark .EasyMDEContainer .CodeMirror {
            background: #1c1917 !important;
            /* stone-900 */
            color: #d6d3d1 !important;
            /* stone-300 */
        }

        .EasyMDEContainer .editor-toolbar {
            border: 0 !important;
            border-bottom: 1px solid #e7e5e4 !important;
            /* stone-200 */
            background: rgba(250, 250, 249, 0.5) !important;
            /* stone-50/50 */
            opacity: 1 !important;
            border-top-left-radius: 0.75rem;
            border-top-right-radius: 0.75rem;
        }

        .dark .EasyMDEContainer .editor-toolbar {
            border-bottom-color: #292524 !important;
            /* stone-800 */
            background: rgba(12, 10, 9, 0.5) !important;
            /* stone-950/50 */
        }

        .EasyMDEContainer .editor-toolbar button {
            color: #78716c !important;
            /* stone-500 */
            border: 1px solid transparent !important;
        }

        .dark .EasyMDEContainer .editor-toolbar button {
            color: #a8a29e !important;
            /* stone-400 */
        }

        .EasyMDEContainer .editor-toolbar button:hover {
            background: #e7e5e4 !important;
            /* stone-200 */
            color: #44403c !important;
            /* stone-700 */
        }

        .dark .EasyMDEContainer .editor-toolbar button:hover {
            background: #292524 !important;
            /* stone-800 */
            color: #f5f5f4 !important;
            /* stone-100 */
        }

        .EasyMDEContainer .editor-toolbar button.active {
            background: #fef3c7 !important;
            /* amber-100 */
            color: #d97706 !important;
            /* amber-600 */
        }

        .dark .EasyMDEContainer .editor-toolbar button.active {
            background: rgba(120, 53, 15, 0.3) !important;
            /* amber-900/30 */
            color: #fbbf24 !important;
            /* amber-400 */
        }

        /* Fix Preview and Side-by-side */
        .editor-preview,
        .editor-preview-side {
            background: white !important;
            z-index: 50 !important;
            padding: 2rem !important;
            max-width: none !important;
        }

        .dark .editor-preview,
        .dark .editor-preview-side {
            background: #0c0a09 !important;
            /* stone-950 */
            color: #d6d3d1 !important;
        }

        /* Apply Tipography Invert in Dark Mode */
        .dark .editor-preview.prose,
        .dark .editor-preview-side.prose {
            --tw-prose-body: var(--color-stone-300);
            --tw-prose-headings: var(--color-white);
            --tw-prose-links: var(--color-amber-400);
            --tw-prose-bold: var(--color-white);
            --tw-prose-counters: var(--color-stone-400);
            --tw-prose-bullets: var(--color-stone-600);
            --tw-prose-quotes: var(--color-stone-100);
            --tw-prose-quote-borders: var(--color-stone-700);
            --tw-prose-captions: var(--color-stone-400);
            --tw-prose-code: var(--color-white);
            --tw-prose-pre-code: var(--color-stone-300);
            --tw-prose-pre-bg: var(--color-stone-800);
            --tw-prose-th-borders: var(--color-stone-700);
            --tw-prose-td-borders: var(--color-stone-800);
        }

        /* Side by side specific */
        .editor-preview-side {
            border-left: 1px solid #e7e5e4 !important;
        }

        .dark .editor-preview-side {
            border-left-color: #292524 !important;
        }

        .CodeMirror-cursor {
            border-left: 2px solid #f59e0b !important;
            /* amber-500 */
        }
    </style>
</div>