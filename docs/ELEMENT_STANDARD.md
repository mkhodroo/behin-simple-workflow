# استاندارد المان‌های فرایند (Element Standard)

این سند مشخص می‌کند «المان» در این پکیج چیست و هر المان جدید باید چه ساختاری داشته باشد.

> **المان** = هر نوع گره (Task) در نمودار فرایند. مقدار `type` در جدول `wf_task` نام المان است.

## چرا رجیستری؟

پیش از این بازآرایی، فهرست المان‌ها در ۶ جای مختلف hard-code شده بود: dropdown ساخت تسک، رنگ و شکل
دیاگرام، اعتبارسنجی، مدل «المان اجرایی»، و اکسپورت/ایمپورت فرایند. افزودن یک المان جدید یعنی
ویرایش همهٔ آن فایل‌ها و هماهنگ‌نگه‌داشتنشان.

حالا یک منبع واحد وجود دارد: **رجیستری المان‌ها**. هر جایی که به «چه المان‌هایی داریم» نیاز است،
از رجیستری می‌پرسد. افزودن المان جدید فقط یک فایل جدید + یک خط ثبت است.

## ساختار پوشه

```
src/Elements/
├── ElementContract.php          ← قرارداد (interface)؛ هر المان باید این را پیاده‌سازی کند
├── AbstractElement.php          ← پیاده‌سازی پیش‌فرض؛ المان فقط تفاوت‌ها را override می‌کند
├── ElementRegistry.php          ← رجیستری: بارگذاری، جستجو، ثبت/حذف در زمان اجرا
├── Builtin/                     ← المان‌های پیش‌فرض پکیج
│   ├── FormElement.php
│   ├── ScriptElement.php
│   ├── ConditionElement.php
│   ├── TimedConditionElement.php
│   └── EndElement.php
└── Custom/                      ← المان‌های افزوده‌شده توسط اپ (با دستور ساخته می‌شوند)

src/config/elements.php          ← فهرست المان‌های پیش‌فرض (کلید => کلاس)
```

## المان‌های پیش‌فرض

| key | رنگ | شکل دیاگرام | المان اجرایی | فیلدهای اختصاصی |
| --- | --- | --- | --- | --- |
| `form` | `primary` (آبی) | `( )` | `wf_forms` | `executive_element_id`, `assignment_type` |
| `script` | `success` (سبز) | `[ ]` | `wf_scripts` | `executive_element_id` |
| `condition` | `warning` (زرد) | `{ }` | `wf_conditions` | `executive_element_id` |
| `timed_condition` | `info` | `{ }` | `wf_conditions` | `executive_element_id`, `timing_type`, `timing_value`, `timing_key_name` |
| `end` | `danger` (قرمز) | `(( ))` | ندارد | ندارد |
## قرارداد (متدها)

| متد | نوع | کار | پیش‌فرض در `AbstractElement` |
| --- | --- | --- | --- |
| `key()` | `string` | **الزامی** — مقدار ستون `wf_task.type` | ندارد (باید پیاده شود) |
| `label()` | `string` | کلید ترجمهٔ نمایشی | نام انسانی از روی کلید |
| `bootstrapColor()` | `string` | رنگ Badge در صفحهٔ ویرایش تسک | `secondary` |
| `diagramColors()` | `array` | `fill` و `stroke` دیاگرام Mermaid | خاکستری |
| `diagramClass()` | `string` | کلاس CSS نود در دیاگرام | `task-{key}` |
| `diagramShape()` | `array` | جفت کاراکتر ابتدا/انتها | `['[', ']']` |
| `executiveModelClass()` | `?string` | کلاس مدل «المان اجرایی» | `null` |
| `hasExecutiveElement()` | `bool` | آیا المان اجرایی دارد | خودکار از مدل |
| `executiveIndexRoute()` | `?string` | روت لیست المان‌های اجرایی | `null` |
| `executiveEditRoute(?string $id)` | `?string` | روت ویرایش المان اجرایی | `null` |
| `exportColumns()` | `array` | ستون‌های اکسپورت فرایند | `['id', 'name', 'content']` |
| `importColumns()` | `array` | ستون‌های ایمپورت فرایند | `['name', 'content']` |
| `afterImport(...)` | `void` | قلاب نگاشت ارجاع‌ها پس از ایمپورت | خالی |
| `validateDefinition(Task $task)` | `array` | پیام‌های خطای تعریف تسک | خالی (بدون خطا) |
| `taskSettingFields()` | `array` | ستون‌های `wf_task` که این المان در فرم ویرایش به آن‌ها نیاز دارد | خالی |

