@props([
    'id'         => 'tiptap-' . uniqid(),
    'name'       => 'content',
    'value'      => null,
    'htmlModel'  => null,
    'jsonModel'  => null,
])

<div
    class="tiptap-editor-wrapper flex flex-col overflow-hidden border border-neutral-200 bg-neutral-50 transition-all focus-within:border-primary dark:border-neutral-800 dark:bg-neutral-950"
    x-data="tiptapEditor({
        id: @js($id),
        jsonModel: @js($jsonModel ?? $attributes->get('wire:model')),
        htmlModel: @js($htmlModel),
        initialContent: @js($value)
    })"
    @submit.window="clearDraft()"
>

    {{-- ── Toolbar ── --}}
    <div class="flex flex-wrap items-center gap-1 border-b border-neutral-200 bg-neutral-100/50 px-2 py-1.5 dark:border-neutral-800 dark:bg-neutral-900/50">

        {{-- History --}}
        <div class="flex items-center">
            <x-ui.button type="button" variant="ghost" size="xs" @mousedown.prevent="undo()" title="Undo">
                <x-ui.icon name="ps:arrow-counter-clockwise" class="size-4" />
            </x-ui.button>
            <x-ui.button type="button" variant="ghost" size="xs" @mousedown.prevent="redo()" title="Redo">
                <x-ui.icon name="ps:arrow-clockwise" class="size-4" />
            </x-ui.button>
        </div>

        <x-ui.separator vertical class="mx-1 h-5 self-center" />

        {{-- Headings --}}
        <div class="flex items-center">
            <x-ui.button type="button" variant="ghost" size="xs"
                @mousedown.prevent="toggleHeading(1)"
                ::data-active="isActive('heading', { level: 1 }) ? 'true' : 'false'"
                title="Heading 1">
                <x-ui.icon name="ps:text-h-one" class="size-4" />
            </x-ui.button>
            <x-ui.button type="button" variant="ghost" size="xs"
                @mousedown.prevent="toggleHeading(2)"
                ::data-active="isActive('heading', { level: 2 }) ? 'true' : 'false'"
                title="Heading 2">
                <x-ui.icon name="ps:text-h-two" class="size-4" />
            </x-ui.button>
            <x-ui.button type="button" variant="ghost" size="xs"
                @mousedown.prevent="toggleHeading(3)"
                ::data-active="isActive('heading', { level: 3 }) ? 'true' : 'false'"
                title="Heading 3">
                <x-ui.icon name="ps:text-h-three" class="size-4" />
            </x-ui.button>
        </div>

        <x-ui.separator vertical class="mx-1 h-5 self-center" />

        {{-- Inline marks --}}
        <div class="flex items-center">
            <x-ui.button type="button" variant="ghost" size="xs"
                @mousedown.prevent="toggleBold()"
                ::data-active="isActive('bold') ? 'true' : 'false'"
                title="Bold">
                <x-ui.icon name="ps:text-b" variant="bold" class="size-4" />
            </x-ui.button>
            <x-ui.button type="button" variant="ghost" size="xs"
                @mousedown.prevent="toggleItalic()"
                ::data-active="isActive('italic') ? 'true' : 'false'"
                title="Italic">
                <x-ui.icon name="ps:text-italic" class="size-4" />
            </x-ui.button>
            <x-ui.button type="button" variant="ghost" size="xs"
                @mousedown.prevent="toggleUnderline()"
                ::data-active="isActive('underline') ? 'true' : 'false'"
                title="Underline">
                <x-ui.icon name="ps:text-underline" class="size-4" />
            </x-ui.button>
            <x-ui.button type="button" variant="ghost" size="xs"
                @mousedown.prevent="toggleStrike()"
                ::data-active="isActive('strike') ? 'true' : 'false'"
                title="Strikethrough">
                <x-ui.icon name="ps:text-strikethrough" class="size-4" />
            </x-ui.button>
            <x-ui.button type="button" variant="ghost" size="xs"
                @mousedown.prevent="toggleCode()"
                ::data-active="isActive('code') ? 'true' : 'false'"
                title="Inline Code">
                <x-ui.icon name="ps:code" class="size-4" />
            </x-ui.button>
            <x-ui.button type="button" variant="ghost" size="xs"
                @mousedown.prevent="toggleLink()"
                ::data-active="isActive('link') ? 'true' : 'false'"
                title="Insert Link">
                <x-ui.icon name="ps:link" class="size-4" />
            </x-ui.button>
        </div>

        <x-ui.separator vertical class="mx-1 h-5 self-center" />

        {{-- Block nodes --}}
        <div class="flex items-center">
            <x-ui.button type="button" variant="ghost" size="xs"
                @mousedown.prevent="toggleBulletList()"
                ::data-active="isActive('bulletList') ? 'true' : 'false'"
                title="Bullet List">
                <x-ui.icon name="ps:list-bullets" class="size-4" />
            </x-ui.button>
            <x-ui.button type="button" variant="ghost" size="xs"
                @mousedown.prevent="toggleOrderedList()"
                ::data-active="isActive('orderedList') ? 'true' : 'false'"
                title="Ordered List">
                <x-ui.icon name="ps:list-numbers" class="size-4" />
            </x-ui.button>
            <x-ui.button type="button" variant="ghost" size="xs"
                @mousedown.prevent="toggleBlockquote()"
                ::data-active="isActive('blockquote') ? 'true' : 'false'"
                title="Blockquote">
                <x-ui.icon name="ps:quotes" class="size-4" />
            </x-ui.button>
            <x-ui.button type="button" variant="ghost" size="xs"
                @mousedown.prevent="toggleCodeBlock()"
                ::data-active="isActive('codeBlock') ? 'true' : 'false'"
                title="Code Block">
                <x-ui.icon name="ps:terminal-window" class="size-4" />
            </x-ui.button>
        </div>

        <x-ui.separator vertical class="mx-1 h-5 self-center" />

        {{-- Table --}}
        <div class="flex items-center">
            <x-ui.button type="button" variant="ghost" size="xs"
                @mousedown.prevent="insertTable()"
                ::data-active="isActive('table') ? 'true' : 'false'"
                title="Insert Table">
                <x-ui.icon name="ps:table" class="size-4" />
            </x-ui.button>

            <template x-if="isActive('table')">
                <div class="flex items-center">
                    <x-ui.button type="button" variant="ghost" size="xs"
                        @mousedown.prevent="addColumnAfter()"
                        title="Add Column After">
                        <x-ui.icon name="ps:columns" class="size-4" />
                    </x-ui.button>
                    <x-ui.button type="button" variant="ghost" size="xs"
                        @mousedown.prevent="addRowAfter()"
                        title="Add Row After">
                        <x-ui.icon name="ps:rows" class="size-4" />
                    </x-ui.button>
                    <x-ui.button type="button" variant="ghost" color="red" size="xs"
                        @mousedown.prevent="deleteTable()"
                        title="Delete Table">
                        <x-ui.icon name="ps:trash" class="size-4" />
                    </x-ui.button>
                </div>
            </template>
        </div>

        <x-ui.separator vertical class="mx-1 h-5 self-center" />

        {{-- Image --}}
        <x-ui.button type="button" variant="ghost" size="xs"
            @mousedown.prevent="addImage()"
            title="Insert Image">
            <x-ui.icon name="ps:image" class="size-4" />
        </x-ui.button>

    </div>

    {{-- ── Editor surface ── --}}
    <div
        id="tiptap-el-{{ $id }}"
        class="tiptap-content min-h-64 bg-white px-5 py-4 text-neutral-900 focus:outline-none dark:bg-neutral-900 dark:text-neutral-100"
    ></div>

    {{-- Hidden form inputs --}}
    <input
        type="hidden"
        name="{{ $name }}_json"
        id="tiptap-json-input-{{ $id }}"
        value="{{ is_string($value) ? $value : json_encode($value) }}"
    />
    <input
        type="hidden"
        name="{{ $name }}"
        id="tiptap-html-input-{{ $id }}"
    />

    {{-- ── Link modal ── --}}
    <x-ui.modal
        :id="$id . '-link-modal'"
        width="md"
        position="center"
        backdrop="dark"
        animation="scale"
        x-on:modal-opened.window="
            if ($event.detail.id === '{{ $id }}-link-modal') {
                $nextTick(() => document.getElementById('tiptap-modal-input-{{ $id }}')?.focus())
            }
        "
    >
        <x-ui.field>
            <x-ui.label class="term-prompt">{{ __('URL') }}</x-ui.label>
            <x-ui.input
                type="url"
                id="tiptap-modal-input-{{ $id }}"
                x-model="modalValue"
                placeholder="https://example.com"
                @keydown.enter="submitLinkModal()"
                @keydown.escape="$modal.close('{{ $id }}-link-modal')"
                leftIcon="link"
            />
        </x-ui.field>

        <x-slot name="footer">
            <div class="flex justify-end gap-3">
                <x-ui.button
                    type="button"
                    variant="ghost"
                    x-on:click="$modal.close('{{ $id }}-link-modal')">
                    Cancel
                </x-ui.button>
                <x-ui.button
                type="button"
                variant="outline"
                color="green"
                @click="submitLinkModal()">
                {{ __('Insert Link') }}
            </x-ui.button>
        </div>
    </x-slot>
