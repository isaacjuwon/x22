import { Editor } from '@tiptap/core';
import StarterKit from '@tiptap/starter-kit';
import Link from '@tiptap/extension-link';
import Image from '@tiptap/extension-image';
import Underline from '@tiptap/extension-underline';
import Placeholder from '@tiptap/extension-placeholder';
import { Table } from '@tiptap/extension-table';
import { TableRow } from '@tiptap/extension-table-row';
import { TableCell } from '@tiptap/extension-table-cell';
import { TableHeader } from '@tiptap/extension-table-header';
import CodeBlockShiki from 'tiptap-extension-code-block-shiki';

window.setupTiptap = function (
  elementId,
  htmlInputId,
  jsonInputId,
  initialContent = '',
  onUpdateCallback = null,
) {
  let content = initialContent;
  try {
    if (
      typeof initialContent === 'string' &&
      (initialContent.startsWith('{') || initialContent.startsWith('['))
    ) {
      content = JSON.parse(initialContent);
    }
  } catch (e) {
    content = initialContent;
  }

  const editor = new Editor({
    element: document.getElementById(elementId),
    extensions: [
      StarterKit.configure({
        codeBlock: false,
        bulletList: {
          keepMarks: true,
          keepAttributes: false,
        },
        orderedList: {
          keepMarks: true,
          keepAttributes: false,
        },
      }),
      CodeBlockShiki.configure({
        defaultLanguage: 'javascript',
        langs: ['javascript', 'typescript', 'php', 'html', 'css', 'bash', 'json', 'sql', 'markdown', 'yaml', 'xml'],
        themes: {
          light: 'github-light',
          dark: 'github-dark',
        },
        HTMLAttributes: {
          class: 'rounded-xl border border-neutral-200 dark:border-neutral-800 bg-neutral-50 dark:bg-neutral-950 p-4 font-mono text-sm leading-relaxed my-6',
        },
      }),
      Link.configure({
        openOnClick: false,
        HTMLAttributes: {
          class: 'text-primary underline underline-offset-4 decoration-2',
        },
      }),
      Underline,
      Image.configure({
        HTMLAttributes: {
          class: 'rounded-3xl shadow-xl border border-border mx-auto my-8 max-w-full',
        },
      }),
      Table.configure({
        resizable: true,
        HTMLAttributes: {
          class: 'tiptap-table',
        },
      }),
      TableRow,
      TableHeader,
      TableCell,
      Placeholder.configure({
        placeholder: 'Write something remarkable...',
      }),
    ],
    content: content,
    editorProps: {
      attributes: {
        class:
          'tiptap prose prose-zinc prose-lg md:prose-xl max-w-none focus:outline-none min-h-[500px] px-8 py-10',
      },
    },
    shouldRerenderOnTransaction: true,
    onUpdate: ({ editor }) => {
      const htmlEl = htmlInputId ? document.getElementById(htmlInputId) : null;
      const jsonEl = jsonInputId ? document.getElementById(jsonInputId) : null;

      if (htmlEl) htmlEl.value = editor.getHTML();
      if (jsonEl) jsonEl.value = JSON.stringify(editor.getJSON());

      if (onUpdateCallback) onUpdateCallback(editor.getHTML(), editor.getJSON());
    },
    onSelectionUpdate: () => {
      if (onUpdateCallback) onUpdateCallback();
    },
    onTransaction: () => {
      if (onUpdateCallback) onUpdateCallback();
    },
  });

  // Expose editor globally for Alpine to access
  window.tiptapEditors = window.tiptapEditors || {};
  window.tiptapEditors[elementId] = editor;

  return editor;
};
