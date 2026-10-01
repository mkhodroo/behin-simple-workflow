@foreach ($children as $child)
    @php
        // کلاس، شکل و رنگ نود از المان ثبت‌شده در رجیستری المان‌ها گرفته می‌شود
        $childElement = $child->element();
        $taskClass = $childElement?->diagramClass() ?? 'task-default';
        [$shapeStart, $shapeEnd] = $childElement?->diagramShape() ?? ['[', ']'];
        $taskName = $child->name . ($child->is_preview ? ' (' . trans('fields.Preview') . ')' : '');
    @endphp

    {{-- لینک پدر به فرزند --}}
    {{ $task->id }}-->{{ $child->id }}{{ $shapeStart }}"{{ $taskName }}"{{ $shapeEnd }}:::{{ $taskClass }}

    {{-- اضافه‌کردن لینک قابل کلیک به نود --}}
    click {{ $child->id }} "{{ route('simpleWorkflow.task.edit', $child->id) }}" "{{ trans('fields.Edit') }}"

    @php
        $children = $child->children();
    @endphp

    {{-- اگر المنت بعدی دارد --}}
    @if ($child->next_element_id)
        {{ $child->id }} --> {{ $child->next_element_id }}
    @endif

    {{-- اگر فرزند دارد بازگشتی تکرار کن --}}
    @if (count($children))
        @include('SimpleWorkflowView::Core.Task.tree1', [
            'children' => $children,
            'task' => $child,
        ])
    @endif
@endforeach
