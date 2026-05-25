@props([
    'id'    => 'tiptap-' . uniqid(),
    'name'  => 'content',
    'value' => null,
])

<div
    class="tiptap-editor-wrapper overflow-hidden rounded-[var(--radius-box)] border border-neutral-800 bg-neutral-900 transition-all focus-within:ring-1 focus-within:ring-primary"
    x-data="tiptapEditor({
        id: @js($id),
        wireModel: @js($attributes->get('wire:model')),
        initialContent: @js($value)
    })"
    @submit.window="clearDraft()"
>

    {{-- ── Toolbar ── --}}
    <div class="flex flex-wrap items-center gap-1 border-b border-neutral-800 px-2 py-1.5">

        {{-- History --}}
        <div class="flex items-center">
            <x-ui.button type="button" variant="ghost" size="xs" @click="undo()" title="Undo">
                <x-ui.icon name="ps:arrow-counter-clockwise" class="size-4" />
            </x-ui.button>
            <x-ui.button type="button" variant="ghost" size="xs" @click="redo()" title="Redo">
                <x-ui.icon name="ps:arrow-clockwise" class="size-4" />
            </x-ui.button>
        </div>

        <x-ui.separator orientation="vertical" class="mx-1 h-5 self-center" />

        {{-- Headings --}}
        <div class="flex items-center">
            <x-ui.button type="button" variant="ghost" size="xs"
                @click="toggleHeading(1)"
                ::class="isActive('heading', { level: 1 }) ? 'bg-primary/10 !text-primary' : ''"
                title="Heading 1">
                <x-ui.icon name="ps:text-h-one" class="size-4" />
            </x-ui.button>
            <x-ui.button type="button" variant="ghost" size="xs"
                @click="toggleHeading(2)"
                ::class="isActive('heading', { level: 2 }) ? 'bg-primary/10 !text-primary' : ''"
                title="Heading 2">
                <x-ui.icon name="ps:text-h-two" class="size-4" />
            </x-ui.button>
            <x-ui.button type="button" variant="ghost" size="xs"
                @click="toggleHeading(3)"
                ::class="isActive('heading', { level: 3 }) ? 'bg-primary/10 !text-primary' : ''"
                title="Heading 3">
                <x-ui.icon name="ps:text-h-three" class="size-4" />
            </x-ui.button>
        </div>

        <x-ui.separator orientation="vertical" class="mx-1 h-5 self-center" />

        {{-- Inline marks --}}
        <div class="flex items-center">
            <x-ui.button type="button" variant="ghost" size="xs"
                @click="toggleBold()"
                ::class="isActive('bold') ? 'bg-primary/10 !text-primary' : ''"
                title="Bold">
                <x-ui.icon name="ps:text-b" variant="bold" class="size-4" />
            </x-ui.button>
            <x-ui.button type="button" variant="ghost" size="xs"
                @click="toggleItalic()"
                ::class="isActive('italic') ? 'bg-primary/10 !text-primary' : ''"
                title="Italic">
                <x-ui.icon name="ps:text-italic" class="size-4" />
            </x-ui.button>
            <x-ui.button type="button" variant="ghost" size="xs"
                @click="toggleUnderline()"
                ::class="isActive('underline') ? 'bg-primary/10 !text-primary' : ''"
                title="Underline">
                <x-ui.icon name="ps:text-underline" class="size-4" />
            </x-ui.button>
            <x-ui.button type="button" variant="ghost" size="xs"
                @click="toggleStrike()"
                ::class="isActive('strike') ? 'bg-primary/10 !text-primary' : ''"
                title="Strikethrough">
                <x-ui.icon name="ps:text-strikethrough" class="size-4" />
            </x-ui.button>
            <x-ui.button type="button" variant="ghost" size="xs"
                @click="toggleCode()"
                ::class="isActive('code') ? 'bg-primary/10 !text-primary' : ''"
                title="Inline Code">
                <x-ui.icon name="ps:code" class="size-4" />
            </x-ui.button>
            <x-ui.button type="button" variant="ghost" size="xs"
                @click="toggleLink()"
                ::class="isActive('link') ? 'bg-primary/10 !text-primary' : ''"
                title="Insert Link">
                <x-ui.icon name="ps:link" class="size-4" />
            </x-ui.button>
        </div>

        <x-ui.separator orientation="vertical" class="mx-1 h-5 self-center" />

        {{-- Block nodes --}}
        <div class="flex items-center">
            <x-ui.button type="button" variant="ghost" size="xs"
                @click="toggleBulletList()"
                ::class="isActive('bulletList') ? 'bg-primary/10 !text-primary' : ''"
                title="Bullet List">
                <x-ui.icon name="ps:list-bullets" class="size-4" />
            </x-ui.button>
            <x-ui.button type="button" variant="ghost" size="xs"
                @click="toggleOrderedList()"
                ::class="isActive('orderedList') ? 'bg-primary/10 !text-primary' : ''"
                title="Ordered List">
                <x-ui.icon name="ps:list-numbers" class="size-4" />
            </x-ui.button>
            <x-ui.button type="button" variant="ghost" size="xs"
                @click="toggleBlockquote()"
                ::class="isActive('blockquote') ? 'bg-primary/10 !text-primary' : ''"
                title="Blockquote">
                <x-ui.icon name="ps:quotes" class="size-4" />
            </x-ui.button>
            <x-ui.button type="button" variant="ghost" size="xs"
                @click="toggleCodeBlock()"
                ::class="isActive('codeBlock') ? 'bg-primary/10 !text-primary' : ''"
                title="Code Block">
                <x-ui.icon name="ps:terminal-window" class="size-4" />
            </x-ui.button>
        </div>

        <x-ui.separator orientation="vertical" class="mx-1 h-5 self-center" />

        {{-- Table --}}
        <div class="flex items-center">
            <x-ui.button type="button" variant="ghost" size="xs"
                @click="insertTable()"
                title="Insert Table">
                <x-ui.icon name="ps:table" class="size-4" />
            </x-ui.button>

            <template x-if="isActive('table')">
                <div class="flex items-center">
                    <x-ui.button type="button" variant="ghost" size="xs"
                        @click="addColumnAfter()"
                        title="Add Column After">
                        <x-ui.icon name="ps:columns" class="size-4" />
                    </x-ui.button>
                    <x-ui.button type="button" variant="ghost" size="xs"
                        @click="addRowAfter()"
                        title="Add Row After">
                        <x-ui.icon name="ps:rows" class="size-4" />
                    </x-ui.button>
                    <x-ui.button type="button" variant="danger" size="xs"
                        @click="deleteTable()"
                        title="Delete Table">
                        <x-ui.icon name="ps:trash" class="size-4" />
                    </x-ui.button>
                </div>
            </template>
        </div>

        <x-ui.separator orientation="vertical" class="mx-1 h-5 self-center" />

        {{-- Image --}}
        <x-ui.button type="button" variant="ghost" size="xs"
            @click="addImage()"
            title="Insert Image">
            <x-ui.icon name="ps:image" class="size-4" />
        </x-ui.button>

    </div>

    {{-- ── Editor surface ── --}}
    <div
        id="tiptap-el-{{ $id }}"
        class="tiptap-content min-h-64 px-5 py-4 focus:outline-none"
    ></div>

    {{-- Hidden form input --}}
    
    <input
    type="hidden"
    name="{{ $name }}"
    id="tiptap-input-{{ $id }}"
    value="{{ is_string($value) ? $value : json_encode($value) }}"
