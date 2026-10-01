<?php

namespace Behin\SimpleWorkflow\Elements;

use Behin\SimpleWorkflow\Models\Core\Task;

/**
 * استاندارد (قرارداد) هر «المان» در فرایند.
 *
 * هر نوع المان (فرم، اسکریپت، شرط، پایان، ...) باید یک پیاده‌سازی از این قرارداد باشد
 * تا در تمام بخش‌های پکیج (لیست ساخت تسک، اعتبارسنجی، دیاگرام، اکسپورت/ایمپورت و اجرای جریان)
 * به‌صورت یکسان شناخته شود و افزودن المان جدید نیاز به ویرایش پراکنده نداشته باشد.
 */
interface ElementContract
{
    /** کلید یکتای المان؛ همان مقداری که در ستون wf_task.type ذخیره می‌شود. */
    public function key(): string;

    /** کلید ترجمهٔ نمایشی المان (مثل trans('Form')). */
    public function label(): string;

    /** رنگ Bootstrap برای نشان (badge) در صفحهٔ ویرایش تسک؛ مثال: primary */
    public function bootstrapColor(): string;

    /** رنگ پس‌زمینه و خط در دیاگرام Mermaid. */
    public function diagramColors(): array;

    /** کلاس CSS این المان در دیاگرام؛ به‌صورت پیش‌فرض برابر key است. */
    public function diagramClass(): string;

    /**
     * شکل نود در دیاگرام Mermaid.
     *
     * @return array{0: string, 1: string} جفت کاراکترهای ابتدا/انتها؛ مثلاً ['(', ')']
     */
    public function diagramShape(): array;

    /** آیا المان یک «المان اجرایی» (فرم/اسکریپت/شرط) دارد که جداگانه ذخیره می‌شود. */
    public function hasExecutiveElement(): bool;

    /** کلاس مدل المان اجرایی؛ برای المان‌های بدون المان اجرایی null برگرداند. */
    public function executiveModelClass(): ?string;

    /** نام روت لیست المان‌های اجرایی (برای پر کردن select در فرم تسک). */
    public function executiveIndexRoute(): ?string;

    /** نام روت ویرایش المان اجرایی؛ اگر المان اجرایی ندارد null برگرداند. */
    public function executiveEditRoute(?string $executiveElementId): ?string;

    /** ستون‌هایی از المان اجرایی که در اکسپورت فرایند ذخیره می‌شوند. */
    public function exportColumns(): array;

    /** ستون‌هایی از المان اجرایی که هنگام ایمپورت از داده‌های اکسپورت خوانده می‌شوند. */
    public function importColumns(): array;

    /**
     * بررسی صحت تعریف تسک از این نوع المان.
     *
     * @return array<int, string> فهرست پیام‌های خطا؛ آرایهٔ خالی یعنی بدون خطا
     */
    public function validateDefinition(Task $task): array;

    /** ستون‌های اضافی wf_task که این المان در فرم ساخت/ویرایش تسک به آن‌ها نیاز دارد. */
    public function taskSettingFields(): array;
}