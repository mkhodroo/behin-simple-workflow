@extends('behin-layouts.app')

@section('title')
    گزارش کارتابل تسک‌ها
@endsection

@section('style')
    <style>
        /* ==========================================================================
           گزارش کارتابل تسک‌ها — تم ارغوانی (کاملا مجزا)
           همه استایل‌ها با پیشوند wfr- نوشته شده‌اند تا با سایر صفحات تداخل نکنند
           ========================================================================== */
        .wfr {
            --wfr-dark: #4c1d95;
            --wfr-main: #6d28d9;
            --wfr-mid: #7c3aed;
            --wfr-light: #a855f7;
            --wfr-lighter: #c4b5fd;
            --wfr-soft: #f5f3ff;
            --wfr-soft-2: #ede9fe;
            --wfr-text: #2e1065;
            --wfr-muted: #7c6bab;
            --wfr-line: #ede9fe;
            direction: rtl;
            text-align: right;
            color: var(--wfr-text);
            padding: 4px 2px 40px;
        }

        .wfr * {
            box-sizing: border-box;
        }

        .wfr a {
            text-decoration: none !important;
        }

        /* ---------- هدر ---------- */
        .wfr-hero {
            position: relative;
            overflow: hidden;
            border-radius: 24px;
            padding: 26px 28px;
            margin-bottom: 22px;
            color: #fff;
            background: linear-gradient(120deg, var(--wfr-dark) 0%, var(--wfr-main) 45%, var(--wfr-light) 100%);
            box-shadow: 0 18px 40px rgba(76, 29, 149, .32);
        }

        .wfr-hero::after {
            content: "";
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at 88% 10%, rgba(255, 255, 255, .28), transparent 45%),
                radial-gradient(circle at 8% 95%, rgba(255, 255, 255, .16), transparent 40%);
            pointer-events: none;
        }

        .wfr-hero-inner {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
        }

        .wfr-hero-icon {
            width: 60px;
            height: 60px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            color: #fff;
            background: rgba(255, 255, 255, .16);
            border: 1px solid rgba(255, 255, 255, .3);
        }

        .wfr-hero h1 {
            margin: 0;
            font-size: 25px;
            font-weight: 700;
            color: #fff;
        }

        .wfr-hero p {
            margin: 6px 0 0;
            font-size: 13.5px;
            opacity: .92;
            line-height: 1.9;
            max-width: 720px;
        }

        .wfr-hero-tags {
            margin-inline-start: auto;
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .wfr-tag {
            background: rgba(255, 255, 255, .18);
            border: 1px solid rgba(255, 255, 255, .28);
            border-radius: 999px;
            padding: 7px 14px;
            font-size: 12.5px;
            white-space: nowrap;
        }

        /* ---------- کارت ---------- */
        .wfr-card {
            background: #fff;
            border: 1px solid var(--wfr-line);
            border-radius: 20px;
            padding: 20px 22px;
            margin-bottom: 20px;
            box-shadow: 0 12px 28px rgba(109, 40, 217, .08);
        }

        .wfr-card-title {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 0 0 16px;
            font-size: 16px;
            font-weight: 700;
            color: var(--wfr-dark);
        }

        .wfr-card-title i {
            color: var(--wfr-mid);
        }

        .wfr-count-pill {
            margin-inline-start: auto;
            background: var(--wfr-soft-2);
            color: var(--wfr-main);
            border-radius: 999px;
            padding: 4px 14px;
            font-size: 12.5px;
            font-weight: 700;
        }
        /* ---------- فیلترها ---------- */
        .wfr-filter {
            display: flex;
            align-items: flex-end;
            gap: 16px;
            flex-wrap: wrap;
        }

        .wfr-field {
            flex: 1 1 260px;
            min-width: 220px;
        }

        .wfr-field label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            color: var(--wfr-muted);
            margin-bottom: 7px;
        }

        .wfr-field select {
            width: 100%;
            height: 46px;
            padding: 0 14px;
            border-radius: 14px;
            border: 1.5px solid var(--wfr-line);
            background: var(--wfr-soft);
            color: var(--wfr-text);
            font-size: 14px;
            outline: none;
            transition: all .2s ease;
            appearance: none;
        }

        .wfr-field select:focus {
            border-color: var(--wfr-light);
            background: #fff;
            box-shadow: 0 0 0 4px rgba(168, 85, 247, .14);
        }

        .wfr-field select:disabled {
            opacity: .65;
            cursor: wait;
        }

        .wfr-hint {
            display: block;
            margin-top: 6px;
            font-size: 12px;
            color: var(--wfr-muted);
        }

        .wfr-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .wfr-btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            height: 46px;
            padding: 0 20px;
            border: none;
            border-radius: 14px;
            font-size: 14px;
            font-weight: 700;
            color: #fff;
            cursor: pointer;
            background: linear-gradient(120deg, var(--wfr-main), var(--wfr-light));
            box-shadow: 0 10px 22px rgba(109, 40, 217, .28);
            transition: transform .18s ease, box-shadow .18s ease;
        }

        .wfr-btn:hover {
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 14px 26px rgba(109, 40, 217, .34);
        }

        .wfr-btn i {
            font-size: 18px;
        }

        .wfr-btn-ghost {
            background: #fff;
            color: var(--wfr-main);
            border: 1.5px solid var(--wfr-soft-2);
            box-shadow: none;
        }

        .wfr-btn-ghost:hover {
            color: var(--wfr-dark);
            background: var(--wfr-soft);
            box-shadow: none;
        }

        /* ---------- کارت‌های آماری ---------- */
        .wfr-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
            gap: 16px;
            margin-bottom: 20px;
        }

        .wfr-stat {
            position: relative;
            overflow: hidden;
            background: #fff;
            border: 1px solid var(--wfr-line);
            border-radius: 20px;
            padding: 20px;
            box-shadow: 0 12px 28px rgba(109, 40, 217, .08);
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .wfr-stat:hover {
            transform: translateY(-4px);
            box-shadow: 0 18px 34px rgba(109, 40, 217, .16);
        }

        .wfr-stat::before {
            content: "";
            position: absolute;
            inset-inline-start: 0;
            top: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--wfr-main), var(--wfr-light));
        }

        .wfr-stat-icon {
            width: 46px;
            height: 46px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            color: var(--wfr-main);
            background: var(--wfr-soft-2);
            margin-bottom: 14px;
        }

        .wfr-stat-value {
            font-size: 27px;
            font-weight: 800;
            color: var(--wfr-dark);
            line-height: 1.3;
        }

        .wfr-stat-value small {
            font-size: 13px;
            font-weight: 700;
            color: var(--wfr-muted);
            margin-inline-start: 4px;
        }

        .wfr-stat-label {
            font-size: 13.5px;
            font-weight: 700;
            color: var(--wfr-muted);
            margin-top: 6px;
        }

        /* ---------- چیپ‌های وضعیت ---------- */
        .wfr-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .wfr-chip {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border-radius: 999px;
            padding: 8px 16px;
            font-size: 13px;
            font-weight: 700;
            background: var(--wfr-soft);
            color: var(--wfr-muted);
            border: 1px solid var(--wfr-line);
        }

        .wfr-chip b {
            font-size: 14px;
        }

        .wfr-chip-open {
            background: linear-gradient(120deg, var(--wfr-main), var(--wfr-light));
            color: #fff;
            border-color: transparent;
            box-shadow: 0 8px 18px rgba(109, 40, 217, .26);
        }

        .wfr-chip-close {
            background: #ecfdf5;
            color: #047857;
            border-color: #d1fae5;
        }

        .wfr-chip-other {
            background: #f8fafc;
            color: #64748b;
            border-color: #e2e8f0;
        }
        /* ---------- لیست کاربران ---------- */
        .wfr-users {
            display: grid;
            gap: 14px;
        }

        .wfr-user {
            background: #fff;
            border: 1px solid var(--wfr-line);
            border-radius: 20px;
            padding: 18px 20px;
            box-shadow: 0 10px 24px rgba(109, 40, 217, .07);
            transition: transform .18s ease, border-color .18s ease, box-shadow .18s ease;
            animation: wfr-fade-up .35s ease both;
        }

        .wfr-user:hover {
            transform: translateY(-3px);
            border-color: var(--wfr-lighter);
            box-shadow: 0 16px 30px rgba(109, 40, 217, .14);
        }

        .wfr-user-head {
            display: flex;
            align-items: center;
            gap: 14px;
            flex-wrap: wrap;
        }

        .wfr-rank {
            width: 34px;
            height: 34px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 800;
            color: var(--wfr-main);
            background: var(--wfr-soft-2);
        }

        .wfr-avatar {
            width: 46px;
            height: 46px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 19px;
            font-weight: 800;
            color: #fff;
            background: linear-gradient(120deg, var(--wfr-main), var(--wfr-light));
            box-shadow: 0 8px 18px rgba(109, 40, 217, .28);
        }

        .wfr-user-meta {
            min-width: 160px;
        }

        .wfr-user-name {
            font-size: 15.5px;
            font-weight: 800;
            color: var(--wfr-dark);
        }

        .wfr-user-sub {
            font-size: 12px;
            color: var(--wfr-muted);
            margin-top: 3px;
        }

        .wfr-user-stats {
            margin-inline-start: auto;
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .wfr-jobs {
            display: inline-flex;
            align-items: baseline;
            gap: 5px;
            background: linear-gradient(120deg, var(--wfr-main), var(--wfr-light));
            color: #fff;
            border-radius: 14px;
            padding: 8px 16px;
            font-size: 12.5px;
            font-weight: 700;
            box-shadow: 0 8px 18px rgba(109, 40, 217, .24);
        }

        .wfr-jobs b {
            font-size: 19px;
        }

        .wfr-share {
            font-size: 12.5px;
            font-weight: 700;
            color: var(--wfr-muted);
            background: var(--wfr-soft);
            border: 1px solid var(--wfr-line);
            border-radius: 999px;
            padding: 6px 13px;
        }

        .wfr-bar {
            height: 9px;
            border-radius: 999px;
            background: var(--wfr-soft-2);
            margin: 14px 0 10px;
            overflow: hidden;
        }

        .wfr-bar span {
            display: block;
            height: 100%;
            border-radius: 999px;
            background: linear-gradient(90deg, var(--wfr-main), var(--wfr-light));
            animation: wfr-grow .7s ease both;
        }

        .wfr-user-foot {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
            font-size: 12.5px;
            color: var(--wfr-muted);
        }

        .wfr-user-foot i {
            color: var(--wfr-light);
            margin-inline-end: 4px;
        }
        /* ---------- جزییات پرونده‌ها ---------- */
        .wfr-details {
            margin-top: 14px;
            border-top: 1px dashed var(--wfr-soft-2);
            padding-top: 12px;
        }

        .wfr-details summary {
            cursor: pointer;
            list-style: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 700;
            color: var(--wfr-main);
            background: var(--wfr-soft);
            border: 1px solid var(--wfr-line);
            border-radius: 12px;
            padding: 8px 14px;
            outline: none;
        }

        .wfr-details summary::-webkit-details-marker {
            display: none;
        }

        .wfr-details summary::before {
            content: "+";
            font-weight: 800;
        }

        .wfr-details[open] summary::before {
            content: "−";
        }

        .wfr-cases {
            list-style: none;
            margin: 12px 0 0;
            padding: 0;
            display: grid;
            gap: 8px;
        }

        .wfr-cases li a {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            background: var(--wfr-soft);
            border: 1px solid var(--wfr-line);
            border-radius: 14px;
            padding: 10px 14px;
            font-size: 13px;
            color: var(--wfr-text);
            transition: all .16s ease;
        }

        .wfr-cases li a:hover {
            background: var(--wfr-soft-2);
            border-color: var(--wfr-lighter);
            transform: translateX(-3px);
        }

        .wfr-case-num {
            font-weight: 800;
            color: var(--wfr-main);
            background: #fff;
            border-radius: 9px;
            padding: 3px 10px;
            border: 1px solid var(--wfr-line);
        }

        .wfr-case-name {
            flex: 1 1 180px;
            font-weight: 600;
        }

        .wfr-case-date {
            font-size: 12px;
            color: var(--wfr-muted);
        }

        .wfr-more {
            margin-top: 10px;
            font-size: 12.5px;
            color: var(--wfr-muted);
        }

        /* ---------- پیام‌ها و حالت خالی ---------- */
        .wfr-note {
            display: flex;
            align-items: center;
            gap: 10px;
            border-radius: 16px;
            padding: 14px 16px;
            font-size: 13.5px;
            font-weight: 600;
            margin-bottom: 16px;
            background: var(--wfr-soft);
            border: 1px solid var(--wfr-line);
            color: var(--wfr-dark);
        }

        .wfr-note-warn {
            background: #fef3c7;
            border-color: #fde68a;
            color: #92400e;
        }

        .wfr-empty {
            text-align: center;
            padding: 50px 20px;
        }

        .wfr-empty-icon {
            width: 84px;
            height: 84px;
            margin: 0 auto 18px;
            border-radius: 26px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            color: var(--wfr-main);
            background: var(--wfr-soft-2);
        }

        .wfr-empty h3 {
            margin: 0 0 8px;
            font-size: 17px;
            font-weight: 800;
            color: var(--wfr-dark);
        }

        .wfr-empty p {
            margin: 0;
            font-size: 13.5px;
            color: var(--wfr-muted);
            line-height: 2;
        }

        /* ---------- انیمیشن ---------- */
        @keyframes wfr-fade-up {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes wfr-grow {
            from {
                width: 0;
            }
        }

        /* ---------- چاپ ---------- */
        @media print {

            .wfr-filter,
            .wfr-hero-tags,
            .main-sidebar,
            .main-header,
            .navbar {
                display: none !important;
            }

            .wfr-hero {
                box-shadow: none;
                border-radius: 12px;
            }

            .wfr-user,
            .wfr-stat {
                box-shadow: none;
                break-inside: avoid;
            }
        }
    </style>
@endsection

@section('content')
    <div class="wfr">

        {{-- ======================= هدر ======================= --}}
        <div class="wfr-hero">
            <div class="wfr-hero-inner">
                <div class="wfr-hero-icon">
                    <i class="fa fa-line-chart" style="font-size:30px"></i>
                </div>
                <div>
                    <h1>گزارش کارتابل تسک‌ها</h1>
                    <p>
                        یک فرایند و سپس یک تسک (فرم) را انتخاب کنید؛ در ادامه تعداد کارهای موجود در آن تسک و
                        لیست کاربرانی که این کارها در کارتابل آن‌ها قرار دارد نمایش داده می‌شود.
                    </p>
                </div>
                <div class="wfr-hero-tags">
                    @if ($task)
                        <span class="wfr-tag">
                            <i class="fa fa-cubes"></i>
                            {{ $task->process ? $task->process->name : 'بدون فرایند' }}
                        </span>
                        <span class="wfr-tag">
                            <i class="fa fa-tasks"></i>
                            {{ $task->name }}
                        </span>
                    @endif
                    <span class="wfr-tag" dir="ltr">{{ \Morilog\Jalali\Jalalian::now()->format('Y-m-d') }}</span>
                </div>
            </div>
        </div>

        {{-- ======================= فیلتر ======================= --}}
        <form class="wfr-card wfr-filter" method="GET" action="{{ route('simpleWorkflow.report.index') }}">
            <div class="wfr-field">
                <label for="wfr-process">فرایند</label>
                <select name="process_id" id="wfr-process">
                    <option value="">— انتخاب فرایند —</option>
                    @foreach ($processes as $process)
                        <option value="{{ $process->id }}" @selected($selectedProcessId == $process->id)>
                            {{ $process->name }}@if ($process->category)
                                ({{ $process->category }})
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="wfr-field">
                <label for="wfr-task">تسک</label>
                <select name="task_id" id="wfr-task" class="select2" data-selected="{{ $task ? $task->id : '' }}">
                    <option value="">— انتخاب تسک —</option>
                    @foreach ($tasks as $item)
                        <option value="{{ $item->id }}" @selected($task && $task->id == $item->id)>
                            {{ $item->name }}@if ($item->is_preview)
                                (پیش‌نمایش)
                            @endif
                        </option>
                    @endforeach
                </select>
                <span class="wfr-hint">
                    @if ($selectedProcessId)
                        {{ number_format($tasks->count()) }} تسک برای این فرایند یافت شد.
                    @else
                        برای مشاهده لیست تسک‌ها ابتدا فرایند را انتخاب کنید.
                    @endif
                </span>
            </div>

            <div class="wfr-actions">
                <button type="submit" class="wfr-btn">
                    <i class="fa fa-search"></i>
                    نمایش گزارش
                </button>
                @if ($task)
                    <a class="wfr-btn wfr-btn-ghost" href="{{ route('simpleWorkflow.report.index') }}">
                        <i class="fa fa-refresh"></i>
                        پاک کردن
                    </a>
                    <button type="button" class="wfr-btn wfr-btn-ghost" onclick="window.print()">
                        <i class="fa fa-print"></i>
                        چاپ گزارش
                    </button>
                @endif
            </div>
        </form>

        @if (!$task)
            {{-- ======================= حالت بدون انتخاب ======================= --}}
            <div class="wfr-card wfr-empty">
                <div class="wfr-empty-icon">
                    <i class="fa fa-hand-pointer-o" style="font-size:40px"></i>
                </div>
                <h3>{{ $selectedProcessId ? 'تسک مورد نظر را انتخاب کنید' : 'برای شروع، یک تسک انتخاب کنید' }}</h3>
                <p>
                    @if ($selectedProcessId)
                        فرایند انتخاب شده است؛ حالا تسک مورد نظر را از فیلتر بالا انتخاب کنید و روی
                        «نمایش گزارش» بزنید.
                    @else
                        ابتدا فرایند و سپس تسک مورد نظر را از فیلتر بالا انتخاب کنید و روی «نمایش گزارش» بزنید
                        تا تعداد کارهای آن تسک و کاربران دارای کار در کارتابل نمایش داده شود.
                    @endif
                </p>
            </div>
        @else


            {{-- ======================= کارت‌های آماری ======================= --}}
            <div class="wfr-stats">
                <div class="wfr-stat">
                    <div class="wfr-stat-icon"><i class="fa fa-inbox" style="font-size:22px"></i>
                    </div>
                    <div class="wfr-stat-value" dir="ltr">{{ number_format($report['open_total']) }}</div>
                    <div class="wfr-stat-label">کار باز در کارتابل کاربران</div>
                </div>
                <div class="wfr-stat">
                    <div class="wfr-stat-icon"><i class="fa fa-users" style="font-size:22px"></i></div>
                    <div class="wfr-stat-value" dir="ltr">{{ number_format($report['user_count']) }}</div>
                    <div class="wfr-stat-label">کاربر دارای کار در این تسک</div>
                </div>
                <div class="wfr-stat">
                    <div class="wfr-stat-icon"><i class="fa fa-hourglass-half" style="font-size:22px"></i>
                    </div>
                    <div class="wfr-stat-value" dir="ltr">{{ $report['oldest_job_jalali'] ?: '—' }}</div>
                    <div class="wfr-stat-label">قدیمی‌ترین کار در انتظار</div>
                </div>
                <div class="wfr-stat">
                    <div class="wfr-stat-icon"><i class="fa fa-archive" style="font-size:22px"></i></div>
                    <div class="wfr-stat-value" dir="ltr">{{ number_format($report['total_inboxes']) }}</div>
                    <div class="wfr-stat-label">کل کارهای این تسک ({{ number_format($report['closed_total']) }} بسته)
                    </div>
                </div>
            </div>

            {{-- ======================= وضعیت‌ها ======================= --}}
            <div class="wfr-card">
                <div class="wfr-card-title">
                    <i class="fa fa-pie-chart" style="font-size:20px"></i>
                    وضعیت کارهای این تسک
                    <span class="wfr-count-pill">مجموع {{ number_format($report['total_inboxes']) }} کار</span>
                </div>
                <div class="wfr-chips">
                    @foreach ($report['statuses'] as $status)
                        @if ($status['count'] > 0)
                            <span
                                class="wfr-chip {{ $status['type'] == 'open' ? 'wfr-chip-open' : ($status['type'] == 'close' ? 'wfr-chip-close' : 'wfr-chip-other') }}">
                                {{ $status['label'] }}
                                <b dir="ltr">{{ number_format($status['count']) }}</b>
                            </span>
                        @endif
                    @endforeach
                </div>
            </div>

            @if ($report['without_actor'] > 0)
                <div class="wfr-note">
                    <i class="fa fa-info-circle"></i>
                    {{ number_format($report['without_actor']) }} کار باز در این تسک بدون کاربر است (کارتابل
                    سیستمی).
                </div>
            @endif

            @if ($report['detail_truncated'])
                <div class="wfr-note wfr-note-warn">
                    <i class="fa fa-exclamation-triangle"></i>
                    تعداد کارهای باز این تسک بیش از {{ number_format($report['detail_limit']) }} مورد است؛ جزییات
                    پرونده‌ها فقط برای بخشی از آن‌ها نمایش داده می‌شود.
                </div>
            @endif
            {{-- ======================= کاربران ======================= --}}
            <div class="wfr-card">
                <div class="wfr-card-title">
                    <i class="fa fa-id-card-o" style="font-size:20px"></i>
                    کاربران دارای کار در کارتابل
                    <span class="wfr-count-pill">{{ number_format($report['user_count']) }} کاربر</span>
                </div>

                @if (count($report['users']) == 0)
                    <div class="wfr-empty">
                        <div class="wfr-empty-icon"><i class="fa fa-check-circle-o" style="font-size:40px"></i></div>
                        <h3>در حال حاضر کار بازی وجود ندارد</h3>
                        <p>
                            هیچ کاری از تسک «{{ $task->name }}» در کارتابل کاربران نیست؛
                            {{ number_format($report['total_inboxes']) }} کار ثبت‌شده این تسک بسته شده‌اند.
                        </p>
                    </div>
                @else
                    <div class="wfr-users">
                        @foreach ($report['users'] as $user)
                            <div class="wfr-user">
                                <div class="wfr-user-head">
                                    <span class="wfr-rank" dir="ltr">{{ $loop->iteration }}</span>
                                    <span class="wfr-avatar">{{ mb_substr($user['name'], 0, 1) }}</span>
                                    <div class="wfr-user-meta">
                                        <div class="wfr-user-name">{{ $user['name'] }}</div>
                                        <div class="wfr-user-sub">
                                            @if ($user['number'])
                                                کد پرسنلی: <span dir="ltr">{{ $user['number'] }}</span>
                                            @else
                                                کاربر سیستم
                                            @endif
                                        </div>
                                    </div>
                                    <div class="wfr-user-stats">
                                        <span class="wfr-jobs">کارتابل: <b
                                                dir="ltr">{{ number_format($user['jobs']) }}</b> کار</span>
                                        <span class="wfr-share">سهم <span
                                                dir="ltr">{{ $user['share'] }}%</span></span>
                                    </div>
                                </div>
                                <div class="wfr-bar">
                                    <span style="width: {{ max($user['share'], 2) }}%"></span>
                                </div>
                                <div class="wfr-user-foot">
                                    <span><i class="fa fa-clock-o"></i> قدیمی‌ترین کار:
                                        <span dir="ltr">{{ $user['first_job_jalali'] }}</span></span>
                                    @if ($user['waiting_days'] !== null)
                                        <span><i class="fa fa-hourglass-half"></i>
                                            {{ number_format($user['waiting_days']) }} روز در انتظار</span>
                                    @endif
                                    <span><i class="fa fa-refresh"></i> آخرین کار:
                                        <span dir="ltr">{{ $user['last_job_jalali'] }}</span></span>
                                </div>
                                @if (count($user['cases']) > 0)
                                    <details class="wfr-details">
                                        <summary>
                                            مشاهده پرونده‌های در کارتابل این کاربر
                                            ({{ number_format(count($user['cases'])) }} مورد@if ($user['more_cases'])
                                                از {{ number_format($user['jobs']) }}
                                            @endif)
                                        </summary>
                                        <ul class="wfr-cases">
                                            @foreach ($user['cases'] as $case)
                                                <li>
                                                    <a href="{{ route('simpleWorkflow.inbox.view', $case['inbox_id']) }}"
                                                        target="_blank">
                                                        <span class="wfr-case-num"
                                                            dir="ltr">{{ $case['case_number'] ?: '—' }}</span>
                                                        <span
                                                            class="wfr-case-name">{{ $case['name'] ?: 'بدون عنوان' }}</span>
                                                        <span class="wfr-case-date"
                                                            dir="ltr">{{ $case['created_at'] }}</span>
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                        @if ($user['more_cases'] > 0)
                                            <div class="wfr-more">
                                                و {{ number_format($user['more_cases']) }} پرونده دیگر در کارتابل این
                                                کاربر وجود دارد…
                                            </div>
                                        @endif
                                    </details>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif
    </div>
@endsection

@section('script')
    <script>
        (function() {
            var processSelect = document.getElementById('wfr-process');
            var taskSelect = document.getElementById('wfr-task');
            var hint = document.querySelector('.wfr-hint');

            if (!processSelect || !taskSelect) {
                return;
            }

            var tasksUrl = "{{ route('simpleWorkflow.report.tasks') }}";

            function fillTasks(tasks, selected) {
                var html = '<option value="">— انتخاب تسک —</option>';
                tasks.forEach(function(task) {
                    var label = task.name + (task.is_preview ? ' (پیش‌نمایش)' : '');
                    html += '<option value="' + task.id + '"' + (task.id === selected ? ' selected' : '') + '>' +
                        label + '</option>';
                });
                taskSelect.innerHTML = html;
                taskSelect.disabled = false;
                if (hint) {
                    hint.textContent = tasks.length + ' تسک برای این فرایند یافت شد.';
                }
            }

            processSelect.addEventListener('change', function() {
                var processId = this.value;

                if (!processId) {
                    fillTasks([], '');
                    if (hint) {
                        hint.textContent = 'برای مشاهده لیست تسک‌ها ابتدا فرایند را انتخاب کنید.';
                    }
                    return;
                }

                taskSelect.disabled = true;

                fetch(tasksUrl + '?process_id=' + encodeURIComponent(processId), {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(function(response) {
                        return response.json();
                    })
                    .then(function(data) {
                        fillTasks(data.tasks || [], '');
                    })
                    .catch(function() {
                        taskSelect.disabled = false;
                        if (hint) {
                            hint.textContent = 'خطا در دریافت لیست تسک‌ها؛ صفحه را دوباره بارگذاری کنید.';
                        }
                    });
            });
        })();
    </script>
@endsection

