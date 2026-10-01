@php
    // ایجاد فاصله برای سطح فعلی درخت
    $indentation = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $level);
@endphp

@foreach ($children as $child)
    @php
        $bgColor = 'bg-' . ($child->element()?->bootstrapColor() ?? 'secondary');
    @endphp
    {{-- <div class=""> --}}
        <div class=" p-2 bg-light">

            {!! $indentation !!}
            <a type="submit" class="" style=""
                    href="{{ route('simpleWorkflow.task.edit', $child->id) }}"><i class="fa fa-edit"></i></a>
            <strong class="">
                <a data-toggle="collapse" href="#{{ $child->id }}">{{ $child->name }}</a>
                @if ($child->is_preview)
                    <span class="badge bg-secondary text-dark">{{ trans('fields.Preview') }}</span>
                @endif
                <span class="badge {{ $bgColor }}">
                    {{ ucfirst($child->type) }}
                </span>
                <input type="hidden" name="id" value="{{ $child->id }}">
                <div class="" style="display: inline">
                    @if ($child->next_element_id)
                        @php
                            $nextBgColor = 'bg-' . ($child->nextTask()?->element()?->bootstrapColor() ?? 'secondary');
                        @endphp
                        <span class="badge {{ $nextBgColor }}">{{ trans('Next Task') }} :
                            {{ $child->nextTask()->name }}
                        </span>
                    @endif
                </div>

            </strong>
            @if ($error = taskHasError($child->id))
                <i class="fa fa-exclamation-triangle text-danger" title="{{ $error['descriptions'] }}"></i>
            @endif


        </div>

        <div id="{{ $child->id }}" class="">
                @if (count($child->children()))
                    @include('SimpleWorkflowView::Core.Task.tree', [
                        'children' => $child->children(),
                        'level' => $level +1,
                    ])
                @endif
        </div>

    {{-- </div> --}}
@endforeach
