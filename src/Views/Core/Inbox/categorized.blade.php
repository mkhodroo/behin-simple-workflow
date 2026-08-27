@extends('behin-layouts.app')

@section('style') <style>
:root {
--inbox-primary: #2563eb;
--inbox-primary-light: #eff6ff;
--inbox-primary-soft: #dbeafe;
--inbox-success: #16a34a;
--inbox-success-light: #f0fdf4;
--inbox-warning: #d97706;
--inbox-danger: #dc2626;
--inbox-text: #1e293b;
--inbox-muted: #64748b;
--inbox-border: #e2e8f0;
--inbox-bg: #f8fafc;
}

```
    .categorized-inbox-page {
        padding: 1.25rem 0 2rem;
    }

    /* =========================
       Main Card
    ========================= */

    .categorized-inbox-card {
        border: 1px solid rgba(226, 232, 240, .8);
        border-radius: 24px;
        background: #fff;
        box-shadow:
            0 10px 30px rgba(15, 23, 42, .04),
            0 2px 8px rgba(15, 23, 42, .03);
        overflow: hidden;
    }

    .categorized-inbox-card > .card-body {
        padding: 0;
    }

    /* =========================
       Header
    ========================= */

    .inbox-header {
        padding: 1.75rem 2rem;
        border-bottom: 1px solid var(--inbox-border);
        background:
            radial-gradient(circle at 95% 0%, rgba(37, 99, 235, .08), transparent 28%),
            linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
    }

    .inbox-header-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
    }

    .inbox-title-wrapper {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .inbox-title-icon {
        width: 52px;
        height: 52px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #2563eb, #60a5fa);
        color: #fff;
        box-shadow: 0 8px 20px rgba(37, 99, 235, .2);
    }

    .inbox-title-icon i {
        font-size: 27px;
    }

    .inbox-title {
        margin: 0;
        font-size: 1.35rem;
        font-weight: 800;
        color: var(--inbox-text);
    }

    .inbox-subtitle {
        margin: .35rem 0 0;
        color: var(--inbox-muted);
        font-size: .86rem;
    }

    .inbox-total-box {
        display: flex;
        align-items: center;
        gap: .7rem;
        padding: .65rem 1rem;
        border: 1px solid var(--inbox-border);
        background: #fff;
        border-radius: 14px;
    }

    .inbox-total-box .icon {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--inbox-primary-light);
        color: var(--inbox-primary);
    }

    .inbox-total-box small {
        display: block;
        color: var(--inbox-muted);
        font-size: .7rem;
    }

    .inbox-total-box strong {
        color: var(--inbox-text);
        font-size: 1rem;
    }

    /* =========================
       Category Section
    ========================= */

    .category-section {
        padding: 1.5rem 2rem;
        background: #fff;
        border-bottom: 1px solid var(--inbox-border);
    }

    .section-label {
        display: flex;
        align-items: center;
        gap: .5rem;
        margin-bottom: 1rem;
        color: var(--inbox-text);
        font-weight: 750;
        font-size: .9rem;
    }

    .section-label i {
        color: var(--inbox-primary);
        font-size: 20px;
    }

    .task-category-scroll {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(190px, 1fr));
        gap: .8rem;
    }

    .category-chip {
        position: relative;
        min-height: 70px;
        padding: .85rem 1rem;
        border: 1px solid var(--inbox-border);
        border-radius: 16px;
        background: #fff;
        color: var(--inbox-text);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .75rem;
        cursor: pointer;
        transition: all .2s ease;
        text-align: right;
    }

    .category-chip:hover {
        border-color: #bfdbfe;
        background: #f8fbff;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(37, 99, 235, .08);
    }

    .category-chip.active {
        color: #fff;
        border-color: transparent;
        background: linear-gradient(135deg, #2563eb, #3b82f6);
        box-shadow: 0 10px 24px rgba(37, 99, 235, .2);
    }

    .category-chip.active:hover {
        background: linear-gradient(135deg, #1d4ed8, #2563eb);
    }

    .chip-main {
        display: flex;
        align-items: center;
        gap: .7rem;
        min-width: 0;
    }

    .chip-icon {
        flex: 0 0 auto;
        width: 38px;
        height: 38px;
        border-radius: 11px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: var(--inbox-primary-light);
        color: var(--inbox-primary);
    }

    .category-chip.active .chip-icon {
        background: rgba(255, 255, 255, .18);
        color: #fff;
    }

    .chip-label {
        font-size: .86rem;
        font-weight: 700;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .chip-count {
        flex: 0 0 auto;
        min-width: 30px;
        height: 28px;
        padding: 0 .55rem;
        border-radius: 9px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #f1f5f9;
        color: #475569;
        font-size: .75rem;
        font-weight: 800;
    }

    .category-chip.active .chip-count {
        background: rgba(255, 255, 255, .2);
        color: #fff;
    }

    /* =========================
       Active Filter
    ========================= */

    .active-filter-card {
        margin: 1.5rem 2rem 0;
        padding: 1rem 1.15rem;
        border: 1px solid #bbf7d0;
        border-radius: 14px;
        background: linear-gradient(135deg, #f0fdf4, #f7fee7);
        color: #166534;
    }

    .active-filter-card strong {
        color: #15803d;
        font-size: .9rem;
    }

    .active-filter-card button {
        border: 0;
        background: rgba(255, 255, 255, .65);
        color: #15803d;
        padding: .45rem .75rem;
        border-radius: 9px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: .3rem;
        cursor: pointer;
        transition: all .2s ease;
    }

    .active-filter-card button:hover {
        background: #fff;
        box-shadow: 0 3px 10px rgba(22, 101, 52, .08);
    }

    /* =========================
       Advanced Filter
    ========================= */

    .advanced-filter-wrapper {
        padding: 1.5rem 2rem 0;
    }

    .advanced-filter-card {
        border: 1px solid #dbeafe;
        border-radius: 18px;
        padding: 1.25rem;
        background:
            linear-gradient(135deg, rgba(239, 246, 255, .75), rgba(248, 250, 252, .9));
    }

    .advanced-filter-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .advanced-filter-title {
        display: flex;
        align-items: center;
        gap: .7rem;
    }

    .advanced-filter-icon {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #dbeafe;
        color: #2563eb;
    }

    .advanced-filter-title h3 {
        font-size: .95rem;
        font-weight: 800;
        color: var(--inbox-text);
        margin: 0;
    }

    .advanced-filter-title p {
        font-size: .76rem;
        color: var(--inbox-muted);
        margin: .15rem 0 0;
    }

    .advanced-filter-actions {
        display: flex;
        align-items: center;
        gap: .6rem;
    }

    .match-mode-wrapper {
        display: flex;
        align-items: center;
        gap: .5rem;
        background: #fff;
        border: 1px solid var(--inbox-border);
        border-radius: 11px;
        padding: .25rem .45rem .25rem .7rem;
    }

    .match-mode-wrapper label {
        margin: 0;
        font-size: .72rem;
        color: var(--inbox-muted);
        white-space: nowrap;
    }

    .match-mode-wrapper select {
        border: 0;
        box-shadow: none;
        font-size: .78rem;
        font-weight: 700;
        color: var(--inbox-text);
        background: transparent;
    }

    .advanced-filter-condition.input-group {
        min-height: 48px;
        border: 1px solid #dbeafe;
        border-radius: 13px;
        background: #fff;
        padding: .25rem .35rem;
        gap: .25rem;
        box-shadow: 0 2px 6px rgba(15, 23, 42, .02);
    }

    .advanced-filter-condition .input-group-text {
        width: 38px;
        height: 38px;
        border: 0;
        border-radius: 9px;
        background: #eff6ff;
        color: #2563eb;
        justify-content: center;
    }

    .advanced-filter-condition .form-select,
    .advanced-filter-condition .form-control {
        min-height: 38px;
        border: 0;
        box-shadow: none;
        background: transparent;
        font-size: .8rem;
    }

    .advanced-filter-condition .form-select:focus,
    .advanced-filter-condition .form-control:focus {
        box-shadow: none;
    }

    .advanced-filter-condition .remove-condition {
        width: 38px;
        height: 38px;
        border: 0;
        border-radius: 9px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #dc2626;
        background: transparent;
    }

    .advanced-filter-condition .remove-condition:hover {
        background: #fef2f2;
    }

    .advanced-filter-footer {
        display: flex;
        justify-content: flex-end;
        gap: .5rem;
        margin-top: 1rem;
    }

    .advanced-filter-footer .btn {
        padding: .5rem 1rem;
        font-size: .78rem;
        font-weight: 700;
    }

    #advanced-filter-active {
        border: 0;
        border-radius: 12px;
        background: #eff6ff;
        color: #1d4ed8;
        margin-bottom: 0;
    }

    #advanced-filter-active strong {
        color: #1d4ed8;
    }

    /* =========================
       Table
    ========================= */

    .inbox-table-wrapper {
        padding: 1.5rem 2rem 2rem;
    }

    .table-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: .85rem;
    }

    .table-title {
        display: flex;
        align-items: center;
        gap: .5rem;
        font-size: .9rem;
        font-weight: 800;
        color: var(--inbox-text);
    }

    .table-title i {
        color: var(--inbox-primary);
    }

    .table-modern {
        border: 1px solid var(--inbox-border);
        border-radius: 16px;
        overflow: hidden;
        background: #fff;
    }

    .table-modern table {
        margin-bottom: 0 !important;
    }

    .table-modern thead {
        background: #f8fafc;
    }

    .table-modern thead th {
        border-bottom: 1px solid var(--inbox-border);
        padding: 1rem .85rem;
        color: #64748b;
        font-size: .72rem;
        font-weight: 800;
        white-space: nowrap;
    }

    .table-modern tbody td {
        padding: .95rem .85rem;
        border-bottom: 1px solid #f1f5f9;
        color: #475569;
        font-size: .8rem;
    }

    .table-modern tbody tr {
        transition: all .18s ease;
    }

    .table-modern tbody tr:last-child td {
        border-bottom: 0;
    }

    .table-modern tbody tr:hover {
        background: #f8fbff;
    }

    .case-number {
        display: inline-flex;
        align-items: center;
        padding: .4rem .65rem;
        border-radius: 8px;
        background: #f1f5f9;
        color: #334155;
        font-weight: 800;
        font-size: .75rem;
    }

    .case-title {
        color: #1e293b;
        font-weight: 750;
    }

    .process-name {
        color: #475569;
        font-weight: 600;
    }

    .task-name {
        color: #334155;
        font-weight: 650;
    }

    .date-wrapper {
        display: flex;
        flex-direction: column;
        gap: .15rem;
    }

    .date-wrapper .date {
        color: #475569;
        font-weight: 700;
        font-size: .76rem;
    }

    .date-wrapper .time {
        color: #94a3b8;
        font-size: .7rem;
    }

    /* =========================
       Status
    ========================= */

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        padding: .42rem .7rem;
        border-radius: 9px;
        font-size: .7rem;
        font-weight: 800;
        white-space: nowrap;
    }

    .status-badge::before {
        content: '';
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: currentColor;
    }

    .status-new {
        background: #eff6ff;
        color: #1d4ed8;
    }

    .status-in-progress {
        background: #fffbeb;
        color: #d97706;
    }

    .status-draft {
        background: #f0f9ff;
        color: #0284c7;
    }

    .status-canceled {
        background: #fef2f2;
        color: #dc2626;
    }

    .status-done {
        background: #f0fdf4;
        color: #16a34a;
    }

    /* =========================
       Actions
    ========================= */

    .row-actions {
        display: flex;
        align-items: center;
        gap: .35rem;
    }

    .row-action {
        width: 34px;
        height: 34px;
        border-radius: 9px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all .18s ease;
        text-decoration: none !important;
    }

    .row-action i {
        font-size: 18px;
    }

    .row-action.view {
        background: #eff6ff;
        color: #2563eb;
    }

    .row-action.view:hover {
        background: #dbeafe;
        transform: translateY(-1px);
    }

    .row-action.cancel {
        background: #fef2f2;
        color: #dc2626;
    }

    .row-action.cancel:hover {
        background: #fee2e2;
        transform: translateY(-1px);
    }

    /* =========================
       Empty States
    ========================= */

    .inbox-empty {
        padding: 3.5rem 1rem;
        text-align: center;
    }

    .inbox-empty-icon {
        width: 70px;
        height: 70px;
        margin: 0 auto 1rem;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f1f5f9;
        color: #94a3b8;
    }

    .inbox-empty-icon i {
        font-size: 34px;
    }

    .inbox-empty-title {
        color: #334155;
        font-weight: 800;
        margin-bottom: .35rem;
    }

    .inbox-empty-text {
        color: #94a3b8;
        font-size: .8rem;
    }

    /* =========================
       Buttons
    ========================= */

    .btn-modern-primary {
        border: 0;
        border-radius: 10px;
        padding: .55rem .9rem;
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        background: #2563eb;
        color: #fff;
        font-size: .78rem;
        font-weight: 750;
        transition: all .2s ease;
    }

    .btn-modern-primary:hover {
        background: #1d4ed8;
        color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 5px 14px rgba(37, 99, 235, .2);
    }

    .btn-modern-light {
        border: 1px solid var(--inbox-border);
        border-radius: 10px;
        padding: .5rem .9rem;
        background: #fff;
        color: #64748b;
        font-size: .78rem;
        font-weight: 700;
    }

    .btn-modern-light:hover {
        background: #f8fafc;
        color: #334155;
    }

    /* =========================
       DataTables
    ========================= */

    .dataTables_wrapper {
        padding-top: .9rem;
    }

    .dataTables_wrapper .dataTables_filter input {
        border: 1px solid var(--inbox-border);
        border-radius: 10px;
        padding: .45rem .75rem;
        outline: none;
        font-size: .78rem;
    }

    .dataTables_wrapper .dataTables_filter input:focus {
        border-color: #93c5fd;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, .08);
    }

    .dataTables_wrapper .dataTables_info {
        color: #94a3b8;
        font-size: .72rem;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button {
        border-radius: 8px !important;
        border: 0 !important;
        font-size: .72rem;
    }

    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: #2563eb !important;
        color: #fff !important;
    }

    /* =========================
       Responsive
    ========================= */

    @media (max-width: 992px) {
        .inbox-header,
        .category-section,
        .inbox-table-wrapper {
            padding-left: 1.25rem;
            padding-right: 1.25rem;
        }

        .advanced-filter-wrapper {
            padding-left: 1.25rem;
            padding-right: 1.25rem;
        }

        .active-filter-card {
            margin-left: 1.25rem;
            margin-right: 1.25rem;
        }

        .inbox-header-content {
            align-items: flex-start;
        }

        .inbox-total-box {
            display: none;
        }

        .task-category-scroll {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 768px) {
        .categorized-inbox-page {
            padding: .5rem 0 1rem;
        }

        .categorized-inbox-card {
            border-radius: 18px;
        }

        .inbox-header {
            padding: 1.25rem;
        }

        .inbox-title-icon {
            width: 44px;
            height: 44px;
            border-radius: 13px;
        }

        .inbox-title {
            font-size: 1.1rem;
        }

        .category-section {
            padding: 1.15rem;
        }

        .task-category-scroll {
            grid-template-columns: 1fr;
        }

        .advanced-filter-wrapper {
            padding: 1rem 1.15rem 0;
        }

        .advanced-filter-header {
            flex-direction: column;
            align-items: stretch;
        }

        .advanced-filter-actions {
            flex-direction: column;
            align-items: stretch;
        }

        .match-mode-wrapper {
            justify-content: space-between;
        }

        .advanced-filter-condition.input-group {
            flex-wrap: wrap;
            border-radius: 14px;
            padding: .5rem;
        }

        .advanced-filter-condition .input-group-text {
            display: none;
        }

        .advanced-filter-condition .form-select,
        .advanced-filter-condition .form-control {
            width: 100%;
            flex: 0 0 100%;
            border: 1px solid #f1f5f9;
            border-radius: 9px;
            background: #f8fafc;
        }

        .advanced-filter-condition .remove-condition {
            position: absolute;
            top: .65rem;
            left: .65rem;
        }

        .active-filter-card {
            margin: 1rem 1.15rem 0;
        }

        .inbox-table-wrapper {
            padding: 1rem 1.15rem 1.25rem;
        }

        .table-toolbar {
            margin-bottom: .5rem;
        }

        .table-modern {
            border-radius: 12px;
        }
    }
</style>
```

