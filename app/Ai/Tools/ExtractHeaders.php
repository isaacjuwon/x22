<?php

namespace App\Ai\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class ExtractHeaders implements Tool
{
    /**
     * Get the description of the tool's purpose.
     */
    public function description(): Stringable|string
    {
        return 'This tool extracts headings from Tiptap HTML or JSON content to generate an outline of the text.';
    }

    /**
     * Execute the tool.
     */
    public function handle(Request $request): Stringable|string
    {
        $content = trim($request['content'] ?? '');

        if (empty($content)) {
            return 'The provided content is empty.';
        }

        $headings = [];

        // Check if content is JSON
        $decoded = json_decode($content, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            $headings = $this->extractFromJson($decoded);
        } else {
            // Otherwise, treat as HTML
            $headings = $this->extractFromHtml($content);
        }

        if (empty($headings)) {
            return 'No headings found in the content.';
        }

        // Format outline
        $outline = '';
        foreach ($headings as $heading) {
            $prefix = str_repeat('#', $heading['level']);
            $outline .= "{$prefix} {$heading['text']}\n";
        }

        return trim($outline);
    }

    /**
     * Extract headings recursively from Tiptap JSON node structure.
     */
    private function extractFromJson(array $node): array
    {
        $headings = [];

        if (isset($node['type']) && $node['type'] === 'heading') {
            $level = $node['attrs']['level'] ?? 1;
            $text = $this->collectText($node['content'] ?? []);
            if (!empty($text)) {
                $headings[] = [
                    'level' => (int)$level,
                    'text' => $text,
                ];
            }
        }

        // If it's a doc or has children, recurse
        if (isset($node['content']) && is_array($node['content'])) {
            foreach ($node['content'] as $child) {
                if (is_array($child)) {
                    $headings = array_merge($headings, $this->extractFromJson($child));
                }
            }
        }

        // Handle case where root is a sequential array of nodes instead of a single doc node
        if (!isset($node['type']) && is_array($node)) {
            foreach ($node as $item) {
                if (is_array($item)) {
                    $headings = array_merge($headings, $this->extractFromJson($item));
                }
            }
        }

        return $headings;
    }

    /**
     * Collect text nodes.
     */
    private function collectText(array $content): string
    {
        $text = '';
        foreach ($content as $item) {
            if (isset($item['type']) && $item['type'] === 'text') {
                $text .= $item['text'] ?? '';
            }
        }
        return trim($text);
    }

    /**
     * Extract headings from HTML using regex.
     */
    private function extractFromHtml(string $html): array
    {
        $headings = [];
        preg_match_all('/<h([1-6])\b[^>]*>(.*?)<\/h\1>/is', $html, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $level = (int)$match[1];
            $text = strip_tags($match[2]);
            $text = trim(html_entity_decode($text, ENT_QUOTES, 'UTF-8'));
            if (!empty($text)) {
                $headings[] = [
                    'level' => $level,
                    'text' => $text,
                ];
            }
        }
        return $headings;
    }

    /**
     * Get the tool's schema definition.
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'content' => $schema->string()->required(),
        ];
    }
}
