<?php

namespace Behin\SimpleWorkflow\Elements\Builtin;

/**
 * المان «شرط زمان‌دار»: همان رفتار شرط، اما اجرای شاخهٔ بعدی با تأخیر (ایستا یا پویا).
 */
class TimedConditionElement extends ConditionElement
{
    public function key(): string
    {
        return 'timed_condition';
    }

    public function label(): string
    {
        return 'Timed Condition';
    }

    public function bootstrapColor(): string
    {
        return 'info';
    }

    public function diagramColors(): array
    {
        return ['fill' => '#8408f1', 'stroke' => '#6d00d3'];
    }

    public function diagramShape(): array
    {
        return ['{', '}'];
    }

    public function taskSettingFields(): array
    {
        return ['executive_element_id', 'timing_type', 'timing_value', 'timing_key_name'];
    }
}