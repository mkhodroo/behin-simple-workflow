<?php

namespace Behin\SimpleWorkflow\Console\Commands;

use Behin\SimpleWorkflow\Elements\ElementRegistry;
use Illuminate\Console\Command;

class ListElementsCommand extends Command
{
    protected $signature = 'workflow:elements';

    protected $description = 'نمایش المان‌های ثبت‌شده در رجیستری المان‌های فرایند';

    public function handle(): int
    {
        $registry = app(ElementRegistry::class);
        $elements = $registry->all();

        if (count($elements) === 0) {
            $this->warn('هیچ المانی در رجیستری ثبت نشده است.');

            return self::SUCCESS;
        }

        $this->info('المان‌های فعال در رجیستری:');
        $this->newLine();

        $rows = [];
        foreach ($elements as $key => $element) {
            $rows[] = [
                $key,
                trans($element->label()),
                $element->bootstrapColor(),
                $element->hasExecutiveElement() ? class_basename($element->executiveModelClass()) : '-',
                $element->diagramClass(),
                implode(',', $element->diagramShape()),
                implode(',', $element->taskSettingFields()) ?: '-',
            ];
        }

        $this->table(
            ['key', 'label', 'color', 'executive model', 'diagram class', 'shape', 'setting fields'],
            $rows
        );

        $this->newLine();
        $this->line('برای ساخت المان جدید: php artisan workflow:make-element <key>');

        return self::SUCCESS;
    }
}