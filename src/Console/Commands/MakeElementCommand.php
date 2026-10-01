<?php

namespace Behin\SimpleWorkflow\Console\Commands;

use Behin\SimpleWorkflow\Elements\ElementRegistry;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class MakeElementCommand extends Command
{
    protected $signature = 'workflow:make-element
                            {key : کلید المان (مقدار ستون type در جدول wf_task)}
                            {--force : بازنویسی فایل موجود}';

    protected $description = 'ساخت یک المان استاندارد جدید برای فرایند مطابق قرارداد پکیج';

    public function handle(): int
    {
        $key = Str::snake(trim($this->argument('key')));

        if ($key === '') {
            $this->error('کلید المان نمی‌تواند خالی باشد.');

            return self::FAILURE;
        }

        $registry = app(ElementRegistry::class);

        if ($registry->has($key) && !$this->option('force')) {
            $this->error("المان «{$key}» از قبل ثبت شده است. برای بازنویسی از --force استفاده کنید.");

            return self::FAILURE;
        }

        $className = Str::studly($key) . 'Element';
        $path = $this->elementsPath() . '/' . $className . '.php';

        if (file_exists($path) && !$this->option('force')) {
            $this->error("فایل {$path} از قبل وجود دارد. برای بازنویسی از --force استفاده کنید.");

            return self::FAILURE;
        }

        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        file_put_contents($path, $this->stub($className, $key));

        $this->info("المان ساخته شد: {$path}");
        $this->newLine();
        $this->line('برای فعال‌سازی، یکی از این دو کار را انجام دهید:');
        $this->line("  1) کلید «{$key}» را به آرایهٔ 'elements' در config/workflow.php اپ اضافه کنید:");
        $this->line("     'elements' => ['{$key}' => \\{$className}::class],");
        $this->line('  2) یا کلاس را مستقیماً در src/config/elements.php پکیج ثبت کنید.');
        $this->newLine();
        $this->line('سپس بررسی کنید: php artisan workflow:elements');

        return self::SUCCESS;
    }

    protected function elementsPath(): string
    {
        return dirname(__DIR__, 2) . '/Elements/Custom';
    }

    protected function stub(string $className, string $key): string
    {
        $label = Str::title(str_replace('_', ' ', $key));

        return <<<PHP
        <?php

        namespace Behin\SimpleWorkflow\Elements\Custom;

        use Behin\SimpleWorkflow\Elements\AbstractElement;
        use Behin\SimpleWorkflow\Models\Core\Task;

        /**
         * المان «{$label}».
         *
         * نمونهٔ مرجع برای افزودن المان جدید. هر متدی که لازم نیست را حذف کنید؛
         * مقادیر پیش‌فرض AbstractElement کافی است.
         */
        class {$className} extends AbstractElement
        {
            /** کلید المان؛ همان مقداری که در ستون wf_task.type ذخیره می‌شود. */
            public function key(): string
            {
                return '{$key}';
            }

            /** کلید ترجمهٔ نمایشی. */
            public function label(): string
            {
                return '{$label}';
            }

            /** رنگ Bootstrap نشان در صفحهٔ ویرایش تسک. */
            public function bootstrapColor(): string
            {
                return 'secondary';
            }

            /** رنگ پس‌زمینه و خط در دیاگرام Mermaid. */
            public function diagramColors(): array
            {
                return ['fill' => '#6c757d', 'stroke' => '#4d5459'];
            }

            /**
             * آیا المان یک «المان اجرایی» جداگانه دارد؟
             * در صورت true، کلاس مدل آن را در executiveModelClass برگردانید
             * تا در ویوی ویرایش تسک، اکسپورت و ایمپورت هم به‌صورت خودکار کار کند.
             */
            public function executiveModelClass(): ?string
            {
                return null;
            }

            /** روت ویرایش المان اجرایی (در صورت داشتن). */
            public function executiveEditRoute(?string \$executiveElementId): ?string
            {
                return null;
            }

            /** بررسی صحت تعریف تسک؛ هر خطا یک پیام قابل نمایش به کاربر است. */
            public function validateDefinition(Task \$task): array
            {
                \$errors = [];

                // if (\$task->executive_element_id == null) {
                //     \$errors[] = trans('fields.Missing executive element');
                // }

                return \$errors;
            }

            /** ستون‌های اختصاصی این المان در جدول wf_task. */
            public function taskSettingFields(): array
            {
                return [];
            }
        }

        PHP;
    }
}