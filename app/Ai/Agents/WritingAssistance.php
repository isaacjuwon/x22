<?php

namespace App\Ai\Agents;

use App\Ai\Tools\AnalyzeTextMetrics;
use App\Ai\Tools\SuggestSynonyms;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Promptable;
use Stringable;

class WritingAssistance implements Agent, HasTools
{
    use Promptable;

    /**
     * Get the instructions that the agent should follow.
     */
    public function instructions(): Stringable|string
    {
        return 'You are a professional writing assistant. You help the user draft, refine, format, improve, or check their text. Use the provided tools (AnalyzeTextMetrics, SuggestSynonyms) to gather statistics, readability reports, or alternative word suggestions when asked.';
    }

    /**
     * Get the tools available to the agent.
     *
     * @return iterable<\Laravel\Ai\Contracts\Tool>
     */
    public function tools(): iterable
    {
        return [
            new AnalyzeTextMetrics,
            new SuggestSynonyms,
        ];
    }
}
