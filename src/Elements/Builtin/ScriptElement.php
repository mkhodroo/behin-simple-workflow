<?php

namespace Behin\SimpleWorkflow\Elements\Builtin;

use Behin\SimpleWorkflow\Elements\AbstractElement;
use Behin\SimpleWorkflow\Models\Core\Script;
use Behin\SimpleWorkflow\Models\Core\Task;

/**
 * المان «اسکریپت»: بدون کارتابل، فقط یک کلاس PHP را اجرا می‌کند و سپس به تسک بعدی می‌رود.
 */
class ScriptElement extends AbstractElement
{
    public function key(): string
    {
        return 'script';
    }

    public function label(): string
    {
        return 'Script';
    }

    public function bootstrapColor(): string
    {
        return 'success';
    }

    public function diagramColors(): array
    {
        return ['fill' => '#28a745', 'stroke' => '#1e7e34'];
    }

    public function executiveModelClass(): ?string
    {
        return Script::class;
    }

    public function executiveIndexRoute(): ?string
    {
        return 'simpleWorkflow.scripts.index';
    }

    public function executiveEditRoute(?string $executiveElementId): ?string
    {
        return $executiveElementId ? route('simpleWorkflow.scripts.edit', ['script' => $executiveElementId]) : null;
    }

    public function exportColumns(): array
    {
        return ['id', 'name', 'executive_file', 'content'];
    }

    public function validateDefinition(Task $task): array
    {
        if ($task->executive_element_id == null) {
            return [trans('fields.don\'t have executive element')];
        }

        return [];
    }

    public function taskSettingFields(): array
    {
        return ['executive_element_id'];
    }
}