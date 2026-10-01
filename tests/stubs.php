<?php

/*
 * شیم‌های حداقلی Laravel برای اجرای مستقل تست المان‌ها خارج از اپلیکیشن.
 *
 * این فایل فقط برای تست‌های خط فرمان استفاده می‌شود و در محیط واقعی (که لاراول
 * بارگذاری شده) هرگز require نمی‌شود.
 */

namespace Illuminate\Support {
    if (!class_exists(Arr::class, false)) {
        class Arr
        {
            public static function map(array $array, callable $callback): array
            {
                $keys = array_keys($array);
                $items = array_map($callback, $array, $keys);

                return array_combine($keys, $items);
            }
        }
    }
}

namespace Illuminate\Database\Eloquent {
    if (!class_exists(Model::class, false)) {
        class Model
        {
            public $id = 'fake-id';
        }
    }
}

namespace Behin\SimpleWorkflow\Models\Core {
    if (!class_exists(Task::class, false)) {
        /** جایگزین سبک مدل Task برای تست اعتبارسنجی المان‌ها */
        class Task
        {
            public $type;
            public $executive_element_id;
            public $assignment_type;
            public $actorCount = 0;

            public function actors()
            {
                return new class($this->actorCount)
                {
                    public function __construct(private int $count)
                    {
                    }

                    public function count(): int
                    {
                        return $this->count;
                    }
                };
            }
        }
    }

    // این مدل‌ها فقط به‌صورت ثابت کلاسی (Form::class) استفاده می‌شوند
    class Form
    {
    }
    class Script
    {
    }
    class Condition
    {
    }
}

namespace {
    if (!function_exists('trans')) {
        function trans($key = null, array $replace = [], $locale = null)
        {
            return $key;
        }
    }

    if (!function_exists('app')) {
        function app($abstract = null, array $parameters = [])
        {
            return $abstract;
        }
    }

    if (!function_exists('abort_if')) {
        function abort_if($boolean, $code, $message = '')
        {
            //
        }
    }
}