@endsection

@section('content') <div class="container-fluid categorized-inbox-page"> <div class="card categorized-inbox-card">

```
        {{-- Header --}}
        <div class="inbox-header">
            <div class="inbox-header-content">

                <div class="inbox-title-wrapper">
                    <div class="inbox-title-icon">
                        <i class="fa fa-folder"></i>
                    </div>

                    <div>
                        <h1 class="inbox-title">
                            {{ trans('fields.Categorized Inbox') }}
                        </h1>

                        <p class="inbox-subtitle">
                            {{ trans('fields.Categorized Inbox Hint') }}
                        </p>
                    </div>
                </div>

                <div class="inbox-total-box">
                    <div class="icon">
                        <i class="fa fa-tasks"></i>
                    </div>

                    <div>
                        <small>{{ trans('fields.Items Count') }}</small>
                        <strong>{{ $totalCount }}</strong>
                    </div>
                </div>

            </div>
        </div>


        {{-- Categories --}}
        @if ($taskCategories->isNotEmpty())

            <div class="category-section">

                <div class="section-label">
                    <i class="fa fa-category"></i>
                    <span>{{ trans('fields.Switch Task') }}</span>
                </div>

                <div class="task-category-scroll">

                    {{-- All --}}
                    <button type="button"
                        class="category-chip {{ $selectedTaskId === null ? 'active' : '' }}"
                        data-task-filter="">

                        <div class="chip-main">

                            <span class="chip-icon">
                                <i class="fa fa-program"></i>
                            </span>

                            <span class="chip-label">
                                {{ trans('fields.All Tasks') }}
                            </span>

                        </div>

                        <span class="chip-count">
                            {{ $totalCount }}
                        </span>

                    </button>


                    @foreach ($taskCategories as $category)

                        <button type="button"
                            class="category-chip {{ $selectedTaskId === $category['id'] ? 'active' : '' }}"
                            data-task-filter="{{ $category['id'] }}">

                            <div class="chip-main">

                                <span class="chip-icon">
                                    <i class="fa fa-tasks"></i>
                                </span>

                                <span class="chip-label"
                                    title="{{ $category['label'] }}">
                                    {{ $category['label'] }}
                                </span>

                            </div>

                            <span class="chip-count">
                                {{ $category['count'] }}
                            </span>

                        </button>

                    @endforeach

                </div>

            </div>

        @else

            <div class="category-section">
                <div class="alert alert-info mb-0">
                    {{ trans('fields.You have no items in your inbox') }}
                </div>
            </div>

        @endif


        {{-- Active Task Filter --}}
        @if ($selectedTaskLabel)

            <div class="active-filter-card d-flex flex-wrap justify-content-between align-items-center gap-3">

                <div class="d-flex align-items-center gap-3">

                    <div class="inbox-title-icon"
                        style="width:40px;height:40px;border-radius:11px;box-shadow:none;">
                        <i class="fa fa-search" style="font-size:20px;"></i>
                    </div>

                    <div class="d-flex flex-column">

                        <span class="text-muted small">
                            {{ trans('fields.Selected Task') }}
                        </span>

                        <strong>
                            {{ $selectedTaskLabel }}
                        </strong>

                    </div>

                </div>

                <div class="d-flex align-items-center gap-3">

                    <div class="text-muted small">
                        {{ trans('fields.Items Count') }}:
                        <strong style="color:#334155;">
                            {{ $rows->count() }}
                        </strong>
                    </div>

                    <button type="button" data-task-filter="">
                        <i class="fa fa-close"></i>
                        {{ trans('fields.Clear Filter') }}
                    </button>

                </div>

            </div>

        @endif


        {{-- Advanced Filter --}}
        @if (access('باکس فیلتر پیشرفته کارتابل'))

            <div class="advanced-filter-wrapper">

                <div class="advanced-filter-card">

                    <div class="advanced-filter-header">

                        <div class="advanced-filter-title">

                            <div class="advanced-filter-icon">
                                <i class="fa fa-true"></i>
                            </div>

                            <div>
                                <h3>
                                    {{ trans('fields.Advanced Filter') }}
                                </h3>

                                <p>
                                    {{ trans('fields.Advanced Filter Hint') }}
                                </p>
                            </div>

                        </div>


                        <div class="advanced-filter-actions">

                            <div class="match-mode-wrapper">

                                <label for="advanced-filter-mode">
                                    {{ trans('fields.Match Mode') }}
                                </label>

                                <select id="advanced-filter-mode"
                                    class="form-select form-select-sm">

                                    <option value="and">
                                        {{ trans('fields.Match All Conditions') }}
                                    </option>

                                    <option value="or">
                                        {{ trans('fields.Match Any Conditions') }}
                                    </option>

                                </select>

                            </div>


                            <button type="button"
                                class="btn-modern-primary"
                                id="add-advanced-condition"
                                {{ empty($availableVariables) ? 'disabled' : '' }}>

                                <i class="fa fa-plus" style="font-size:17px;">
                                    
                                </i>

                                {{ trans('fields.Add Condition') }}

                            </button>

                        </div>

                    </div>


                    @if (empty($availableVariables))

                        <div class="alert alert-light border text-muted mb-0">
                            {{ trans('fields.No Variables Available') }}
                        </div>

                    @else

                        <div id="advanced-conditions"
                            class="d-flex flex-column gap-2">
                        </div>


                        <div class="advanced-filter-footer">

                            <button type="button"
                                class="btn-modern-light"
                                id="clear-advanced-filter">

                                {{ trans('fields.Clear Filter') }}

                            </button>

                            <button type="button"
                                class="btn-modern-primary"
                                id="apply-advanced-filter">

                                <i class="fa fa-search" style="font-size:16px;">
                                    
                                </i>

                                {{ trans('fields.Apply Filter') }}

                            </button>

                        </div>


                        <div id="advanced-filter-active"
                            class="alert d-none mt-3 small">
                        </div>

                    @endif

                </div>

            </div>

        @endif


        {{-- Table --}}
        <div class="inbox-table-wrapper">

            @if ($rows->isEmpty())

                <div class="inbox-empty">

                    <div class="inbox-empty-icon">
                        <i class="fa fa-folder"></i>
                    </div>

                    <div class="inbox-empty-title">
                        {{ trans('fields.You have no items in your inbox') }}
                    </div>

                    <div class="inbox-empty-text">
                        {{ trans('fields.Categorized Inbox Hint') }}
                    </div>

                </div>

            @else

                <div class="table-toolbar">

                    <div class="table-title">
                        <i class="material-icons">format_list_bulleted</i>
                        <span>{{ trans('fields.Items Count') }}: {{ $rows->count() }}</span>
                    </div>

                </div>


                <div class="table-responsive table-modern">

                    <table class="table align-middle"
                        id="categorized-inbox-table">

                        <thead>

                            <tr>

                                <th style="width:80px;"></th>

                                <th>
                                    {{ trans('fields.Case Number') }}
                                </th>

                                <th>
                                    {{ trans('fields.Case Title') }}
                                </th>

                                <th>
                                    {{ trans('fields.Process Title') }}
                                </th>

                                <th>
                                    {{ trans('fields.Task Title') }}
                                </th>

                                <th>
                                    {{ trans('fields.Status') }}
                                </th>

                                <th>
                                    {{ trans('fields.Received At') }}
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            @foreach ($rows as $index => $row)

                                @php
                                    $rowVariables = $caseVariables[$row->case_id] ?? [];
                                    $status = config('workflow.inboxStatus.' . $row->status);
                                @endphp

                                <tr ondblclick="window.location.href = '{{ route('simpleWorkflow.inbox.view', $row->id) }}'"
                                    data-case-id="{{ $row->case_id }}"
                                    data-variables='@json($rowVariables, JSON_UNESCAPED_UNICODE)'>

                                    {{-- Actions --}}
                                    <td>

                                        <div class="row-actions">

                                            <a href="{{ route('simpleWorkflow.inbox.view', $row->id) }}"
                                                class="row-action view"
                                                title="{{ trans('fields.View') }}">

                                                <i class="fa fa-folder">
                                                    
                                                </i>

                                            </a>


                                            @if ($row->task && $row->task->allow_cancel)

                                                <a href="{{ route('simpleWorkflow.inbox.cancel', $row->id) }}"
                                                    title="{{ trans('fields.Cancel') }}"
                                                    onclick="return confirm('آیا از لغو درخواست مطمئن هستید؟')"
                                                    class="row-action cancel">

                                                    <i class="fa fa-trash">
                                                        
                                                    </i>

                                                </a>

                                            @endif

                                        </div>

                                    </td>


                                    {{-- Case Number --}}
                                    <td>

                                        <span class="case-number">
                                            {{ optional($row->case ?? null)->number ?? '-' }}
                                        </span>

                                    </td>


                                    {{-- Case Title --}}
                                    <td>

                                        <div class="case-title">
                                            {{ $row->case_name ?? '-' }}
                                        </div>

                                    </td>


                                    {{-- Process --}}
                                    <td>

                                        <div class="process-name">
                                            {{ optional($row->task->process ?? null)->name ?? '-' }}
                                        </div>

                                    </td>


                                    {{-- Task --}}
                                    <td>

                                        <div class="task-name">
                                            {{ optional($row->task ?? null)->name ?? '-' }}
                                        </div>

                                    </td>


                                    {{-- Status --}}
                                    <td>

                                        <span class="status-badge bg-{{ $status['color'] }}">
                                            {{ trans('fields.' . $status['label']) }}
                                        </span>

                                    </td>


                                    {{-- Date --}}
                                    <td dir="ltr">

                                        <div class="date-wrapper">

                                            <span class="date">
                                                {{ toJalali($row->created_at)->format('Y-m-d') }}
                                            </span>

                                            <span class="time">
                                                {{ toJalali($row->created_at)->format('H:i') }}
                                            </span>

                                        </div>

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            @endif

        </div>

    </div>
</div>
```

