<?php

use App\Ai\Agents\ReadingAssistance;
use App\Ai\Tools\ExtractKeywords;
use App\Ai\Tools\ExtractHeaders;
use Laravel\Ai\Tools\Request;

test('reading assistance agent is configured correctly', function () {
    $agent = new ReadingAssistance;

    expect($agent->instructions())->toContain('reading assistant');
    expect($agent->tools())->toBeArray();
    expect($agent->tools()[0])->toBeInstanceOf(ExtractKeywords::class);
    expect($agent->tools()[1])->toBeInstanceOf(ExtractHeaders::class);
});

test('reading assistance agent can be faked and prompted', function () {
    ReadingAssistance::fake([
        'Here is a summary of the article.',
    ]);

    $response = (new ReadingAssistance)->prompt('Summarize this story.');

    expect($response->text)->toBe('Here is a summary of the article.');
    ReadingAssistance::assertPrompted('Summarize this story.');
});

test('extract keywords tool filters stopwords and sorts by frequency', function () {
    $tool = new ExtractKeywords;

    // "the", "over", "is" are stopwords.
    // "fox" appears twice, "quick" appears twice.
    // "lazy" and "dog" appear once.
    $text = 'The quick brown fox jumps over the lazy dog. The fox is quick.';
    $request = new Request(['text' => $text, 'limit' => 3]);
    $result = $tool->handle($request);

    expect($result)->toContain('fox');
    expect($result)->toContain('quick');
});

test('extract headers tool parses html headings correctly', function () {
    $tool = new ExtractHeaders;

    $html = '<h1>Introduction</h1><p>Welcome to the document.</p><h2>Section 1</h2><h3>Detail A</h3>';
    $request = new Request(['content' => $html]);
    $result = $tool->handle($request);

    expect($result)->toBe("# Introduction\n## Section 1\n### Detail A");
});

test('extract headers tool parses tiptap json headings correctly', function () {
    $tool = new ExtractHeaders;

    $tiptapJson = json_encode([
        'type' => 'doc',
        'content' => [
            [
                'type' => 'heading',
                'attrs' => ['level' => 1],
                'content' => [
                    ['type' => 'text', 'text' => 'Tiptap Title']
                ]
            ],
            [
                'type' => 'paragraph',
                'content' => [
                    ['type' => 'text', 'text' => 'Paragraph content.']
                ]
            ],
            [
                'type' => 'heading',
                'attrs' => ['level' => 2],
                'content' => [
                    ['type' => 'text', 'text' => 'Sub Heading']
                ]
            ]
        ]
    ]);

    $request = new Request(['content' => $tiptapJson]);
    $result = $tool->handle($request);

    expect($result)->toBe("# Tiptap Title\n## Sub Heading");
});
