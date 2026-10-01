<?php

namespace Behin\SimpleWorkflow\Elements\Builtin;

use Behin\SimpleWorkflow\Elements\AbstractElement;
use Behin\SimpleWorkflow\Models\Core\Form;
use Behin\SimpleWorkflow\Models\Core\Task;

/**
 * المان «فرم»: کار را در کارتابل کاربران قرار می‌دهد.
 */
class FormElement extends AbstractElement
{
    public function key(): string
    {
        return 'form';
    }

    public function label(): string
    {
        return 'Form';
    }

    public function bootstrapColor(): string
    {
        return 'primary';
    }

    public function diagramColors(): array
    {
        return ['fill' => '#007bff', 'stroke' => '#0056b3'];
    }

    public function diagramShape(): array
    {
        return ['(', ')'];
    }

    public function executiveModelClass(): ?string
    {
        return Form::class;
    }

    public function executiveIndexRoute(): ?string
    {
        return 'simpleWorkflow.form.index';
    }

    public function executiveEditRoute(?string $executiveElementId): ?string
    {
        return $executiveElementId ? route('simpleWorkflow.form.edit', ['id' => $executiveElementId]) : null;
    }

    public function exportColumns(): array
    {
        return ['id', 'name', 'executive_file', 'content'];
    }

    public function validateDefinition(Task $task): array
    {
        $errors = [];

        if ($task->actors()->count() == 0 && $task->assignment_type != 'public') {
            $errors[] = trans('fields.don\'t have actor');
        }

        if ($task->assignment_type == null) {
            $errors[] = trans('fields.don\'t have assignment type');
        }

        return $errors;
    }

    public function taskSettingFields(): array
    {
        return ['executive_element_id', 'assignment_type'];
    }
}