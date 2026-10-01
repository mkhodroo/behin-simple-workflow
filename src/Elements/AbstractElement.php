<?php

namespace Behin\SimpleWorkflow\Elements;

use Behin\SimpleWorkflow\Models\Core\Task;
use Illuminate\Database\Eloquent\Model;

/**
 * پیاده‌سازی پایه و پیش‌فرض قرارداد المان.
 *
 * هر المان فقط مواردی را override می‌کند که با رفتار پیش‌فرض فرق دارد.
 */
abstract class AbstractElement implements ElementContract
{
    /** کلید المان باید صریحاً توسط هر المان اعلام شود (همان مقدار ستون wf_task.type). */
    abstract public function key(): string;

    public function label(): string
    {
        return ucfirst(str_replace('_', ' ', $this->key()));
    }

    public function bootstrapColor(): string
    {
        return 'secondary';
    }

    public function diagramColors(): array
    {
        return ['fill' => '#6c757d', 'stroke' => '#4d5459'];
    }

    public function diagramClass(): string
    {
        return 'task-' . str_replace('-', '_', $this->key());
    }

    public function diagramShape(): array
    {
        return ['[', ']'];
    }

    public function hasExecutiveElement(): bool
    {
        return $this->executiveModelClass() !== null;
    }

    public function executiveModelClass(): ?string
    {
        return null;
    }

    public function executiveIndexRoute(): ?string
    {
        return null;
    }

    public function executiveEditRoute(?string $executiveElementId): ?string
    {
        return null;
    }

    public function exportColumns(): array
    {
        return ['id', 'name', 'content'];
    }

    public function importColumns(): array
    {
        return ['name', 'content'];
    }

    /**
 * قلاب اختیاری پس از ساخته‌شدن المان اجرایی در ایمپورت فرایند.
 *
 * برای المان‌هایی که به تسک دیگری ارجاع دارند (مثل next_if_true در شرط) استفاده می‌شود.
 *
 * @param  array<string, Model>  $tasksMap  نگاشت شناسهٔ قدیمی تسک به مدل تسک جدید
 */
    public function afterImport(?Model $executiveModel, array $executiveData, array $tasksMap): void
    {
        // پیش‌فرض: المانی که ارجاعی به تسک دیگری ندارد، کاری لازم نیست.
    }

    public function validateDefinition(Task $task): array
    {
        return [];
    }

    public function taskSettingFields(): array
    {
        return [];
    }

    /**
     * @return array<string, mixed> آرایهٔ رنگ پس‌زمینه/خط به‌همراه نام کلاس CSS برای استفاده در دیاگرام
     */
    public function diagramStyle(): array
    {
        return [
            'class' => $this->diagramClass(),
            'fill' => $this->diagramColors()['fill'],
            'stroke' => $this->diagramColors()['stroke'],
        ];
    }

    public function toArray(): array
    {
        return [
            'key' => $this->key(),
            'label' => $this->label(),
            'bootstrap_color' => $this->bootstrapColor(),
            'diagram' => array_merge($this->diagramStyle(), ['shape' => $this->diagramShape()]),
            'has_executive_element' => $this->hasExecutiveElement(),
            'executive_model' => $this->executiveModelClass(),
            'executive_index_route' => $this->executiveIndexRoute(),
            'setting_fields' => $this->taskSettingFields(),
        ];
    }
}