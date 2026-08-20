@extends('behin-layouts.app')

@section('title')
    تاریخچه
@endsection

@section('content')
    <div class="container">
        <div class="">
            <div class="card table-responsive">
                <div class="card-header bg-info">
                    {{-- تاریخچه انجام کار پرونده شماره {{ $rows[0]->case->number }} --}}
                    <a href="{{ route('simpleWorkflow.inbox.cancel', $rows[0]->id) }}" class="btn btn-sm btn-danger">
                        کنسل کردن پرونده
                    </a>
                    <form action="{{ route('simpleWorkflow.inbox.uncanceledCase', $rows[0]->case->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-warning">
                            در دست بررسی کردن پرونده
                        </button>
                    </form>
                </div>
                <div class="card-body">
                    <table class="table table-stripped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ trans('fields.Process') }}</th>
                                <th>{{ trans('fields.Task') }}</th>
                                <th>{{ trans('fields. Case') }}</th>
                                <th>{{ trans('fields.Actor') }}</th>
                                <th>{{ trans('fields.Status') }}</th>
                                <th>{{ trans('fields.Created At') }}</th>
                                <th>{{ trans('fields.Done Date') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rows as $row)
                                <tr @if (config("workflow.inboxStatus.{$row->status}.type") === 'open') class="bg-warning" @endif>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $row->task->process->name }}</td>
                                    <td>{{ $row->task->name }}</td>
                                    <td>{{ $row->case_name }}</td>
                                    <td>{{ getUserInfo($row->actor)?->name }}</td>
                                    <td>{{ trans('fields.' . $row->status) }}</td>
                                    <td dir="ltr">{{ toJalali($row->created_at)->format('Y-m-d H:i') }}</td>
                                    <td dir="ltr">
                                        {{ $row->updated_at != $row->created_at ? toJalali($row->updated_at)->format('Y-m-d H:i') : '' }}
                                    </td>
                                    <td>
                                        @if (access('مدیریت تاریخچه'))
                                            <a href="{{ route('simpleWorkflow.inbox.edit', $row->id) }}"
                                                class="btn btn-sm btn-primary">{{ trans('fields.Edit') }}</a>
                                            <a href="{{ route('simpleWorkflow.inbox.changeStatus', $row->id) }}"
                                                class="btn btn-sm btn-warning">{{ trans('fields.Change Status') }}</a>
                                            <a href="{{ route('simpleWorkflow.inbox.copy', $row->id) }}"
                                                class="btn btn-sm btn-info">
                                                کپی
                                            </a>
                                            <form action="{{ route('simpleWorkflow.inbox.destroy', $row->id) }}"
                                                method="POST" style="display:inline"
                                                onsubmit="return confirm('آیا از حذف این ردیف تاریخچه اطمینان دارید؟')">
                                                @csrf
                                                @method('DELETE')

                                                <button class="btn btn-sm btn-danger">
                                                    حذف
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
@endsection
