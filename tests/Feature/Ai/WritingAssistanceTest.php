<?php

use App\Ai\Agents\WritingAssistance;
use App\Ai\Tools\AnalyzeTextMetrics;
use App\Ai\Tools\SuggestSynonyms;
use Laravel\Ai\Tools\Request;
use Laravel\Ai\Prompts\AgentPrompt;

test('writing assistance agent is configured correctly', function () {
    $agent = new WritingAssistance;

    expect($agent->instructions())->toContain('writing assistant');
    expect($agent->tools())->toBeArray();
    expect($agent->tools()[0])->toBeInstanceOf(AnalyzeTextMetrics::class);
    expect($agent->tools()[1])->toBeInstanceOf(SuggestSynonyms::class);
});

test('writing assistance agent can be faked and prompted', function () {
    WritingAssistance::fake([
        'I am here to help you write better.',
    ]);

    $response = (new WritingAssistance)->prompt('Please improve my essay.');

    expect($response->text)->toBe('I am here to help you write better.');
    WritingAssistance::assertPrompted('Please improve my essay.');
});

test('analyze text metrics tool calculates statistics correctly', function () {
    $tool = new AnalyzeTextMetrics;

    // A simple sentence: "This is a simple sentence. This is another sentence."
    // Words: This (1), is (1), a (1), simple (2), sentence (2) = 7 syllables
    // Words: This (1), is (1), another (3), sentence (2) = 7 syllables
    // Total words = 10
    // Total sentences = 2
    // Total syllables = 14
    $request = new Request(['text' => 'This is a simple sentence. This is another sentence.']);
    $result = $tool->handle($request);

    $data = json_decode($result, true);

    expect($data['word_count'])->toBe(10);
    expect($data['sentence_count'])->toBe(2);
    expect($data['syllable_count'])->toBe(14);
    expect($data['flesch_reading_ease'])->toBeNumeric();
    expect($data['readability_level'])->toBeString();
});

test('suggest synonyms tool returns correct suggestions', function () {
    $tool = new SuggestSynonyms;

    $requestHappy = new Request(['word' => 'happy']);
    $resultHappy = $tool->handle($requestHappy);
    expect($resultHappy)->toContain('cheerful');

    $requestUnknown = new Request(['word' => 'antigravity']);
    $resultUnknown = $tool->handle($requestUnknown);
    expect($resultUnknown)->toContain('No local synonyms found');
});
