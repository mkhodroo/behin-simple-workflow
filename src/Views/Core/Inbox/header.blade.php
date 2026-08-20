<div class="card shadow-sm border-0">
    <div class="card-header bg-white">
        <h6 class="mb-0 font-weight-bold">
            <i class="fa fa-folder-open text-primary"></i>
            اطلاعات پرونده
        </h6>
    </div>

    <div class="card-body py-3">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <span class="text-muted">
                <i class="fa fa-hashtag"></i>
                {{ trans('fields.Case Number') }}
            </span>

            <span class="badge badge-primary px-3 py-2">
                {{ $case->number }}
            </span>
        </div>

        <hr class="my-2">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <span class="text-muted">
                <i class="fa fa-user"></i>
                {{ trans('fields.Creator') }}
            </span>

            <span class="badge badge-light border px-3 py-2">
                {{ getUserInfo($case->creator)->name }}
            </span>
        </div>

        <hr class="my-2">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <span class="text-muted">
                <i class="fa fa-clock"></i>
                {{ trans('fields.Created At') }}
            </span>

            <span class="badge badge-light border px-3 py-2" dir="ltr">
                {{ toJalali($case->created_at)->format('Y-m-d H:i') }}
            </span>
        </div>

        <hr class="my-2">

        <div>
            <small class="text-muted d-block mb-1">
                <i class="fa fa-fingerprint"></i>
                شناسه پرونده
            </small>

            <code class="d-block p-2 bg-light rounded">
                {{ $case->id }}
            </code>
        </div>

    </div>
</div>