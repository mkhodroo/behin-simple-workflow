<?php

namespace Behin\SimpleWorkflow\Elements;

/**
 * ساخت نام کلاس CSS/کلاس Mermaid به‌شکل «امن».
 *
 * چرا این کلاس لازم است؟
 * -------------------
 * نودهای دیاگرام با `:::className` رنگ‌آمیزی می‌شوند. نام کلاس در گرامر Mermaid
 * فقط با حروف و اعداد ساخته می‌شود و کلمات کلیدی گرامر (مثل `end`، `class`،
 * `style`، `click`، `subgraph`، `default`، `graph`، `direction`) در هر جای
 * نام ظاهر شوند باعث خطای «Syntax error in graph» می‌شوند.
 *
 * برای نمونه المان `end` کلاس `task-end` تولید می‌کرد و چون `end` کلمهٔ کلیدی
 * پایان‌دهندهٔ بلوک `subgraph` است، کل گراف از کار می‌افتاد.
 *
 * راه‌حل: نام کلاس به یک شناسهٔ camelCase با پیشوند ثابت تبدیل می‌شود تا هیچ
 * بخشی از آن نتواند کلمهٔ کلیدی باشد و در CSS هم معتبر بماند.
 */
class DiagramClass
{
    /** پیشوند ثابت؛ تضمین می‌کند نام هیچ‌وقت با کلمهٔ کلیدی گرامر برخورد نکند. */
    protected const PREFIX = 'wf';

    /** نام جایگزین وقتی کلید المان خالی یا فقط عدد باشد. */
    protected const FALLBACK = 'Unknown';

    /** کلمات کلیدی رزرو‌شدهٔ گرامر Mermaid (برای مستندسازی و اطمینان). */
    public const RESERVED_KEYWORDS = [
        'graph',
        'end',
        'subgraph',
        'default',
        'class',
        'classDef',
        'style',
        'click',
        'call',
        'direction',
        'flowchart',
        'linkStyle',
        'interpolate',
        'href',
        'o',
        'x',
    ];

    /**
     * تبدیل کلید المان به نام کلاس امن برای Mermaid و CSS.
     */
    public static function make(?string $key): string
    {
        $words = preg_split('/[^a-zA-Z0-9]+/', (string) $key, -1, PREG_SPLIT_NO_EMPTY) ?: [];

        $pascal = '';
        foreach ($words as $word) {
            $pascal .= ucfirst(strtolower($word));
        }

        if ($pascal === '') {
            $pascal = self::FALLBACK;
        }

        // نام کلاس نمی‌تواند با عدد شروع شود (در CSS و در گرامر معتبر نیست).
        if (preg_match('/^[0-9]/', $pascal)) {
            $pascal = 'X' . $pascal;
        }

        return self::PREFIX . $pascal;
    }
}