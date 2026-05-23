<div
  class="tiptap-editor-wrapper overflow-hidden rounded-[0.375rem] border-2 border-[#e0e0d8] transition-all focus-within:border-[#16a34a] dark:border-[#2d2d28] dark:focus-within:border-[#4ade80]"
  x-data="tiptapEditor({
        id: @js($id),
        wireModel: @js($attributes->get('wire:model')),
        initialContent: @js($value)
    })"
  @submit.window="clearDraft()"
>
  <!-- Toolbar -->
  <div class="tiptap-toolbar z-60 flex flex-wrap gap-1 border-b border-[#e0e0d8] bg-[#f0f0ea] p-2 dark:border-[#2d2d28] dark:bg-[#1a1a16]">

    <div class="flex items-center gap-1 rounded-[0.25rem] bg-[#f5f5f0] p-1 dark:bg-[#0d0d0b]">
      <x-ui.button variant="ghost" size="sm" squared @click="undo()" title="Undo">
        <x-ui.icon name="ps:arrow-counter-clockwise" />
      </x-ui.button>
      <x-ui.button variant="ghost" size="sm" squared @click="redo()" title="Redo">
        <x-ui.icon name="ps:arrow-clockwise" />
      </x-ui.button>
    </div>

    <div class="mx-1 h-6 w-px self-center bg-[#e0e0d8] dark:bg-[#2d2d28]"></div>

    <div class="flex items-center gap-1 rounded-[0.25rem] bg-[#f5f5f0] p-1 dark:bg-[#0d0d0b]">
      <x-ui.button variant="ghost" size="sm" squared @click="toggleHeading(1)"
        x-bind:class="isActive('heading', { level: 1 }) ? 'bg-[#16a34a] text-[#f0fdf4] shadow-sm dark:bg-[#4ade80] dark:text-[#052e16]' : ''"
        title="Heading 1">
        <x-ui.icon name="ps:text-h-one" variant="bold" />
      </x-ui.button>
      <x-ui.button variant="ghost" size="sm" squared @click="toggleHeading(2)"
        x-bind:class="isActive('heading', { level: 2 }) ? 'bg-[#16a34a] text-[#f0fdf4] shadow-sm dark:bg-[#4ade80] dark:text-[#052e16]' : ''"
        title="Heading 2">
        <x-ui.icon name="ps:text-h-two" variant="bold" />
      </x-ui.button>
      <x-ui.button variant="ghost" size="sm" squared @click="toggleHeading(3)"
        x-bind:class="isActive('heading', { level: 3 }) ? 'bg-[#16a34a] text-[#f0fdf4] shadow-sm dark:bg-[#4ade80] dark:text-[#052e16]' : ''"
        title="Heading 3">
        <x-ui.icon name="ps:text-h-three" variant="bold" />
      </x-ui.button>
    </div>

    <div class="mx-1 h-6 w-px self-center bg-[#e0e0d8] dark:bg-[#2d2d28]"></div>

    <div class="flex items-center gap-1 rounded-[0.25rem] bg-[#f5f5f0] p-1 dark:bg-[#0d0d0b]">
      <x-ui.button variant="ghost" size="sm" squared @click="toggleBold()"
        x-bind:class="isActive('bold') ? 'bg-[#16a34a] text-[#f0fdf4] shadow-sm dark:bg-[#4ade80] dark:text-[#052e16]' : ''"
        title="Bold">
        <x-ui.icon name="ps:text-b" variant="bold" />
      </x-ui.button>
      <x-ui.button variant="ghost" size="sm" squared @click="toggleItalic()"
        x-bind:class="isActive('italic') ? 'bg-[#16a34a] text-[#f0fdf4] shadow-sm dark:bg-[#4ade80] dark:text-[#052e16]' : ''"
        title="Italic">
        <x-ui.icon name="ps:text-italic" variant="bold" />
      </x-ui.button>
      <x-ui.button variant="ghost" size="sm" squared @click="toggleUnderline()"
        x-bind:class="isActive('underline') ? 'bg-[#16a34a] text-[#f0fdf4] shadow-sm dark:bg-[#4ade80] dark:text-[#052e16]' : ''"
        title="Underline">
        <x-ui.icon name="ps:text-underline" variant="bold" />
      </x-ui.button>
      <x-ui.button variant="ghost" size="sm" squared @click="toggleStrike()"
        x-bind:class="isActive('strike') ? 'bg-[#16a34a] text-[#f0fdf4] shadow-sm dark:bg-[#4ade80] dark:text-[#052e16]' : ''"
        title="Strike">
        <x-ui.icon name="ps:text-strikethrough" variant="bold" />
      </x-ui.button>
      <x-ui.button variant="ghost" size="sm" squared @click="toggleCode()"
        x-bind:class="isActive('code') ? 'bg-[#16a34a] text-[#f0fdf4] shadow-sm dark:bg-[#4ade80] dark:text-[#052e16]' : ''"
        title="Inline Code">
        <x-ui.icon name="ps:code" variant="bold" />
      </x-ui.button>
      <x-ui.button variant="ghost" size="sm" squared @click="toggleLink()"
        x-bind:class="isActive('link') ? 'bg-[#16a34a] text-[#f0fdf4] shadow-sm dark:bg-[#4ade80] dark:text-[#052e16]' : ''"
        title="Insert Link">
        <x-ui.icon name="ps:link" variant="bold" />
      </x-ui.button>
    </div>

    <div class="mx-1 h-6 w-px self-center bg-[#e0e0d8] dark:bg-[#2d2d28]"></div>

    <div class="flex items-center gap-1 rounded-[0.25rem] bg-[#f5f5f0] p-1 dark:bg-[#0d0d0b]">
      <x-ui.button variant="ghost" size="sm" squared @click="toggleBulletList()"
        x-bind:class="isActive('bulletList') ? 'bg-[#16a34a] text-[#f0fdf4] shadow-sm dark:bg-[#4ade80] dark:text-[#052e16]' : ''"
        title="Bullet List">
        <x-ui.icon name="ps:list-bullets" variant="bold" />
      </x-ui.button>
      <x-ui.button variant="ghost" size="sm" squared @click="toggleOrderedList()"
        x-bind:class="isActive('orderedList') ? 'bg-[#16a34a] text-[#f0fdf4] shadow-sm dark:bg-[#4ade80] dark:text-[#052e16]' : ''"
        title="Ordered List">
        <x-ui.icon name="ps:list-numbers" variant="bold" />
      </x-ui.button>
      <x-ui.button variant="ghost" size="sm" squared @click="toggleBlockquote()"
        x-bind:class="isActive('blockquote') ? 'bg-[#16a34a] text-[#f0fdf4] shadow-sm dark:bg-[#4ade80] dark:text-[#052e16]' : ''"
        title="Blockquote">
        <x-ui.icon name="ps:quotes" variant="bold" />
      </x-ui.button>
      <x-ui.button variant="ghost" size="sm" squared @click="toggleCodeBlock()"
        x-bind:class="isActive('codeBlock') ? 'bg-[#16a34a] text-[#f0fdf4] shadow-sm dark:bg-[#4ade80] dark:text-[#052e16]' : ''"
        title="Code Block">
        <x-ui.icon name="ps:terminal-window" variant="bold" />
      </x-ui.button>
    </div>

    <div class="mx-1 h-6 w-px self-center bg-[#e0e0d8] dark:bg-[#2d2d28]"></div>

    <div class="flex items-center gap-1 rounded-[0.25rem] bg-[#f5f5f0] p-1 dark:bg-[#0d0d0b]" x-data="{ open: false }">
      <x-ui.button variant="ghost" size="sm" squared @click="insertTable()" title="Insert Table">
        <x-ui.icon name="ps:table" />
      </x-ui.button>
      <template x-if="isActive('table')">
        <div class="flex items-center gap-1">
          <x-ui.button variant="ghost" size="sm" squared @click="addColumnAfter()" title="Add Column">
            <x-ui.icon name="ps:columns" />
          </x-ui.button>
          <x-ui.button variant="ghost" size="sm" squared @click="addRowAfter()" title="Add Row">
            <x-ui.icon name="ps:rows" />
          </x-ui.button>
          <x-ui.button variant="ghost" size="sm" squared @click="deleteTable()"
            class="!text-[#dc2626] hover:!bg-[#dc2626]/10 dark:!text-[#f87171] dark:hover:!bg-[#f87171]/10"
            title="Delete Table">
            <x-ui.icon name="ps:trash" />
          </x-ui.button>
        </div>
      </template>
    </div>

    <div class="mx-1 h-6 w-px self-center bg-[#e0e0d8] dark:bg-[#2d2d28]"></div>

    <div class="flex items-center gap-1 rounded-[0.25rem] bg-[#f5f5f0] p-1 dark:bg-[#0d0d0b]">
      <x-ui.button variant="ghost" size="sm" squared @click="addImage()" title="Add Image">
        <x-ui.icon name="ps:image" />
      </x-ui.button>
    </div>
  </div>

  <!-- Custom Modal for Link & Image -->
  <div
    x-show="modalOpen"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    style="display: none"
    class="fixed inset-0 z-[999] flex items-center justify-center overflow-y-auto p-4"
    role="dialog"
    aria-modal="true"
  >
    <!-- Backdrop -->
    <div
      class="fixed inset-0 bg-[#f5f5f0]/95 backdrop-blur-xl dark:bg-[#0d0d0b]/95"
      @click="closeModal()"
    ></div>

    <!-- Modal Content -->
    <div
      class="relative w-full max-w-md transform rounded-[0.375rem] border border-[#16a34a] bg-[#ffffff] p-8 shadow-[0_0_24px_rgba(22,163,74,0.08)] ring-0 transition-all dark:border-[#4ade80] dark:bg-[#111110] dark:shadow-[0_0_24px_rgba(74,222,128,0.08)]"
      x-transition:enter="transition ease-out duration-200"
      x-transition:enter-start="opacity-0 scale-95 translate-y-4"
      x-transition:enter-end="opacity-100 scale-100 translate-y-0"
    >
      <div class="mb-6 flex items-center justify-between">
        <h3 class="text-lg font-black text-[#1a1a14] dark:text-[#c8c8b8]" x-text="modalTitle"></h3>
        <button
          type="button"
          @click="closeModal()"
          class="flex h-8 w-8 items-center justify-center rounded-[0.25rem] text-[#5a5a52] transition-colors hover:bg-[#f0f0ea] dark:text-[#808075] dark:hover:bg-[#1a1a16]"
        >
          <x-ui.icon name="ps:x" />
        </button>
      </div>

      <div class="space-y-4">
        <div>
          <label class="mb-2 block text-xs font-bold uppercase tracking-widest text-[#5a5a52] before:content-['>_'] before:text-[#16a34a] before:font-bold dark:text-[#808075] dark:before:text-[#4ade80]">
            <span x-text="modalType === 'link' ? 'URL' : 'Image URL'"></span>
          </label>
          <input
            type="url"
            id="tiptap-modal-input"
            x-model="modalValue"
            @keydown.enter="submitModal()"
            @keydown.escape="closeModal()"
            class="w-full rounded-[0.1875rem] border border-[#d4d4cc] bg-[#ffffff] px-4 py-3 text-[#1a1a14] caret-[#16a34a] transition-all placeholder:text-[#a8a89c] focus:border-[#16a34a] focus:shadow-[0_0_0_2px_rgba(22,163,74,0.12)] focus:outline-none dark:border-[#2d2d28] dark:bg-[#111110] dark:text-[#c8c8b8] dark:caret-[#4ade80] dark:placeholder:text-[#525248] dark:focus:border-[#4ade80] dark:focus:shadow-[0_0_0_2px_rgba(74,222,128,0.12)]"
            :placeholder="modalType === 'link' ? 'https://example.com' : 'https://example.com/image.jpg'"
          />
        </div>

        <div class="flex gap-3 pt-2">
          <x-ui.button variant="solid" size="lg" class="flex-1 font-bold" @click="closeModal()">
            Cancel
          </x-ui.button>
          <x-ui.button variant="primary" size="lg" class="flex-1 font-bold" @click="submitModal()">
            Insert
          </x-ui.button>
        </div>
      </div>
    </div>
  </div>

  <!-- Editor Content -->
  <div id="tiptap-el-{{ $id }}" class="tiptap-content min-h-[500px] bg-[#ffffff] dark:bg-[#111110]"></div>

  <!-- Hidden Input for Form -->
  <input type="hidden" name="{{ $name }}" id="tiptap-input-{{ $id }}" value="{{ $value }}" />
