@extends('behin-layouts.app')

@section('title', trans('Edit Process'))

@section('content')
    <script src="https://lib.arvancloud.ir/mermaid/9.4.2-rc.1/mermaid.min.js"></script>
    <script>
        mermaid.initialize({
            startOnLoad: true
        });
    </script>
    <style>
        {{-- استایل نودهای دیاگرام به‌صورت پویا از رجیستری المان‌ها ساخته می‌شود --}}
        @foreach ($elementDiagramStyles ?? [] as $style)
            .{{ $style['class'] }} {
                fill: {{ $style['fill'] }} !important;
                stroke: {{ $style['stroke'] }} !important;
                font-family: Vazir !important;
                color: white !important;
            }
        @endforeach
    </style>
    <div class="card">
        <div class="card-header">
            {{ $process->name }}
            <button onclick="check_error()" class="btn btn-danger col-sm-auto">
                {{ trans('fields.Check Error') }}
            </button>
        </div>

        <div class="card-body table-responsive">
            <div class="mermaid" style="width: 1000px">
                graph TD
                @foreach ($process->startTasks() as $task)
                    @php
                        // کلاس و شکل نود از المان ثبت‌شده در رجیستری گرفته می‌شود
                        $taskElement = $task->element();
                        $taskClass = $taskElement?->diagramClass() ?? \Behin\SimpleWorkflow\Elements\DiagramClass::make(null);
                        [$shapeStart, $shapeEnd] = $taskElement?->diagramShape() ?? ['[', ']'];
                    @endphp
                    {{ $task->id }}{{ $shapeStart }}"{{ $task->name }}"{{ $shapeEnd }}:::{{ $taskClass }}
                    click {{ $task->id }} "{{ route('simpleWorkflow.task.edit', $task->id) }}"
                    @php
                        $children = $task->children();
                    @endphp
                    @if (count($children))
                        @include('SimpleWorkflowView::Core.Task.tree1', [
                            'children' => $children,
                            'task' => $task,
                        ])
                    @endif
                @endforeach
            </div>
        </div>








        <!-- Modal for editing tasks -->
        <div class="modal fade" id="taskModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-body p-0">
                        <iframe id="taskModalIframe" style="width:100%;height:80vh;border:0;"></iframe>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <div class="card mb-3">
        <div class="card-header">{{ trans('fields.Script Before Start') }}</div>
        <div class="card-body">
            <form action="{{ route('simpleWorkflow.process.update', $process->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-sm-10">
                        <select name="script_before_start" id="script_before_start" class="form-select select2">
                            <option value="">{{ trans('None') }}</option>
                            @foreach ($scripts as $script)
                                <option value="{{ $script->id }}" {{ $process->script_before_start == $script->id ? 'selected' : '' }}>
                                    {{ $script->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-sm-2">
                        <button type="submit" class="btn btn-primary w-100">{{ trans('Save') }}</button>
                    </div>
                </div>
                <small class="form-text text-muted">
                    {{ trans('fields.Script Before Start Hint') }}
                </small>
            </form>
        </div>
    </div>
    <div class="card">
        <form action="{{ route('simpleWorkflow.task.create') }}" method="POST" class="">
            @csrf
            <input type="hidden" name="process_id" value="{{ $process->id }}">
            <div class="row mb-3">
                <label for="name" class="col-sm-2 col-form-label">{{ trans('Task Name') }}</label>
                <div class="col-sm-10">
                    <input type="text" name="name" id="name" class="form-control"
                        placeholder="{{ trans('Enter task name') }}">
                </div>
            </div>
            <div class="row mb-3">
                <label for="type" class="col-sm-2 col-form-label">{{ trans('Task Type') }}</label>
                <div class="col-sm-10">
                    <select name="type" id="type" class="form-select">
                        {{-- گزینه‌های نوع تسک از رجیستری المان‌ها خوانده می‌شود --}}
                        @foreach ($elementOptions ?? [] as $elementKey => $elementLabel)
                            <option value="{{ $elementKey }}">{{ $elementLabel }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row mb-3">
                <label for="parent_id" class="col-sm-2 col-form-label">{{ trans('Parent Task') }}</label>
                <div class="col-sm-10">
                    <select name="parent_id" id="parent_id" class="form-select select2">
                        <option value="">{{ trans('None') }}</option>
                        @foreach ($process->tasks() as $task)
                            <option value="{{ $task->id }}">{{ $task->name }}
                                @if ($task->is_preview)
                                    ({{ trans('fields.Preview') }})
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-10 offset-sm-2">
                    <button type="submit" class="btn btn-primary">{{ trans('Create') }}</button>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('script')
    <script>
        initial_view();

        document.addEventListener('click', function(e) {
            const link = e.target.closest('.task-edit-link');
            if (link) {
                e.preventDefault();
                var url = link.getAttribute('href');
                window.location = url;
            }
        });

        window.addEventListener('message', function(e) {
            if (e.data === 'task-updated') {
                $('#taskModal').modal('hide');
                location.reload();
            }
        });

        function create_process() {
            var form = $('#create-process-form')[0];
            var fd = new FormData(form);
            send_ajax_formdata_request(
                "{{ route('simpleWorkflow.process.create') }}",
                fd,
                function(response) {
                    console.log(response);

                }
            )

        }

        function check_error() {
            send_ajax_get_request(
                "{{ route('simpleWorkflow.process.processHasError', ['processId' => $process->id]) }}",
                function(response) {
                    if (response > 0) {
                        show_error('Process Has Error');
                    } else {
                        show_message('Process Has No Error');
                    }
                }
            )
        }
    </script>
@endsection