## افزودن یک المان جدید (گام‌به‌گام)

### ۱. ساخت فایل

```bash
php artisan workflow:make-element notification
```

این دستور فایل `src/Elements/Custom/NotificationElement.php` را با یک استامبل کامل و کامنت‌گذاری‌شده می‌سازد.

### ۲. ویرایش کلاس

```php
namespace Behin\SimpleWorkflow\Elements\Custom;

use Behin\SimpleWorkflow\Elements\AbstractElement;
use Behin\SimpleWorkflow\Models\Core\Task;

class NotificationElement extends AbstractElement
{
    public function key(): string
    {
        return 'notification';            // مقدار ستون wf_task.type
    }

    public function label(): string
    {
        return 'Notification';            // کلید ترجمه
    }

    public function bootstrapColor(): string
    {
        return 'teal';
    }

    public function diagramColors(): array
    {
        return ['fill' => '#20c997', 'stroke' => '#0f8a6a'];
    }

    public function validateDefinition(Task $task): array
    {
        return $task->message ? [] : [trans('fields.Message is required')];
    }
}
```

هر متدی که لازم نیست را حذف کنید؛ پیش‌فرض‌های `AbstractElement` کافی‌اند.

### ۳. ثبت در رجیستری

کانفیگ `config/workflow.php` اپ:

```php
return [
    'elements' => [
        'notification' => \App\Workflow\Elements\NotificationElement::class,
    ],
];
```

یا مستقیم در `src/config/elements.php` پکیج. ترتیب آرایه = ترتیب نمایش در dropdown.

### ۴. بررسی

```bash
php artisan workflow:elements
```

جدولی از المان‌های فعال به همراه رنگ، مدل اجرایی، کلاس دیاگرام و فیلدهای اختصاصی چاپ می‌شود.

## غیرفعال کردن یک المان پیش‌فرض

```php
// config/workflow.php
'elements' => [
    'end' => false,   // این المان در dropdown دیده نمی‌شود
],
```

## ثبت در زمان اجرا (مثلاً در ServiceProvider اپ)

```php
use Behin\SimpleWorkflow\Elements\ElementRegistry;

app(ElementRegistry::class)->register(new NotificationElement());
// یا
app(ElementRegistry::class)->remove('end');
```

## استفاده از رجیستری در ویو و کد

```php
// هلپر سراسری
workflowElements()->options();        // ['form' => 'Form', ...]
workflowElements()->get('form');      // FormElement
workflowElements()->has('notification');
workflowElements()->keys();

// روی مدل تسک
$task->element();                     // المان این تسک یا null
$task->executiveElement();            // مدل المان اجرایی یا null
```

## محدودهٔ فعلی و مرحلهٔ بعد

این نسخه همهٔ **نقاط خواندن** (نمایش، اعتبارسنجی، مدل اجرایی، اکسپورت/ایمپورت) را رجیستری‌محور کرده است.

منطق **اجرای جریان** در `RoutingController::executeNextTask()` هنوز شرطی است (`if ($task->type == 'form')` و ...)،
چون تغییر آن رفتار اجرای پرونده‌ها را جابه‌جا می‌کند و باید جداگانه و با تست رفتاری انجام شود.

برای مهاجرت اجرا در مرحلهٔ بعد، پیشنهاد می‌شود قرارداد به این شکل گسترش یابد:

```php
// در ElementContract
public function execute(Task $task, ElementContext $context): ElementResult;
```

که در آن `ElementContext` اطلاعات پرونده، ایننباک و مسیر بعدی را نگه می‌دارد و `ElementResult` می‌گوید
«ادامه بده»، «توقف کن (break)»، «این تسک بعدی را اجرا کن» یا «پاسخ HTTP برگردان».

## تست

```bash
php tests/element_registry_test.php
```

این تست مستقل از اپلیکیشن اجرا می‌شود و بارگذاری المان‌ها، مقادیر پیش‌فرض، اعتبارسنجی و رفتار
غیرفعال‌سازی (`false`) را بررسی می‌کند.