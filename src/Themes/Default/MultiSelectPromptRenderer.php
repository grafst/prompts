<?php

namespace Laravel\Prompts\Themes\Default;

use Laravel\Prompts\Concerns\Colors;
use Laravel\Prompts\MultiSelectPrompt;

class MultiSelectPromptRenderer
{
    use Colors;
    use Concerns\DrawsBoxes;

    /**
     * Render the multiselect prompt.
     */
    public function __invoke(MultiSelectPrompt $prompt): string
    {
        return match ($prompt->state) {
            'submit' => <<<EOT

                {$this->box($this->dim($prompt->message), $this->dim($this->renderSelectedOptions($prompt)))}

                EOT,

            'cancel' => <<<EOT

                {$this->box($prompt->message, $this->strikethrough($this->dim($this->renderSelectedOptions($prompt))), 'red')}
                {$this->red('  ⚠ Cancelled.')}

                EOT,

            'error' => <<<EOT

                {$this->box($prompt->message, $this->renderOptions($prompt), 'yellow')}
                {$this->yellow("  ⚠ {$prompt->error}")}

                EOT,

            default => <<<EOT

                {$this->box($this->cyan($prompt->message), $this->renderOptions($prompt))}


                EOT,
        };
    }

    /**
     * Render the options.
     */
    protected function renderOptions(MultiSelectPrompt $prompt): string
    {
        return collect($prompt->options)
            ->values()
            ->map(function ($label, $i) use ($prompt) {
                $selected = in_array(array_keys($prompt->options)[$i], $prompt->value());
                $active = $prompt->highlighted === $i;

                return match (true) {
                    $active && $selected => "› {$this->green('◼')} {$label} ",
                    $active => "› ◻ {$label} ",
                    $selected => "  {$this->green('◼')} {$this->dim($label)} ",
                    default => "  {$this->dim('◻')} {$this->dim($label)}",
                };
            })
            ->implode(PHP_EOL);
    }

    /**
     * Render the selected options.
     */
    protected function renderSelectedOptions(MultiSelectPrompt $prompt): string
    {
        return collect($prompt->options)
            ->filter(fn ($label, $key) => in_array($key, $prompt->values))
            ->implode(', ');
    }
}