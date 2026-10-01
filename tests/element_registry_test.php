<?php
/*
 * تست مستقل رجیستری و المان‌های پکیج (بدون بوت کامل اپلیکیشن)
 *
 * اجرا:  php tests/element_registry_test.php
 *
 * کلاس‌های واقعی src/Elements بارگذاری می‌شوند و رفتار رجیستری، بارگذاری
 * المان‌های غیرفعال و مقادیر پیش‌فرض المان‌ها بررسی می‌شود.
 */

$failures = 0;

function check(string $title, $actual, $expected): void
{
    global $failures;
    $ok = $actual === $expected;
    if (!$ok) {
        $failures++;
    }
    echo ($ok ? '[PASS] ' : '[FAIL] ') . $title . PHP_EOL;
    if (!$ok) {
        echo '        expected: ' . json_encode($expected) . PHP_EOL;
        echo '        actual:   ' . json_encode($actual) . PHP_EOL;
    }
}

require __DIR__ . '/stubs.php';

$root = dirname(__DIR__) . '/src';

require $root . '/Elements/ElementContract.php';
require $root . '/Elements/AbstractElement.php';
require $root . '/Elements/ElementRegistry.php';
require $root . '/Elements/Builtin/FormElement.php';
require $root . '/Elements/Builtin/ScriptElement.php';
require $root . '/Elements/Builtin/ConditionElement.php';
require $root . '/Elements/Builtin/EndElement.php';
require $root . '/Elements/Builtin/TimedConditionElement.php';

use Behin\SimpleWorkflow\Elements\Builtin\EndElement;
use Behin\SimpleWorkflow\Elements\Builtin\FormElement;
use Behin\SimpleWorkflow\Elements\ElementRegistry;
use Behin\SimpleWorkflow\Models\Core\Form;
use Behin\SimpleWorkflow\Models\Core\Task;

$definitions = require $root . '/config/elements.php';

$registry = ElementRegistry::make($definitions);
// --- بارگذاری و شناسایی ---
check('المان‌های پیش‌فرض همگی ثبت شدند', array_keys($registry->all()), array_keys($definitions));
check('المان form شناسایی شد', $registry->has('form'), true);
check('المان timed_condition شناسایی شد', $registry->has('timed_condition'), true);
check('المان ناشناخته ثبت نشده', $registry->has('unknown_element'), false);

// --- گزینه‌های dropdown ---
check('گزینه‌های dropdown با ترجمه ساخته می‌شوند', $registry->options(), [
    'form' => 'Form',
    'condition' => 'Condition',
    'script' => 'Script',
    'end' => 'End',
    'timed_condition' => 'Timed Condition',
]);

// --- رنگ، کلاس و شکل دیاگرام ---
check('رنگ bootstrap المان فرم', $registry->get('form')->bootstrapColor(), 'primary');
check('رنگ bootstrap المان پایان', $registry->get('end')->bootstrapColor(), 'danger');
check('رنگ bootstrap المان شرط زمان‌دار', $registry->get('timed_condition')->bootstrapColor(), 'info');
check('کلاس CSS دیاگرام فرم', $registry->get('form')->diagramClass(), 'task-form');
check('کلاس CSS دیاگرام شرط زمان‌دار', $registry->get('timed_condition')->diagramClass(), 'task-timed_condition');
check('شکل نود فرم در دیاگرام', $registry->get('form')->diagramShape(), ['(', ')']);
check('شکل نود شرط در دیاگرام', $registry->get('condition')->diagramShape(), ['{', '}']);
check('شکل نود پایان در دیاگرام', $registry->get('end')->diagramShape(), ['((', '))']);
check('رنگ‌های دیاگرام اسکریپت', $registry->get('script')->diagramColors(), ['fill' => '#28a745', 'stroke' => '#1e7e34']);

// --- المان اجرایی ---
check('المان فرم المان اجرایی دارد', $registry->get('form')->hasExecutiveElement(), true);
check('مدل المان اجرایی فرم', $registry->get('form')->executiveModelClass(), Form::class);
check('المان پایان المان اجرایی ندارد', $registry->get('end')->hasExecutiveElement(), false);
check('المان پایان مدل اجرایی ندارد', $registry->get('end')->executiveModelClass(), null);

// --- ستون‌های اکسپورت/ایمپورت ---
check('ستون‌های اکسپورت فرم', $registry->get('form')->exportColumns(), ['id', 'name', 'executive_file', 'content']);
check('ستون‌های اکسپورت شرط', $registry->get('condition')->exportColumns(), ['id', 'name', 'content', 'next_if_true']);
check('ستون‌های ایمپورت شرط next_if_true را وارد نمی‌کند', $registry->get('condition')->importColumns(), ['name', 'content']);

// --- ستون‌های تنظیمات ---
check('فیلدهای تنظیمات فرم', $registry->settingFieldsFor('form'), ['executive_element_id', 'assignment_type']);
check('فیلدهای تنظیمات شرط زمان‌دار', $registry->settingFieldsFor('timed_condition'), ['executive_element_id', 'timing_type', 'timing_value', 'timing_key_name']);
check('فیلدهای تنظیمات المان ناشناخته خالی است', $registry->settingFieldsFor('unknown_element'), []);
check('فیلدهای تنظیمات المان پایان خالی است', $registry->settingFieldsFor('end'), []);

// --- اعتبارسنجی ---
$formTask = new Task();
$formTask->type = 'form';
$formTask->assignment_type = 'normal';
$formTask->actorCount = 0;
check('فرم بدون کاربر خطا دارد', count($registry->validate($formTask)), 1);

$formTask->actorCount = 2;
check('فرم با کاربر و نوع تخصیص خطا ندارد', $registry->validate($formTask), []);

$formTask->assignment_type = null;
check('فرم بدون نوع تخصیص خطا دارد', $registry->validate($formTask), ["fields.don't have assignment type"]);

$scriptTask = new Task();
$scriptTask->type = 'script';
$scriptTask->executive_element_id = null;
check('اسکریپت بدون المان اجرایی خطا دارد', $registry->validate($scriptTask), ["fields.don't have executive element"]);

$scriptTask->executive_element_id = 'abc';
check('اسکریپت با المان اجرایی خطا ندارد', $registry->validate($scriptTask), []);

$endTask = new Task();
$endTask->type = 'end';
check('المان پایان خطا ندارد', $registry->validate($endTask), []);

// --- ثبت و حذف در زمان اجرا ---
$registry->register(new FormElement());
check('ثبت دستی المان در رجیستری', $registry->has('form'), true);
$registry->remove('form');
check('حذف المان از رجیستری', $registry->has('form'), false);

// --- المان غیرفعال با مقدار false ---
$disabled = ElementRegistry::make([
    'form' => FormElement::class,
    'end' => EndElement::class,
    'off_element' => false,
]);
check('المان با مقدار false بارگذاری نمی‌شود', $disabled->has('off_element'), false);
check('سایر المان‌ها بارگذاری شدند', array_keys($disabled->all()), ['form', 'end']);

// --- خروجی توصیفی ---
$array = $disabled->toArray();
check('خروجی toArray شامل کلید المان است', isset($array['form']), true);
check('خروجی toArray شامل شکل دیاگرام است', $array['form']['diagram']['shape'], ['(', ')']);
check('خروجی toArray شامل رنگ bootstrap است', $array['form']['bootstrap_color'], 'primary');

echo PHP_EOL . ($failures === 0 ? 'همهٔ تست‌ها موفق بود.' : "$failures تست ناموفق.") . PHP_EOL;
exit($failures === 0 ? 0 : 1);