</div>

<script>
  if (typeof window.tiptapEditor !== 'function') {
    window.tiptapEditor = function (config) {
      return {
        editor: null,
        updated: 0,
        modalOpen: false,
        modalType: '',
        modalValue: '',
        modalTitle: '',
        init() {
          const id = config.id;
          const editorKey = 'tiptap-el-' + id;

          if (window.tiptapEditors?.[editorKey]) {
            window.tiptapEditors[editorKey].destroy();
            delete window.tiptapEditors[editorKey];
          }

          const inputEl = document.getElementById('tiptap-input-' + id);
          const initialContent = inputEl ? inputEl.value : '';
          const savedDraft = localStorage.getItem('draft-' + id);
          let contentToLoad = initialContent;

          if (
            savedDraft &&
            (!initialContent ||
              initialContent === '{}' ||
              initialContent === '""' ||
              initialContent === 'null')
          ) {
            contentToLoad = savedDraft;
          }

          const setupCallback = () => {
            this.updated++;
            const content = document.getElementById('tiptap-input-' + id).value;
            localStorage.setItem('draft-' + id, content);
            if (window.Livewire && config.wireModel) {
              this.$wire.set(config.wireModel, content);
            }
          };

          try {
            this.editor = window.setupTiptap(
              editorKey,
              'tiptap-input-' + id,
              contentToLoad,
              setupCallback,
            );
            if (contentToLoad === savedDraft && window.Livewire && config.wireModel) {
              this.$wire.set(config.wireModel, savedDraft);
            }
          } catch (e) {
            console.error('Failed to parse Tiptap draft. Clearing corrupt draft.', e);
            localStorage.removeItem('draft-' + id);
            this.editor = window.setupTiptap(
              editorKey,
              'tiptap-input-' + id,
              initialContent,
              setupCallback,
            );
          }
        },
        destroy() {
          const editorKey = 'tiptap-el-' + config.id;
          if (window.tiptapEditors?.[editorKey]) {
            window.tiptapEditors[editorKey].destroy();
            delete window.tiptapEditors[editorKey];
          }
        },
        openModal(type, title, defaultValue = '') {
          this.modalType = type;
          this.modalTitle = title;
          this.modalValue = defaultValue;
          this.modalOpen = true;
          this.$nextTick(() => {
            document.getElementById('tiptap-modal-input')?.focus();
          });
        },
        closeModal() {
          this.modalOpen = false;
          this.modalValue = '';
          this.modalType = '';
        },
        submitModal() {
          const value = this.modalValue.trim();
          if (this.modalType === 'link') {
            const editor = this.getEditor();
            if (!editor) return;
            if (value === '') {
              editor.chain().focus().unsetLink().run();
            } else {
              editor.chain().focus().extendMarkRange('link').setLink({ href: value }).run();
            }
          } else if (this.modalType === 'image') {
            const editor = this.getEditor();
            if (editor && value) {
              editor.chain().focus().setImage({ src: value }).run();
            }
          }
          this.closeModal();
        },
        getEditor() {
          return window.tiptapEditors['tiptap-el-' + config.id] || this.editor;
        },
        toggleBold() { this.getEditor()?.chain().focus().toggleBold().run(); },
        toggleLink() {
          const previousUrl = this.getEditor()?.getAttributes('link').href;
          this.openModal('link', 'Insert Link URL', previousUrl || 'https://');
        },
        toggleItalic() { this.getEditor()?.chain().focus().toggleItalic().run(); },
        toggleUnderline() { this.getEditor()?.chain().focus().toggleUnderline().run(); },
        toggleStrike() { this.getEditor()?.chain().focus().toggleStrike().run(); },
        toggleCode() { this.getEditor()?.chain().focus().toggleCode().run(); },
        toggleHeading(level) { this.getEditor()?.chain().focus().toggleHeading({ level }).run(); },
        toggleBulletList() { this.getEditor()?.chain().focus().toggleBulletList().run(); },
        toggleOrderedList() { this.getEditor()?.chain().focus().toggleOrderedList().run(); },
        toggleBlockquote() { this.getEditor()?.chain().focus().toggleBlockquote().run(); },
        toggleCodeBlock() { this.getEditor()?.chain().focus().toggleCodeBlock().run(); },
        undo() { this.getEditor()?.chain().focus().undo().run(); },
        redo() { this.getEditor()?.chain().focus().redo().run(); },
        insertTable() {
          this.getEditor()?.chain().focus().insertTable({ rows: 3, cols: 3, withHeaderRow: true }).run();
        },
        addColumnAfter() { this.getEditor()?.chain().focus().addColumnAfter().run(); },
        deleteColumn() { this.getEditor()?.chain().focus().deleteColumn().run(); },
        addRowAfter() { this.getEditor()?.chain().focus().addRowAfter().run(); },
        deleteRow() { this.getEditor()?.chain().focus().deleteRow().run(); },
        deleteTable() { this.getEditor()?.chain().focus().deleteTable().run(); },
        addImage() { this.openModal('image', 'Insert Image URL', 'https://'); },
        isActive(type, opts = {}) {
          this.updated;
          return this.getEditor() ? this.getEditor().isActive(type, opts) : false;
        },
        clearDraft() { localStorage.removeItem('draft-' + config.id); },
      };
    };
  }
</script>