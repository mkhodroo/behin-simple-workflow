<?php

namespace Behin\SimpleWorkflow\Elements\Builtin;

use Behin\SimpleWorkflow\Elements\AbstractElement;

/**
 * المان «پایان»: پرونده را در وضعیت done می‌بندد و جریان متوقف می‌شود.
 */
class EndElement extends AbstractElement
{
    public function key(): string
    {
        return 'end';
    }

    public function label(): string
    {
        return 'End';
    }

    public function bootstrapColor(): string
    {
        return 'danger';
    }

    public function diagramColors(): array
    {
        return ['fill' => '#f10808', 'stroke' => '#d30000'];
    }

    public function diagramShape(): array
    {
        return ['((', '))'];
    }
}