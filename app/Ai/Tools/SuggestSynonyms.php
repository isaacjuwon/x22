<?php

namespace App\Ai\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;
use Stringable;

class SuggestSynonyms implements Tool
{
    /**
     * Get the description of the tool's purpose.
     */
    public function description(): Stringable|string
    {
        return 'This tool suggests synonyms for a specific word or phrase.';
    }

    /**
     * Execute the tool.
     */
    public function handle(Request $request): Stringable|string
    {
        $word = trim($request['word'] ?? '');

        if (empty($word)) {
            return 'Please provide a valid word.';
        }

        $synonyms = [
            'happy' => ['cheerful', 'delighted', 'joyful', 'content', 'glad'],
            'sad' => ['gloomy', 'depressed', 'melancholy', 'unhappy', 'sorrowful'],
            'smart' => ['intelligent', 'clever', 'bright', 'wise', 'sharp'],
            'fast' => ['quick', 'rapid', 'swift', 'speedy', 'brisk'],
            'slow' => ['sluggish', 'leisurely', 'unhurried', 'gradual'],
            'beautiful' => ['lovely', 'gorgeous', 'pretty', 'stunning', 'attractive'],
            'ugly' => ['unsightly', 'hideous', 'unappealing', 'plain'],
            'big' => ['large', 'huge', 'massive', 'giant', 'enormous'],
            'small' => ['tiny', 'little', 'miniature', 'petite', 'microscopic'],
            'good' => ['excellent', 'great', 'fine', 'satisfactory', 'superb'],
            'bad' => ['terrible', 'awful', 'poor', 'unacceptable', 'dreadful'],
            'easy' => ['simple', 'effortless', 'painless', 'straightforward'],
            'hard' => ['difficult', 'challenging', 'tough', 'demanding'],
            'write' => ['compose', 'draft', 'author', 'pen', 'inscribe'],
            'read' => ['peruse', 'scan', 'study', 'interpret'],
            'improve' => ['enhance', 'better', 'ameliorate', 'upgrade', 'refine'],
            'change' => ['modify', 'alter', 'transform', 'vary', 'adjust'],
            'help' => ['assist', 'aid', 'support', 'guide', 'succor'],
            'make' => ['create', 'produce', 'generate', 'craft', 'build'],
            'use' => ['utilize', 'employ', 'apply', 'exploit', 'exert'],
        ];

        $wordLower = mb_strtolower($word);

        if (array_key_exists($wordLower, $synonyms)) {
            return implode(', ', $synonyms[$wordLower]);
        }

        return "No local synonyms found for '{$word}'. Feel free to suggest synonyms using your general knowledge.";
    }

    /**
     * Get the tool's schema definition.
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'word' => $schema->string()->required(),
        ];
    }
}