@endsection

@section('script') <script>
const variableOptions = @json($availableVariables ?? []);

```
    const operatorOptions = [{
            value: 'equals',
            label: '{{ trans('fields.Filter Operator Equals') }}'
        },
        {
            value: 'not_equals',
            label: '{{ trans('fields.Filter Operator Not Equals') }}'
        },
        {
            value: 'contains',
            label: '{{ trans('fields.Filter Operator Contains') }}'
        },
        {
            value: 'not_contains',
            label: '{{ trans('fields.Filter Operator Not Contains') }}'
        },
        {
            value: 'starts_with',
            label: '{{ trans('fields.Filter Operator Starts With') }}'
        },
        {
            value: 'ends_with',
            label: '{{ trans('fields.Filter Operator Ends With') }}'
        },
        {
            value: 'gt',
            label: '{{ trans('fields.Filter Operator Greater Than') }}'
        },
        {
            value: 'lt',
            label: '{{ trans('fields.Filter Operator Less Than') }}'
        },
        {
            value: 'is_empty',
            label: '{{ trans('fields.Filter Operator Is Empty') }}'
        },
        {
            value: 'is_not_empty',
            label: '{{ trans('fields.Filter Operator Is Not Empty') }}'
        },
    ];

    const advancedFilterState = {
        conditions: [],
        mode: 'and'
    };

    let categorizedTableInstance = null;
    let conditionsContainer = null;
    let addConditionButton = null;
    let applyFilterButton = null;
    let clearFilterButton = null;
    let matchModeSelect = null;


    function normalizeValue(value) {
        if (value === undefined || value === null) {
            return '';
        }

        if (Array.isArray(value)) {
            return value.join(', ');
        }

        if (typeof value === 'object') {
            try {
                return JSON.stringify(value);
            } catch (error) {
                return '';
            }
        }

        return String(value);
    }


    function isNumeric(value) {
        if (value === undefined || value === null) {
            return false;
        }

        const normalized = String(value)
            .replace(/\s+/g, '')
            .replace(/,/g, '.');

        return normalized !== '' && !isNaN(Number(normalized));
    }


    function compareValues(value, target, comparator) {

        if (isNumeric(value) && isNumeric(target)) {

            const numericValue = Number(
                String(value)
                .replace(/\s+/g, '')
                .replace(/,/g, '.')
            );

            const numericTarget = Number(
                String(target)
                .replace(/\s+/g, '')
                .replace(/,/g, '.')
            );

            return comparator(numericValue, numericTarget);
        }

        const valueString = normalizeValue(value).toLowerCase();
        const targetString = normalizeValue(target).toLowerCase();

        return comparator(valueString, targetString);
    }


    function evaluateCondition(rawValue, condition) {

        const valueString = normalizeValue(rawValue);
        const searchValue = normalizeValue(condition.value);

        const lowerValue = valueString.toLowerCase();
        const lowerSearch = searchValue.toLowerCase();

        switch (condition.operator) {

            case 'equals':
                return lowerValue === lowerSearch;

            case 'not_equals':
                return lowerValue !== lowerSearch;

            case 'contains':
                return lowerSearch === '' ?
                    true :
                    lowerValue.includes(lowerSearch);

            case 'not_contains':
                return lowerSearch === '' ?
                    true :
                    !lowerValue.includes(lowerSearch);

            case 'starts_with':
                return lowerSearch === '' ?
                    true :
                    lowerValue.startsWith(lowerSearch);

            case 'ends_with':
                return lowerSearch === '' ?
                    true :
                    lowerValue.endsWith(lowerSearch);

            case 'gt':
                return compareValues(
                    valueString,
                    searchValue,
                    (a, b) => a > b
                );

            case 'lt':
                return compareValues(
                    valueString,
                    searchValue,
                    (a, b) => a < b
                );

            case 'is_empty':
                return valueString.trim() === '';

            case 'is_not_empty':
                return valueString.trim() !== '';

            default:
                return true;
        }
    }


    function evaluateAdvancedFilters(variables) {

        if (!advancedFilterState.conditions.length) {
            return true;
        }

        const results = advancedFilterState.conditions.map((condition) => {

            const value =
                variables &&
                Object.prototype.hasOwnProperty.call(
                    variables,
                    condition.field
                ) ?
                variables[condition.field] :
                '';

            return evaluateCondition(value, condition);
        });

        if (advancedFilterState.mode === 'or') {
            return results.some(Boolean);
        }

        return results.every(Boolean);
    }


    function toggleValueInput(rowElement, operator) {

        const valueInput =
            rowElement.querySelector('.value-input');

        if (!valueInput) {
            return;
        }

        const requiresValue =
            !['is_empty', 'is_not_empty'].includes(operator);

        valueInput.disabled = !requiresValue;

        if (!requiresValue) {
            valueInput.value = '';
        }
    }


    function createConditionRow() {

        if (!conditionsContainer) {
            return;
        }

        const row = document.createElement('div');

        row.className =
            'advanced-filter-condition input-group input-group-sm flex-nowrap position-relative';


        const variableOptionsMarkup =
            variableOptions.map((option) =>
                `<option value="${option.key}">${option.label}</option>`
            ).join('');


        const operatorOptionsMarkup =
            operatorOptions.map((option) =>
                `<option value="${option.value}">${option.label}</option>`
            ).join('');


        row.innerHTML = `
            <span class="input-group-text material-icons">
                tune
            </span>

            <select class="form-select form-select-sm variable-select">
                <option value="">
                    {{ trans('fields.Select') }}
                </option>

                ${variableOptionsMarkup}
            </select>

            <select class="form-select form-select-sm operator-select">
                ${operatorOptionsMarkup}
            </select>

            <input
                type="text"
                class="form-control form-control-sm value-input"
                placeholder="{{ trans('fields.Value') }}"
            >

            <button
                type="button"
                class="btn btn-outline-danger btn-sm remove-condition"
                title="{{ trans('fields.Remove') }}"
            >
                <i class="material-icons fs-6">
                    close
                </i>
            </button>
        `;


        const operatorSelect =
            row.querySelector('.operator-select');


        if (operatorSelect) {

            toggleValueInput(
                row,
                operatorSelect.value
            );

            operatorSelect.addEventListener(
                'change',
                (event) => {
                    toggleValueInput(
                        row,
                        event.target.value
                    );
                }
            );
        }


        const removeButton =
            row.querySelector('.remove-condition');


        if (removeButton) {

            removeButton.addEventListener(
                'click',
                () => {
                    row.remove();
                }
            );
        }


        conditionsContainer.appendChild(row);
    }


    function updateActiveFilterAlert() {

        const alertElement =
            document.getElementById('advanced-filter-active');

        if (!alertElement) {
            return;
        }


        if (!advancedFilterState.conditions.length) {

            alertElement.classList.add('d-none');
            alertElement.innerHTML = '';

            return;
        }


        const modeText =
            advancedFilterState.mode === 'and' ?
            '{{ trans('fields.Match All Conditions') }}' :
            '{{ trans('fields.Match Any Conditions') }}';


        const summary =
            advancedFilterState.conditions.map((condition) => {

                const variable =
                    variableOptions.find(
                        (option) =>
                        option.key === condition.field
                    );

                const operator =
                    operatorOptions.find(
                        (option) =>
                        option.value === condition.operator
                    );


                const variableLabel =
                    variable ?
                    variable.label :
                    condition.field;

                const operatorLabel =
                    operator ?
                    operator.label :
                    condition.operator;


                if (
                    ['is_empty', 'is_not_empty']
                    .includes(condition.operator)
                ) {
                    return `${variableLabel} ${operatorLabel}`;
                }


                return `${variableLabel} ${operatorLabel} «${condition.value}»`;
            });


        alertElement.classList.remove('d-none');

        alertElement.innerHTML = `
            <div class="fw-semibold mb-1">
                {{ trans('fields.Active Filters') }}
            </div>

            <div>
                ${summary.join('، ')}
            </div>

            <div class="text-muted mt-2">
                {{ trans('fields.Match Mode') }}:
                ${modeText}
            </div>
        `;
    }


    function applyAdvancedFilter() {

        if (!conditionsContainer) {
            return;
        }


        const rawConditions =
            Array.from(
                conditionsContainer.querySelectorAll(
                    '.advanced-filter-condition'
                )
            ).map((row) => {

                const field =
                    row.querySelector(
                        '.variable-select'
                    )?.value || '';

                const operator =
                    row.querySelector(
                        '.operator-select'
                    )?.value || '';

                const valueInput =
                    row.querySelector(
                        '.value-input'
                    );

                const value =
                    valueInput &&
                    !valueInput.disabled ?
                    valueInput.value.trim() :
                    '';


                return {
                    field,
                    operator,
                    value
                };

            }).filter((condition) => {

                if (
                    !condition.field ||
                    !condition.operator
                ) {
                    return false;
                }

                if (
                    ['is_empty', 'is_not_empty']
                    .includes(condition.operator)
                ) {
                    return true;
                }

                return condition.value !== '';
            });


        advancedFilterState.conditions =
            rawConditions;

        advancedFilterState.mode =
            matchModeSelect ?
            matchModeSelect.value :
            'and';


        updateActiveFilterAlert();


        if (categorizedTableInstance) {
            categorizedTableInstance.draw();
        }
    }


    function clearAdvancedFilter() {

        advancedFilterState.conditions = [];
        advancedFilterState.mode = 'and';


        if (conditionsContainer) {
            conditionsContainer.innerHTML = '';
        }


        if (matchModeSelect) {
            matchModeSelect.value = 'and';
        }


        updateActiveFilterAlert();


        if (categorizedTableInstance) {
            categorizedTableInstance.draw();
        }
    }


    function updateTaskFilter(taskId) {

        const url =
            new URL(window.location.href);

        if (taskId) {
            url.searchParams.set('task', taskId);
        } else {
            url.searchParams.delete('task');
        }

        window.location.href =
            url.toString();
    }


    document.addEventListener(
        'DOMContentLoaded',
        () => {

            document
                .querySelectorAll('[data-task-filter]')
                .forEach((button) => {

                    button.addEventListener(
                        'click',
                        () => {
                            updateTaskFilter(
                                button.dataset.taskFilter
                            );
                        }
                    );
                });


            const taskSelect =
                document.getElementById(
                    'task-filter'
                );


            if (taskSelect) {

                taskSelect.addEventListener(
                    'change',
                    () => {
                        updateTaskFilter(
                            taskSelect.value
                        );
                    }
                );
            }


            conditionsContainer =
                document.getElementById(
                    'advanced-conditions'
                );

            addConditionButton =
                document.getElementById(
                    'add-advanced-condition'
                );

            applyFilterButton =
                document.getElementById(
                    'apply-advanced-filter'
                );

            clearFilterButton =
                document.getElementById(
                    'clear-advanced-filter'
                );

            matchModeSelect =
                document.getElementById(
                    'advanced-filter-mode'
                );


            if (!conditionsContainer) {
                return;
            }


            if (
                addConditionButton &&
                variableOptions.length
            ) {

                addConditionButton.addEventListener(
                    'click',
                    () => {
                        createConditionRow();
                    }
                );
            }


            if (applyFilterButton) {

                applyFilterButton.addEventListener(
                    'click',
                    () => {
                        applyAdvancedFilter();
                    }
                );
            }


            if (clearFilterButton) {

                clearFilterButton.addEventListener(
                    'click',
                    () => {
                        clearAdvancedFilter();
                    }
                );
            }


            if (matchModeSelect) {

                matchModeSelect.addEventListener(
                    'change',
                    () => {

                        if (
                            advancedFilterState
                            .conditions.length
                        ) {

                            advancedFilterState.mode =
                                matchModeSelect.value;

                            updateActiveFilterAlert();

                            if (
                                categorizedTableInstance
                            ) {
                                categorizedTableInstance.draw();
                            }
                        }
                    }
                );
            }

        }
    );


    $.fn.dataTable.ext.search.push(
        function(settings, data, dataIndex) {

            if (
                !settings.nTable ||
                settings.nTable.id !==
                'categorized-inbox-table'
            ) {
                return true;
            }


            if (
                !advancedFilterState
                .conditions.length
            ) {
                return true;
            }


            const rowData =
                settings.aoData[dataIndex];


            if (
                !rowData ||
                !rowData.nTr
            ) {
                return true;
            }


            let variables =
                $(rowData.nTr)
                .data('variables');


            if (
                typeof variables === 'string'
            ) {

                try {
                    variables =
                        JSON.parse(variables);
                } catch (error) {
                    variables = {};
                }
            }


            variables =
                variables || {};


            return evaluateAdvancedFilters(
                variables
            );
        }
    );


    $(document).ready(function() {

        if (
            $('#categorized-inbox-table').length
        ) {

            categorizedTableInstance =
                $('#categorized-inbox-table')
                .DataTable({

                    language: {
                        url: 'https://cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Persian.json'
                    },

                    order: [
                        [6, 'desc']
                    ],

                    pageLength: 15,

                    lengthChange: false,

                    responsive: true,

                    autoWidth: false,

                    columnDefs: [{
                        targets: 0,
                        orderable: false,
                        searchable: false
                    }]
                });
        }
    });
</script>
```

@endsection
