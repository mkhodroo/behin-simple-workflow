<?php

/*
|--------------------------------------------------------------------------
| المان‌های استاندارد فرایند
|--------------------------------------------------------------------------
|
| هر کلید، همان مقدار ستون `type` در جدول `wf_task` است و هر مقدار، کلاس یک المان
| که از `Behin\SimpleWorkflow\Elements\AbstractElement` ارث می‌برد.
| ترتیب آرایه، ترتیب نمایش در dropdown «نوع تسک» است.
|
| المان جدید را می‌توانید با دستور زیر بسازید:
|     php artisan workflow:make-element notification
|
| سپس کلاس تولیدشده را اینجا یا در کانفیگ اپ ثبت کنید:
|
|     // config/workflow.php  (اپ)
|     'elements' => [
|         'notification' => \App\Workflow\Elements\NotificationElement::class,
|     ],
|
| و برای غیرفعال کردن یک المان پیش‌فرض، مقدار آن را `false` بگذارید.
|
*/

return [

    'form' => Behin\SimpleWorkflow\Elements\Builtin\FormElement::class,
    'condition' => Behin\SimpleWorkflow\Elements\Builtin\ConditionElement::class,
    'script' => Behin\SimpleWorkflow\Elements\Builtin\ScriptElement::class,
    'end' => Behin\SimpleWorkflow\Elements\Builtin\EndElement::class,
    'timed_condition' => Behin\SimpleWorkflow\Elements\Builtin\TimedConditionElement::class,

];