</x-ui.modal>

{{-- ── Image modal ── --}}
<x-ui.modal
    :id="$id . '-image-modal'"
    width="md"
    position="center"
    backdrop="dark"
    animation="scale"
    x-on:modal-opened.window="
        if ($event.detail.id === '{{ $id }}-image-modal') {
            $nextTick(() => document.getElementById('tiptap-image-input-{{ $id }}')?.focus())
        }
    "
>
    <x-ui.field>
        <x-ui.label class="term-prompt">{{ __('Image URL') }}</x-ui.label>
        <x-ui.input
            type="url"
            id="tiptap-image-input-{{ $id }}"
            x-model="imageValue"
            placeholder="https://example.com/image.jpg"
            @keydown.enter="submitImageModal()"
            @keydown.escape="$modal.close('{{ $id }}-image-modal')"
            leftIcon="ps:image"
        />
    </x-ui.field>

    <x-slot name="footer">
        <div class="flex justify-end gap-3">
            <x-ui.button
                type="button"
                variant="ghost"
                x-on:click="$modal.close('{{ $id }}-image-modal')">
                {{ __('Cancel') }}
            </x-ui.button>
            <x-ui.button
                type="button"
                variant="outline"
                color="green"
                @click="submitImageModal()">
                {{ __('Insert Image') }}
            </x-ui.button>
        </div>
    </x-slot>