/>
    

</div>

{{-- ── Link modal (teleports to body via Sheaf UI) ── --}}
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
        <x-ui.label>URL</x-ui.label>
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
                color="green"
                @click="submitLinkModal()">
                Insert
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
        <x-ui.label>Image URL</x-ui.label>
        <x-ui.input
            type="url"
            id="tiptap-image-input-{{ $id }}"
            x-model="imageValue"
            placeholder="https://example.com/image.jpg"
            @keydown.enter="submitImageModal()"
            @keydown.escape="$modal.close('{{ $id }}-image-modal')"
            leftIcon="image"
        />
    </x-ui.field>

    <x-slot name="footer">
        <div class="flex justify-end gap-3">
            <x-ui.button
                type="button"
                variant="ghost"
                x-on:click="$modal.close('{{ $id }}-image-modal')">
                Cancel
            </x-ui.button>
            <x-ui.button
                type="button"
                color="green"
                @click="submitImageModal()">
                Insert
            </x-ui.button>
        </div>
    </x-slot>
</x-ui.modal>

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
                const inputEl = document.getElementById('tiptap-input-' + id);
                const initial = inputEl?.value ?? '';
                const draft   = localStorage.getItem('draft-' + id);

                let content = initial;
                if (draft && (!initial || ['{}', '""', 'null'].includes(initial))) {
                    content = draft;
                }

                const onUpdate = () => {
                    this.updated++;
                    const val = document.getElementById('tiptap-input-' + id).value;
                    localStorage.setItem('draft-' + id, val);
                    if (window.Livewire && config.wireModel) {
                        this.$wire.set(config.wireModel, val);
                    }
                };

                const boot = (c) => {
                    this.editor = window.setupTiptap(
                        'tiptap-el-' + id,
                        'tiptap-input-' + id,
                        c,
                        onUpdate,
                    );
                };

                try {
                    boot(content);
                    if (content === draft && window.Livewire && config.wireModel) {
                        this.$wire.set(config.wireModel, draft);
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
                this.modalValue = this.getEditor()?.getAttributes('link').href ?? 'https://';
                this.$modal.open(config.id + '-link-modal');
            },
            submitLinkModal() {
                const val    = this.modalValue.trim();
                const editor = this.getEditor();
                if (!editor) return;
                val === ''
                    ? editor.chain().focus().unsetLink().run()
                    : editor.chain().focus().extendMarkRange('link').setLink({ href: val }).run();
                this.$modal.close(config.id + '-link-modal');
                this.modalValue = '';
            },

            addImage() {
                this.imageValue = 'https://';
                this.$modal.open(config.id + '-image-modal');
            },
            submitImageModal() {
                const val    = this.imageValue.trim();
                const editor = this.getEditor();
                if (editor && val) editor.chain().focus().setImage({ src: val }).run();
                this.$modal.close(config.id + '-image-modal');
                this.imageValue = '';
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