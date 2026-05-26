<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Support\Str;

class ContentRenderer
{
    /**
     * Minimal server-side renderer for Tiptap JSON.
     * Since composer installation failed, we use a simple parser
     * or fallback to raw string if it's legacy HTML.
     */
    public static function render($content): string
    {
        if (empty($content)) {
            return '';
        }

        if (is_array($content)) {
            $data = $content;
        } elseif (self::isJson($content)) {
            $data = json_decode((string) $content, true);
        } else {
            // It's already HTML
            // Clean up any stray backticks from markdown-style code
            $content = self::cleanBackticks((string) $content);

            // Sanitize legacy HTML — strip dangerous tags, keep safe formatting
            return strip_tags(
                (string) $content,
                '<p><br><strong><em><u><s><a><ul><ol><li><h1><h2><h3><h4><h5><h6><code><pre><blockquote><img><hr>',
            );
        }

        if (! isset($data['type']) || $data['type'] !== 'doc') {
            return is_array($content) ? '' : (string) $content;
        }

        return self::parseNode($data);
    }

    private static function cleanBackticks(string $html): string
    {
        // Replace markdown-style inline code (backticks) with clean <code> tags
        $html = preg_replace('/`([^`]+)`/i', '<code>$1</code>', $html);

        // Replace triple backticks with clean <pre><code> blocks
        $html = preg_replace('/```(\w*)\n?([^`]*?)```/i', '<pre><code>$2</code></pre>', $html);

        return $html;
    }

    private static function parseNode(array $node): string
    {
        $html = '';

        if (isset($node['content'])) {
            foreach ($node['content'] as $child) {
                $html .= self::renderNode($child);
            }
        }

        return $html;
    }

    private static function renderNode(array $node): string
    {
        $type = $node['type'] ?? 'text';
        $content = '';

        if (isset($node['content'])) {
            foreach ($node['content'] as $child) {
                $content .= self::renderNode($child);
            }
        }

        if ($type === 'text') {
            $text = htmlspecialchars($node['text'] ?? '');
            if (isset($node['marks'])) {
                foreach ($node['marks'] as $mark) {
                    $text = self::applyMark($mark, $text);
                }
            }

            return $text;
        }

        $attributes = self::getAttributes($node['attrs'] ?? []);

        switch ($type) {
            case 'paragraph':
                return "<p class=\"mb-6 text-neutral-600 leading-relaxed text-lg\">{$content}</p>";
            case 'heading':
                $level = $node['attrs']['level'] ?? 1;
                $id = Str::slug(strip_tags($content));
                $headingClass = match ($level) {
                    1 => 'text-3xl md:text-5xl font-bold text-neutral-950 mb-8 mt-16 tracking-tight leading-[1.1]',
                    2 => 'text-2xl md:text-3xl font-bold text-neutral-950 mb-6 mt-12 tracking-tight leading-tight',
                    default => 'text-xl md:text-2xl font-bold text-neutral-900 mb-4 mt-10 tracking-tight',
                };

                return "<h{$level} id=\"{$id}\" class=\"{$headingClass}\">{$content}</h{$level}>";
            case 'bulletList':
                return "<ul class=\"list-disc list-outside mb-8 space-y-3 text-neutral-600 pl-6 text-lg\">{$content}</ul>";
            case 'orderedList':
                return "<ol class=\"list-decimal list-outside mb-8 space-y-3 text-neutral-600 pl-6 text-lg\">{$content}</ol>";
            case 'listItem':
                return "<li class=\"pl-2\">{$content}</li>";
            case 'blockquote':
                return "<blockquote class=\"border-l-2 border-primary/30 pl-8 py-4 my-10 italic text-xl text-neutral-500 bg-neutral-100/50 rounded-r-2xl\">{$content}</blockquote>";
            case 'codeBlock':
                $escapedContent = htmlspecialchars($content);

                return '<pre class="bg-neutral-900 text-neutral-100 rounded-2xl p-6 my-10 text-sm font-mono overflow-x-auto leading-relaxed shadow-sm"><code>'.
                  $escapedContent.
                  '</code></pre>';
            case 'image':
                $src = htmlspecialchars($node['attrs']['src'] ?? '', ENT_QUOTES, 'UTF-8');
                $alt = htmlspecialchars($node['attrs']['alt'] ?? '', ENT_QUOTES, 'UTF-8');

                return "<figure class=\"my-12\"><img src=\"{$src}\" alt=\"{$alt}\" class=\"rounded-3xl border border-neutral-200 bg-neutral-100 mx-auto max-w-full\"></figure>";

            case 'horizontalRule':
                return '<hr>';
            case 'hardBreak':
                return '<br>';
            default:
                return $content;
        }
    }

    private static function applyMark(array $mark, string $text): string
    {
        switch ($mark['type']) {
            case 'bold':
                return "<strong class=\"font-bold text-neutral-950\">{$text}</strong>";
            case 'italic':
                return "<em class=\"italic text-neutral-700\">{$text}</em>";
            case 'underline':
                return "<u class=\"underline decoration-primary/30 decoration-1 underline-offset-2 text-neutral-900\">{$text}</u>";
            case 'strike':
                return "<s class=\"line-through text-neutral-400\">{$text}</s>";
            case 'code':
                return '<code class="bg-neutral-100 text-primary border border-neutral-200 rounded-md px-1.5 py-[2px] text-[0.85em] font-mono font-medium inline-block align-middle">'.
                  $text.
                  '</code>';
            case 'link':
                $href = htmlspecialchars($mark['attrs']['href'] ?? '#');
                $target = $mark['attrs']['target'] ?? '_blank';
                $rel = $target === '_blank' ? 'noopener noreferrer' : '';

                return "<a href=\"{$href}\" class=\"text-neutral-950 underline decoration-primary/50 underline-offset-4 decoration-1 hover:decoration-primary transition-all\" target=\"{$target}\" rel=\"{$rel}\">{$text}</a>";
            default:
                return $text;
        }
    }

    private static function getAttributes(array $attrs): string
    {
        $html = '';
        foreach ($attrs as $key => $value) {
            $html .= " {$key}=\"".htmlspecialchars((string) $value).'"';
        }

        return $html;
    }

    private static function isJson($string): bool
    {
        if (! is_string($string)) {
            return false;
        }
        json_decode($string);

        return json_last_error() === JSON_ERROR_NONE;
    }
}