</x-ui.modal>

</div>

{{-- ── Alpine component (registered once) ── --}}
<script>
if (typeof window.tiptapEditor !== 'function') {
    window.tiptapEditor = function (config) {
        return {
            editor:     null,
            updated:    0,
            modalValue: '',
            imageValue: '',

            init() {
                const id      = config.id;
                const jsonInput = document.getElementById('tiptap-json-input-' + id);
                const initial = jsonInput?.value ?? '';
                const draft   = localStorage.getItem('draft-' + id);

                let content = initial;
                if (draft && (!initial || ['{}', '""', 'null'].includes(initial))) {
                    content = draft;
                }

                const onUpdate = (html, json) => {
                    this.updated++;
                    localStorage.setItem('draft-' + id, JSON.stringify(json));
                    
                    if (window.Livewire) {
                        if (config.jsonModel) this.$wire.set(config.jsonModel, json);
                        if (config.htmlModel) this.$wire.set(config.htmlModel, html);
                    }
                };

                const boot = (c) => {
                    this.editor = window.setupTiptap(
                        'tiptap-el-' + id,
                        'tiptap-html-input-' + id,
                        'tiptap-json-input-' + id,
                        c,
                        onUpdate,
                    );
                };

                try {
                    boot(content);
                    if (content === draft && window.Livewire) {
                        if (config.jsonModel) this.$wire.set(config.jsonModel, JSON.parse(draft));
                    }
                } catch (e) {
                    console.error('Tiptap draft corrupt — resetting.', e);
                    localStorage.removeItem('draft-' + id);
                    boot(initial);
                }
            },

            /* ── Editor accessor ── */
            getEditor() {
                return window.tiptapEditors?.['tiptap-el-' + config.id] ?? this.editor;
            },

            /* ── Commands ── */
            toggleBold()        { this.getEditor()?.chain().focus().toggleBold().run(); },
            toggleItalic()      { this.getEditor()?.chain().focus().toggleItalic().run(); },
            toggleUnderline()   { this.getEditor()?.chain().focus().toggleUnderline().run(); },
            toggleStrike()      { this.getEditor()?.chain().focus().toggleStrike().run(); },
            toggleCode()        { this.getEditor()?.chain().focus().toggleCode().run(); },
            toggleHeading(lvl)  { this.getEditor()?.chain().focus().toggleHeading({ level: lvl }).run(); },
            toggleBulletList()  { this.getEditor()?.chain().focus().toggleBulletList().run(); },
            toggleOrderedList() { this.getEditor()?.chain().focus().toggleOrderedList().run(); },
            toggleBlockquote()  { this.getEditor()?.chain().focus().toggleBlockquote().run(); },
            toggleCodeBlock()   { this.getEditor()?.chain().focus().toggleCodeBlock().run(); },
            undo()              { this.getEditor()?.chain().focus().undo().run(); },
            redo()              { this.getEditor()?.chain().focus().redo().run(); },

            toggleLink() {
                const editor = this.getEditor();
                if (!editor) return;
                this._savedSelection = editor.state.selection;
                this.modalValue = editor.getAttributes('link').href ?? 'https://';
                this.$modal.open(config.id + '-link-modal');
            },
            submitLinkModal() {
                const val    = this.modalValue.trim();
                const editor = this.getEditor();
                if (!editor) return;

                // Restore selection if saved
                if (this._savedSelection) {
                    editor.commands.setTextSelection(this._savedSelection);
                }

                if (val === '') {
                    editor.chain().focus().extendMarkRange('link').unsetLink().run();
                } else {
                    // If selection is empty, insert the link text
                    if (editor.state.selection.empty) {
                        editor.chain().focus().insertContent(`<a href="${val}">${val}</a>`).run();
                    } else {
                        editor.chain().focus().extendMarkRange('link').setLink({ href: val }).run();
                    }
                }

                this.$modal.close(config.id + '-link-modal');
                this.modalValue = '';
                this._savedSelection = null;
            },

            addImage() {
                const editor = this.getEditor();
                if (!editor) return;
                this._savedImageSelection = editor.state.selection;
                this.imageValue = 'https://';
                this.$modal.open(config.id + '-image-modal');
            },
            submitImageModal() {
                const val    = this.imageValue.trim();
                const editor = this.getEditor();
                if (editor && val) {
                    if (this._savedImageSelection) {
                        editor.commands.setTextSelection(this._savedImageSelection);
                    }
                    editor.chain().focus().setImage({ src: val }).run();
                }
                this.$modal.close(config.id + '-image-modal');
                this.imageValue = '';
                this._savedImageSelection = null;
            },

            insertTable()    { this.getEditor()?.chain().focus().insertTable({ rows: 3, cols: 3, withHeaderRow: true }).run(); },
            addColumnAfter() { this.getEditor()?.chain().focus().addColumnAfter().run(); },
            addRowAfter()    { this.getEditor()?.chain().focus().addRowAfter().run(); },
            deleteTable()    { this.getEditor()?.chain().focus().deleteTable().run(); },

            isActive(type, opts = {}) {
                void this.updated;
                return this.getEditor()?.isActive(type, opts) ?? false;
            },

            clearDraft() {
                localStorage.removeItem('draft-' + config.id);
            },
        };
    };
}
</script>