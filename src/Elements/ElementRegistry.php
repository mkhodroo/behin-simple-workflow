<?php

namespace Behin\SimpleWorkflow\Elements;

use Behin\SimpleWorkflow\Models\Core\Task;
use Illuminate\Support\Arr;
use InvalidArgumentException;

/**
 * رجیستری المان‌های فرایند.
 *
 * تنها منبع حقیقت برای «چه المان‌هایی وجود دارند» در کل پکیج.
 * المان‌های پیش‌فرض از فایل config/elements.php می‌آیند و اپ می‌تواند المان سفارشی
 * با کلید workflow.elements ثبت کند یا المان پیش‌فرض را با کلید false غیرفعال کند.
 */
class ElementRegistry
{
    /** @var array<string, ElementContract> */
    protected array $elements = [];

    /** @var array<string, class-string> */
    protected array $order = [];

    /**
     * @param  array<string, class-string|false>  $definitions
     */
    public function __construct(array $definitions = [])
    {
        $this->load($definitions);
    }

    /**
     * @param  array<string, class-string|false>  $definitions
     */
    public function load(array $definitions): static
    {
        $this->elements = [];
        $this->order = [];

        foreach ($definitions as $key => $class) {
            if ($class === false || $class === null) {
                continue; // المان غیرفعال شده است
            }
            $element = $this->resolve($class);
            $this->elements[$element->key()] = $element;
            $this->order[] = $key;
        }

        return $this;
    }

    /**
     * ثبت (یا بازنویسی) یک المان در زمان اجرا.
     */
    public function register(ElementContract $element): static
    {
        $this->elements[$element->key()] = $element;
        if (!in_array($element->key(), $this->order, true)) {
            $this->order[] = $element->key();
        }

        return $this;
    }

    public function remove(string $key): static
    {
        unset($this->elements[$key]);
        $this->order = array_values(array_filter($this->order, fn ($k) => $k !== $key));

        return $this;
    }

    public function has(string $key): bool
    {
        return isset($this->elements[$key]);
    }

    public function get(string $key): ?ElementContract
    {
        return $this->elements[$key] ?? null;
    }

    /**
     * المان تسک داده‌شده؛ در صورت نامعتبر بودن نوع، المان پیش‌فرض برگردانده می‌شود.
     */
    public function forTask(?Task $task): ?ElementContract
    {
        if (!$task) {
            return null;
        }

        return $this->get($task->type);
    }

    /**
     * @return array<string, ElementContract>
     */
    public function all(): array
    {
        $result = [];
        foreach ($this->order as $key) {
            if (isset($this->elements[$key])) {
                $result[$key] = $this->elements[$key];
            }
        }

        return $result;
    }

    /**
     * فهرست المان‌ها برای dropdown نوع تسک.
     *
     * @return array<string, string>
     */
    public function options(): array
    {
        $options = [];
        foreach ($this->all() as $key => $element) {
            $options[$key] = trans($element->label());
        }

        return $options;
    }

    /**
     * استایل دیاگرام Mermaid برای همهٔ المان‌ها (برای تولید CSS پویا در ویو).
     *
     * @return array<string, array{class: string, fill: string, stroke: string}>
     */
    public function diagramStyles(): array
    {
        $styles = [];
        foreach ($this->all() as $element) {
            $style = $element->diagramStyle();
            $styles[$style['class']] = $style;
        }

        return $styles;
    }

    /**
     * ستون‌های مجاز فرم ساخت/ویرایش تسک برای یک نوع المان.
     *
     * @return array<int, string>
     */
    public function settingFieldsFor(?string $key): array
    {
        $element = $key ? $this->get($key) : null;

        return $element ? $element->taskSettingFields() : [];
    }

    /**
     * بررسی صحت تعریف یک تسک بر اساس المان آن.
     *
     * @return array<int, string>
     */
    public function validate(Task $task): array
    {
        return $this->forTask($task)?->validateDefinition($task) ?? [];
    }

    /**
     * @return array<int, string>
     */
    public function keys(): array
    {
        return array_keys($this->all());
    }

    /**
     * @param  class-string  $class
     */
    protected function resolve(string $class): ElementContract
    {
        $element = is_subclass_of($class, ElementContract::class) ? new $class() : app($class);

        if (!$element instanceof ElementContract) {
            throw new InvalidArgumentException(
                "Element [{$class}] must implement " . ElementContract::class
            );
        }

        return $element;
    }

    public static function make(array $definitions = []): static
    {
        return new static($definitions);
    }

    public function toArray(): array
    {
        return Arr::map($this->all(), fn (ElementContract $element) => $element->toArray());
    }
}