<?php

namespace Behin\SimpleWorkflow\Elements\Builtin;

use Behin\SimpleWorkflow\Elements\AbstractElement;
use Behin\SimpleWorkflow\Models\Core\Condition;
use Behin\SimpleWorkflow\Models\Core\Task;
use Illuminate\Database\Eloquent\Model;

/**
 * المان «شرط»: بر اساس متغیرهای پرونده تصمیم می‌گیرد که به شاخهٔ true برود یا مسیر معمول.
 */
class ConditionElement extends AbstractElement
{
    public function key(): string
    {
        return 'condition';
    }

    public function label(): string
    {
        return 'Condition';
    }

    public function bootstrapColor(): string
    {
        return 'warning';
    }

    public function diagramColors(): array
    {
        return ['fill' => '#ffc107', 'stroke' => '#d39e00'];
    }

    public function diagramShape(): array
    {
        return ['{', '}'];
    }

    public function executiveModelClass(): ?string
    {
        return Condition::class;
    }

    public function executiveIndexRoute(): ?string
    {
        return 'simpleWorkflow.conditions.index';
    }

    public function executiveEditRoute(?string $executiveElementId): ?string
    {
        return $executiveElementId ? route('simpleWorkflow.conditions.edit', ['condition' => $executiveElementId]) : null;
    }

    public function exportColumns(): array
    {
        return ['id', 'name', 'content', 'next_if_true'];
    }

    public function validateDefinition(Task $task): array
    {
        if ($task->executive_element_id == null) {
            return [trans('fields.don\'t have executive element')];
        }

        return [];
    }

    /**
     * `next_if_true` یک شناسهٔ قدیمی تسک است؛ بعد از ساخته‌شدن تسک‌های جدید باید به شناسهٔ جدید نگاشت شود.
     */
    public function afterImport(?Model $executiveModel, array $executiveData, array $tasksMap): void
    {
        if (!$executiveModel) {
            return;
        }

        $nextIfTrueOld = $executiveData['next_if_true'] ?? null;

        if ($nextIfTrueOld && isset($tasksMap[$nextIfTrueOld])) {
            $executiveModel->next_if_true = $tasksMap[$nextIfTrueOld]->id;
            $executiveModel->save();
        }
    }

    public function taskSettingFields(): array
    {
        return ['executive_element_id'];
    }
}