@php
    $content = json_decode($form->content);
    $formMode = in_array($inbox->status, ['done', 'doneByOther']) ? 'readonly' : null;
@endphp


@extends('behin-layouts.app')

@section('title', $form->name)

@section('content')
    @include('SimpleWorkflowView::Core.Inbox.header', [
        'case' => $case
    ])

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="card shadow-sm mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">{{ $task->name }} - {{ $inbox->case_name }}</h6>
            {{-- <h6 class="m-0 font-weight-bold text-primary">{{ $form->name }}</h6> --}}
        </div>
        <div class="card-body">
            <form action="javascript:void(0)" method="POST" id="form" enctype="multipart/form-data"
                class="needs-validation" novalidate>
                <input type="hidden" name="inboxId" id="inboxId" value="{{ $inbox->id }}">
                <input type="hidden" name="caseId" id="caseId" value="{{ $case->id }}">
                <input type="hidden" name="taskId" id="taskId" value="{{ $task->id }}">
                <input type="hidden" name="processId" id="processId" value="{{ $process->id }}">
                @if (View::exists('SimpleWorkflowView::Custom.Form.' . $form->id))
                    @include('SimpleWorkflowView::Custom.Form.' . $form->id, [
                        'form' => $form,
                        'task' => $task,
                        'case' => $case,
                        'inbox' => $inbox,
                        'variables' => $variables,
                        'process' => $process,
                        'mode' => $formMode,
                    ])
                @else
                    @include('SimpleWorkflowView::Core.Form.preview', [
                        'form' => $form,
                        'task' => $task,
                        'case' => $case,
                        'inbox' => $inbox,
                        'variables' => $variables,
                        'process' => $process,
                        'mode' => $formMode,
                    ])
                @endif
            </form>
        </div>
    </div>

    @if (in_array($inbox->status, ['done', 'doneByOther', 'doneBySystem']))
        @php $performerId = $inbox->done_by ?? $inbox->actor; @endphp
        <div class="card shadow-sm mb-2">
            <div class="card-body">
                <p class="m-0">
                    <i class="material-icons text-success mr-2">check_circle</i>
                    {{ trans('fields.Done At') }}:
                    <span class="badge badge-light" dir="ltr">
                        {{ toJalali($inbox->done_at ?? $inbox->updated_at)->format('Y-m-d H:i') }}
                    </span>
                    <br>
                    {{ trans('fields.Done By') }}:
                    <span class="badge badge-light">
                        {{ getUserInfo($performerId)?->name ?? '-' }}
                    </span>
                    @if ($inbox->status === 'doneByOther')
                        <br>
                        {{ trans('fields.Status') }}:
                        <span class="badge badge-warning">{{ trans('fields.doneByOther') }}</span>
                    @endif
                </p>
            </div>
        </div>
    @else
        <div class="row col-sm-12 bg-white p-2 shadow-md justify-between items-center gap-2">
            @if ($task->show_cancel_button)
                <a class="col-sm-3 btn btn-sm btn-outline-danger mb-1" href="{{ route('simpleWorkflow.inbox.cancel', $inbox->id) }}"
                    onclick="return confirm('{{ trans('fields.Are you sure?') }}');">
                    <i class="fa fa-danger"></i>
                    {{ trans('fields.Cancel Request') }}
                </a>
            @endif
            @if ($inbox->status == 'draft')
                <button class="col-sm-3 btn btn-sm btn-outline-info mb-1" onclick="createCaseNumberAndSave()">
                    <i class="material-icons">file_alt</i>
                    {{ trans('fields.Create Case Number and Save') }}
                </button>
            @else
                <button class="col-sm-3 btn btn-sm btn-outline-warning mb-1" onclick="showJumpModal()">
                    <i class="fa fa-arrow-right"></i>
                    {{ trans('fields.Send Manully') }}
                </button>

                @if ($task->show_reminder_button)
                    <button class="col-sm-3 btn btn-sm btn-outline-secondary mb-1" type="button" onclick="openReminderModal()">
                        <i class="material-icons">alarm</i>
                        {{ trans('fields.Add Reminder') }}
                    </button>
                @endif

                @if ($task->show_save_button)
                    <button class="col-sm-3 btn btn-sm btn-outline-primary mb-1" onclick="saveForm()">
                        <i class="fa fa-save"></i>
                        {{ trans('fields.Save') }}
                    </button>
                @endif

                <button class="col-sm-3 btn btn-sm btn-outline-success mb-1" onclick="saveAndNextForm()">
                    <i class="fa fa-save"></i>
                    {{ trans('fields.Save and next') }}
                </button>
            @endif
        </div>

        @if ($task->show_reminder_button)
            <div class="modal fade" id="reminderModal" tabindex="-1" role="dialog" aria-labelledby="reminderModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="reminderModalLabel">{{ trans('fields.Add Reminder') }}</h5>
                            <button type="button" class="close" data-dismiss="modal"
                                aria-label="{{ trans('fields.Close') }}">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form id="reminderForm" action="{{ route('simpleWorkflow.inbox.reminders.store', $inbox->id) }}"
                            method="POST">
                            @csrf
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="reminder-title"
                                        class="col-form-label">{{ trans('fields.Reminder Title') }}</label>
                                    <input type="text" class="form-control" id="reminder-title" name="title"
                                        maxlength="255" required>
                                </div>
                                <div class="form-group">
                                    <label for="reminder-message"
                                        class="col-form-label">{{ trans('fields.Reminder Message') }}</label>
                                    <textarea class="form-control" id="reminder-message" name="message" rows="3"></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="reminder-at"
                                        class="col-form-label">{{ trans('fields.Reminder At') }}</label>
                                    <input type="datetime-local" class="form-control" id="reminder-at" name="remind_at"
                                        required min="{{ now()->format('Y-m-d\TH:i') }}"
                                        value="{{ now()->addMinutes(5)->format('Y-m-d\TH:i') }}">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary"
                                    data-dismiss="modal">{{ trans('fields.Cancel') }}</button>
                                <button type="submit"
                                    class="btn btn-primary">{{ trans('fields.Send Reminder') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif


    @endif
@endsection

@section('script')
    <script>
        // initial_view()
        $('#mobile-navigation').fadeOut(1000)

        function formatDateTimeLocal(date) {
            const pad = (value) => String(value).padStart(2, '0');
            return `${date.getFullYear()}-${pad(date.getMonth() + 1)}-${pad(date.getDate())}T${pad(date.getHours())}:${pad(date.getMinutes())}`;
        }

        function openReminderModal() {
            const modal = $('#reminderModal');
            if (!modal.length) {
                return;
            }

            const input = modal.find('#reminder-at');
            const now = new Date();
            input.attr('min', formatDateTimeLocal(now));
            const defaultDate = new Date(now.getTime() + 5 * 60 * 1000);
            input.val(formatDateTimeLocal(defaultDate));
            modal.modal('show');
        }

        const reminderForm = $('#reminderForm');
        if (reminderForm.length) {
            reminderForm.on('submit', function(event) {
                event.preventDefault();
                const form = this;
                const fd = new FormData(form);
                send_ajax_formdata_request(form.action, fd, function(response) {
                    if (response.status === 200) {
                        show_message(response.msg);
                        form.reset();
                        const modal = $('#reminderModal');
                        if (modal.length) {
                            modal.modal('hide');
                        }
                    } else {
                        show_error(response.msg || '{{ trans('fields.Unknown error') }}');
                    }
                }, function(xhr) {
                    hide_loading();
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        const messages = Object.values(xhr.responseJSON.errors).flat();
                        show_error(messages.join('<br>'));
                        return;
                    }

                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        show_error(xhr.responseJSON.message);
                        return;
                    }

                    show_error('{{ trans('fields.Unknown error') }}');
                });
            });
        }

        function createCaseNumberAndSave() {
            var form = $('#form')[0];
            var fd = new FormData(form);
            send_ajax_formdata_request(
                '{{ route('simpleWorkflow.routing.createCaseNumberAndSave') }}',
                fd,
                function(response) {
                    console.log(response);
                    if (response.status == 200) {
                        show_message(response.msg)
                        window.location.reload();
                    } else {
                        show_error(response.msg);
                    }
                }
            )
        }

        function saveForm() {
            if ($('.view-model-update-btn').length > 0) {
                $('.view-model-update-btn').click()
            }
            var form = $('#form')[0];
            var fd = new FormData(form);
            send_ajax_formdata_request(
                '{{ route('simpleWorkflow.routing.save') }}',
                fd,
                function(response) {
                    console.log(response);
                    if (response.status == 200) {
                        show_message(response.msg)
                        window.location.reload();
                    } else {
                        show_error(response.msg);
                    }
                }
            )
        }

        function saveAndNextForm() {
            if ($('.view-model-update-btn').length > 0) {
                $('.view-model-update-btn').click()
            }
            var form = $('#form')[0];
            var fd = new FormData(form);
            send_ajax_formdata_request(
                '{{ route('simpleWorkflow.routing.saveAndNext') }}',
                fd,
                function(response) {
                    console.log(response);
                    if (response.status == 200) {
                        if (response.url) {
                            if (response.msg) {
                                window.location.href = response.url + '?msg=' + response.msg;
                            } else {
                                window.location.href = response.url;
                            }
                        } else {
                            show_message(response.msg)
                            window.location.href = '{{ route('simpleWorkflow.inbox.index') }}';
                        }
                    } else {
                        show_error(response.msg);
                    }
                }
            )
        }

        function showJumpModal(task_id) {
            send_ajax_get_request(
                '{{ route('simpleWorkflow.task-jump.show', [$task->id, $inbox->id, $case->id, $process->id]) }}',
                function(response) {
                    open_admin_modal_with_data(response, '')
                }
            )
        }

        @if (in_array($inbox->status, ['done', 'doneByOther']))
            $(document).ready(function() {
                var form = $('#form');
                form.find('input, select, textarea, button').prop('disabled', true);
                form.find('a').removeAttr('href').css('pointer-events', 'none');
            });
        @endif
    </script>
@endsection
