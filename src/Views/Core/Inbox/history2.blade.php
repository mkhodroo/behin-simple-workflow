@extends('behin-layouts.app')

@section('title')
    تاریخچه (نسخه ۲ - تایم‌لاین)
@endsection

@section('style')
    <style>
        :root {
            --h2-text: #1e293b;
            --h2-muted: #64748b;
            --h2-border: #e2e8f0;
        }

        .history2-page {
            padding: 1.25rem 0 2rem;
        }

        .history2-header {
            margin-bottom: 1.25rem;
            padding: 1.6rem 1.75rem 1.25rem;
            border: 1px solid var(--h2-border);
            border-radius: 22px;
            background:
                radial-gradient(circle at 95% 0%, rgba(37, 99, 235, .10), transparent 30%),
                linear-gradient(180deg, #ffffff 0%, #f9fbff 100%);
            box-shadow: 0 10px 28px rgba(15, 23, 42, .05);
        }

        .history2-header-content {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .history2-title-wrapper {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .history2-title-icon {
            width: 52px;
            height: 52px;
            flex: 0 0 auto;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #2563eb, #60a5fa);
            color: #fff;
            box-shadow: 0 8px 20px rgba(37, 99, 235, .22);
        }

        .history2-title-icon i {
            font-size: 26px;
        }

        .history2-title {
            margin: 0;
            font-size: 1.3rem;
            font-weight: 800;
            color: var(--h2-text);
        }

        .history2-case-number {
            display: inline-block;
            margin-right: .5rem;
            padding: .2rem .6rem;
            border-radius: 8px;
            background: #eff6ff;
            color: #1d4ed8;
            font-size: .8rem;
            font-weight: 700;
            vertical-align: middle;
        }

        .history2-subtitle {
            margin: .35rem 0 0;
            color: var(--h2-muted);
            font-size: .85rem;
        }

        .history2-header-actions {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: .5rem;
        }

        .history2-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
            gap: .75rem;
            margin-top: 1.25rem;
        }

        .history2-stat-box {
            display: flex;
            align-items: center;
            gap: .7rem;
            padding: .7rem .9rem;
            border: 1px solid var(--h2-border);
            border-radius: 14px;
            background: #fff;
        }

        .history2-stat-icon {
            width: 38px;
            height: 38px;
            flex: 0 0 auto;
            border-radius: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
        }

        .history2-stat-icon i {
            font-size: 18px;
        }

        .history2-stat-icon.blue { background: #2563eb; }
        .history2-stat-icon.purple { background: #7c3aed; }
        .history2-stat-icon.amber { background: #d97706; }
        .history2-stat-icon.green { background: #16a34a; }

        .history2-stat-box small {
            display: block;
            color: var(--h2-muted);
            font-size: .72rem;
        }

        .history2-stat-box strong {
            color: var(--h2-text);
            font-size: 1.05rem;
        }

        .history2-stat-range {
            font-size: .85rem !important;
        }
        /* =========================
           Timeline structure
        ========================= */

        .history2-body {
            padding: 1.75rem;
            border: 1px solid var(--h2-border);
            border-radius: 22px;
            background: #fff;
            box-shadow: 0 10px 28px rgba(15, 23, 42, .04);
        }

        .history2-node {
            display: grid;
            grid-template-columns: 96px 34px 1fr;
            align-items: start;
            column-gap: .5rem;
        }

        .history2-node + .history2-node {
            margin-top: 1.1rem;
        }

        .history2-time-col {
            padding-top: .35rem;
            text-align: center;
        }

        .history2-time-box {
            display: inline-flex;
            flex-direction: column;
            padding: .35rem .5rem;
            border-radius: 12px;
            background: #f1f5f9;
            border: 1px solid var(--h2-border);
        }

        .history2-time {
            font-size: .95rem;
            font-weight: 800;
            color: var(--h2-text);
            line-height: 1.2;
        }

        .history2-date {
            font-size: .66rem;
            color: var(--h2-muted);
        }

        /* marker + connecting line */
        .history2-marker-col {
            position: relative;
            align-self: stretch;
            display: flex;
            justify-content: center;
            padding-top: .6rem;
        }

        .history2-marker-col::before {
            content: '';
            position: absolute;
            top: 0;
            bottom: -1.6rem;
            width: 2px;
            background: linear-gradient(180deg, #dbeafe, #e2e8f0);
        }

        .history2-node:last-child .history2-marker-col::before {
            bottom: 100%;
        }

        .history2-marker {
            position: relative;
            z-index: 2;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #fff;
            border: 2px solid #2563eb;
            box-shadow: 0 0 0 4px rgba(37, 99, 235, .12);
        }

        .history2-marker i {
            font-size: 8px;
            color: #2563eb;
        }

        .history2-node.is-open .history2-marker {
            border-color: #d97706;
            box-shadow: 0 0 0 4px rgba(217, 119, 6, .14);
        }

        .history2-node.is-open .history2-marker i {
            color: #d97706;
        }
        .history2-cards-col {
            padding-bottom: .25rem;
        }

        .history2-node-header {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: .45rem;
            margin-bottom: .55rem;
        }

        .history2-node-index {
            font-size: .85rem;
            font-weight: 800;
            color: var(--h2-text);
        }

        .history2-parallel-badge,
        .history2-single-badge,
        .history2-duration-badge {
            display: inline-flex;
            align-items: center;
            gap: .3rem;
            padding: .28rem .6rem;
            border-radius: 9px;
            font-size: .68rem;
            font-weight: 700;
        }

        .history2-parallel-badge {
            background: #ede9fe;
            color: #6d28d9;
        }

        .history2-single-badge {
            background: #f1f5f9;
            color: #64748b;
        }

        .history2-single-badge i {
            font-size: 6px;
        }

        .history2-duration-badge {
            background: #fff7ed;
            color: #c2410c;
        }

        .history2-cards {
            display: block;
        }

        .history2-cards.is-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: .7rem;
        }
        .history2-card {
            position: relative;
            padding: .9rem 1rem;
            border: 1px solid var(--h2-border);
            border-radius: 16px;
            background: #fff;
            box-shadow: 0 3px 12px rgba(15, 23, 42, .04);
            transition: all .2s ease;
            margin-bottom: .7rem;
        }

        .history2-cards.is-grid .history2-card {
            margin-bottom: 0;
        }

        .history2-card::before {
            content: '';
            position: absolute;
            top: 12px;
            bottom: 12px;
            right: 0;
            width: 4px;
            border-radius: 4px 0 0 4px;
            background: var(--tl-color, #2563eb);
        }

        .history2-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 24px rgba(15, 23, 42, .09);
            border-color: #cbd5e1;
        }

        .history2-card.is-open {
            background: #fffbeb;
            border-color: #fde68a;
        }

        .history2-card-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .5rem;
            margin-bottom: .4rem;
        }

        .history2-card-process {
            display: flex;
            align-items: center;
            gap: .35rem;
            color: var(--h2-muted);
            font-size: .73rem;
            min-width: 0;
        }

        .history2-card-process span {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .history2-status {
            flex: 0 0 auto;
            padding: .25rem .6rem;
            border-radius: 9px;
            font-size: .68rem;
            font-weight: 800;
            white-space: nowrap;
        }

        .history2-card-task {
            color: var(--h2-text);
            font-size: .95rem;
            font-weight: 750;
            margin-bottom: .6rem;
        }

        .history2-card-meta {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: .35rem .75rem;
        }

        .history2-meta-item {
            display: flex;
            align-items: center;
            gap: .35rem;
            color: #475569;
            font-size: .74rem;
            min-width: 0;
        }

        .history2-meta-item i {
            color: #94a3b8;
        }

        .history2-meta-item span {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .history2-card-open-note {
            display: inline-flex;
            align-items: center;
            gap: .3rem;
            margin-top: .6rem;
            padding: .22rem .55rem;
            border-radius: 8px;
            background: #fef3c7;
            color: #b45309;
            font-size: .67rem;
            font-weight: 700;
        }

        .history2-card-actions {
            display: flex;
            align-items: center;
            gap: .35rem;
            margin-top: .7rem;
            padding-top: .6rem;
            border-top: 1px dashed var(--h2-border);
        }

        .history2-action {
            width: 32px;
            height: 32px;
            border: 0;
            border-radius: 9px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all .18s ease;
            text-decoration: none !important;
        }

        .history2-action i {
            font-size: 16px;
        }

        .history2-action.primary { background: #eff6ff; color: #2563eb; }
        .history2-action.warning { background: #fffbeb; color: #d97706; }
        .history2-action.info { background: #f0f9ff; color: #0284c7; }
        .history2-action.danger { background: #fef2f2; color: #dc2626; }

        .history2-action:hover {
            transform: translateY(-1px);
            filter: brightness(.96);
        }

        .history2-empty {
            padding: 3.5rem 1rem;
            text-align: center;
            color: var(--h2-muted);
        }

        .history2-empty i {
            font-size: 40px;
            color: #cbd5e1;
        }

        .history2-empty p {
            margin-top: .6rem;
            font-size: .85rem;
        }

        @media (max-width: 767px) {
            .history2-body {
                padding: 1rem;
            }

            .history2-node {
                grid-template-columns: 68px 26px 1fr;
            }

            .history2-time {
                font-size: .8rem;
            }
        }

    </style>
@endsection

@section('content')
    @php
        $statusColors = [
            'new' => '#2563eb',
            'opened' => '#64748b',
            'inProgress' => '#d97706',
            'draft' => '#0284c7',
            'canceled' => '#dc2626',
            'done' => '#16a34a',
            'doneByOther' => '#16a34a',
            'doneBySystem' => '#16a34a',
        ];

        $humanDuration = function ($seconds) {
            if ($seconds === null) {
                return null;
            }
            $seconds = (int) abs($seconds);
            if ($seconds < 60) {
                return $seconds . ' ثانیه';
            }
            $minutes = (int) floor($seconds / 60);
            if ($minutes < 60) {
                return $minutes . ' دقیقه';
            }
            $hours = (int) floor($minutes / 60);
            $restMinutes = $minutes % 60;
            if ($hours < 24) {
                return $hours . ' ساعت' . ($restMinutes ? ' و ' . $restMinutes . ' دقیقه' : '');
            }
            $days = (int) floor($hours / 24);
            $restHours = $hours % 24;
            return $days . ' روز' . ($restHours ? ' و ' . $restHours . ' ساعت' : '');
        };

        $firstRow = $rows->first();
    @endphp
    <div class="container">
        <div class="history2-page">

            {{-- Header --}}
            <div class="history2-header">
                <div class="history2-header-content">

                    <div class="history2-title-wrapper">
                        <div class="history2-title-icon">
                            <i class="fa fa-history"></i>
                        </div>

                        <div>
                            <h1 class="history2-title">
                                تاریخچه انجام کار
                                @if ($caseNumber)
                                    <span class="history2-case-number">پرونده {{ $caseNumber }}</span>
                                @endif
                            </h1>

                            <p class="history2-subtitle">
                                تسک‌هایی که در یک زمان شروع شده‌اند در یک نقطه از تایم‌لاین نمایش داده می‌شوند.
                            </p>
                        </div>
                    </div>

                    <div class="history2-header-actions">
                        <a href="{{ route('simpleWorkflow.inbox.caseHistoryView', ['caseNumber' => $caseNumber]) }}"
                            class="btn btn-sm btn-outline-secondary">
                            <i class="fa fa-list"></i>
                            نسخه ۱ (لیست)
                        </a>

                        @if ($firstRow)
                            <a href="{{ route('simpleWorkflow.inbox.cancel', $firstRow->id) }}"
                                class="btn btn-sm btn-danger">
                                کنسل کردن پرونده
                            </a>

                            <form action="{{ route('simpleWorkflow.inbox.uncanceledCase', $firstRow->case->id) }}"
                                method="POST" style="display:inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-warning">
                                    در دست بررسی کردن پرونده
                                </button>
                            </form>
                        @endif
                    </div>

                </div>

                {{-- Stats --}}
                <div class="history2-stats">
                    <div class="history2-stat-box">
                        <div class="history2-stat-icon blue"><i class="fa fa-tasks"></i></div>
                        <div>
                            <small>تعداد کل تسک‌ها</small>
                            <strong>{{ $rows->count() }}</strong>
                        </div>
                    </div>

                    <div class="history2-stat-box">
                        <div class="history2-stat-icon purple"><i class="fa fa-map-marker"></i></div>
                        <div>
                            <small>نقاط تایم‌لاین</small>
                            <strong>{{ $timeline->count() }}</strong>
                        </div>
                    </div>

                    <div class="history2-stat-box">
                        <div class="history2-stat-icon amber"><i class="fa fa-clone"></i></div>
                        <div>
                            <small>گروه‌های هم‌زمان</small>
                            <strong>{{ $timeline->where('count', '>', 1)->count() }}</strong>
                        </div>
                    </div>

                    <div class="history2-stat-box">
                        <div class="history2-stat-icon green"><i class="fa fa-calendar"></i></div>
                        <div>
                            <small>بازه زمانی</small>
                            <strong class="history2-stat-range" dir="ltr">
                                {{ $timeline->first()['date'] ?? '-' }}
                                تا
                                {{ $timeline->last()['date'] ?? '-' }}
                            </strong>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Timeline --}}
            <div class="history2-body">
                @forelse ($timeline as $index => $node)
                    <div class="history2-node {{ $node['is_open'] ? 'is-open' : '' }}">

                        {{-- Time --}}
                        <div class="history2-time-col">
                            <div class="history2-time-box">
                                <span class="history2-time" dir="ltr">{{ $node['time'] }}</span>
                                <span class="history2-date" dir="ltr">{{ $node['date'] }}</span>
                            </div>
                        </div>

                        {{-- Marker --}}
                        <div class="history2-marker-col">
                            <span class="history2-marker"><i class="fa fa-circle"></i></span>
                        </div>

                        {{-- Cards --}}
                        <div class="history2-cards-col">
                            <div class="history2-node-header">
                                <span class="history2-node-index">گام {{ $index + 1 }}</span>

                                @if ($node['count'] > 1)
                                    <span class="history2-parallel-badge">
                                        <i class="fa fa-clone"></i>
                                        {{ $node['count'] }} تسک به‌صورت هم‌زمان
                                    </span>
                                @else
                                    <span class="history2-single-badge">
                                        <i class="fa fa-circle"></i>
                                        تسک تکی
                                    </span>
                                @endif

                                @if ($node['duration'] !== null)
                                    <span class="history2-duration-badge">
                                        <i class="fa fa-hourglass-half"></i>
                                        {{ $humanDuration($node['duration']) }}
                                    </span>
                                @endif
                            </div>

                            <div class="history2-cards {{ $node['count'] > 1 ? 'is-grid' : '' }}">
                                @foreach ($node['items'] as $row)
                                    @php
                                        $status = $row->status;
                                        $color = $statusColors[$status] ?? '#64748b';
                                        $isOpen = config("workflow.inboxStatus.{$status}.type") === 'open';
                                        $hasDone = $row->updated_at != $row->created_at;
                                    @endphp

                                    <div class="history2-card {{ $isOpen ? 'is-open' : '' }}"
                                        style="--tl-color: {{ $color }}">

                                        <div class="history2-card-top">
                                            <div class="history2-card-process">
                                                <i class="fa fa-sitemap"></i>
                                                <span>{{ $row->task?->process?->name ?? '-' }}</span>
                                            </div>

                                            <span class="history2-status"
                                                style="color: {{ $color }}; background: {{ $color }}1a">
                                                {{ trans('fields.' . $status) }}
                                            </span>
                                        </div>

                                        <div class="history2-card-task">
                                            {{ $row->task?->name ?? '-' }}
                                        </div>

                                        <div class="history2-card-meta">
                                            <div class="history2-meta-item">
                                                <i class="fa fa-folder"></i>
                                                <span>{{ $row->case_name }}</span>
                                            </div>

                                            <div class="history2-meta-item">
                                                <i class="fa fa-user"></i>
                                                <span>{{ getUserInfo($row->actor)?->name ?? '-' }}</span>
                                            </div>

                                            <div class="history2-meta-item" dir="ltr">
                                                <i class="fa fa-play-circle"></i>
                                                <span>{{ toJalali($row->created_at)->format('Y-m-d H:i') }}</span>
                                            </div>

                                            <div class="history2-meta-item" dir="ltr">
                                                <i class="fa fa-check-circle"></i>
                                                <span>
                                                    {{ $hasDone ? toJalali($row->updated_at)->format('Y-m-d H:i') : '—' }}
                                                </span>
                                            </div>
                                        </div>

                                        @if ($isOpen)
                                            <div class="history2-card-open-note">
                                                <i class="fa fa-spinner"></i>
                                                این تسک هنوز باز است
                                            </div>
                                        @endif

                                        @if (access('مدیریت تاریخچه'))
                                            <div class="history2-card-actions">
                                                <a href="{{ route('simpleWorkflow.inbox.edit', $row->id) }}"
                                                    class="history2-action primary" title="{{ trans('fields.Edit') }}">
                                                    <i class="fa fa-pencil"></i>
                                                </a>

                                                <a href="{{ route('simpleWorkflow.inbox.changeStatus', $row->id) }}"
                                                    class="history2-action warning"
                                                    title="{{ trans('fields.Change Status') }}">
                                                    <i class="fa fa-exchange"></i>
                                                </a>

                                                <a href="{{ route('simpleWorkflow.inbox.copy', $row->id) }}"
                                                    class="history2-action info" title="کپی">
                                                    <i class="fa fa-copy"></i>
                                                </a>

                                                <form action="{{ route('simpleWorkflow.inbox.destroy', $row->id) }}"
                                                    method="POST" style="display:inline"
                                                    onsubmit="return confirm('آیا از حذف این ردیف تاریخچه اطمینان دارید؟')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="history2-action danger" title="حذف">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @endif

                                    </div>
                                @endforeach

                            </div>
                        </div>

                    </div>
                @empty
                    <div class="history2-empty">
                        <i class="fa fa-inbox"></i>
                        <p>برای این پرونده هیچ تسکی ثبت نشده است.</p>
                    </div>
                @endforelse
            </div>

        </div>
    </div>
@endsection
