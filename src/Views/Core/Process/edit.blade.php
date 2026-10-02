@extends('behin-layouts.app')

@section('content')
<div class="container">
    <form method="POST" action="{{ route('simpleWorkflow.process.update', $process->id) }}">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="name">{{ trans('Name') }}</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ $process->name }}">
        </div>
        <div class="form-group">
            <label for="category">{{ trans('Category') }}</label>
            <input type="text" class="form-control" id="category" name="category" value="{{ $process->category }}">
        </div>
        <div class="form-group">
            <label for="case_prefix">{{ trans('Case Prefix') }}</label>
            <input type="text" class="form-control" id="case_prefix" name="case_prefix" value="{{ $process->case_prefix }}">
        </div>
        <div class="form-group">
            <label for="script_before_start">{{ trans('fields.Script Before Start') }}</label>
            <div class="d-flex align-items-center">
                <select name="script_before_start" id="script_before_start" class="form-control select2">
                    <option value="">{{ trans('None') }}</option>
                    @foreach ($scripts as $script)
                        <option value="{{ $script->id }}" {{ $process->script_before_start == $script->id ? 'selected' : '' }}>
                            {{ $script->name }}
                        </option>
                    @endforeach
                </select>
                <a id="script_before_start_edit_btn" class="btn btn-sm btn-outline-primary ml-2"
                    target="_blank"
                    data-url-template="{{ route('simpleWorkflow.scripts.edit', ['script' => 'SCRIPT_ID']) }}"
                    style="display: none;">
                    {{ trans('Edit') }}
                </a>
            </div>
            <small class="form-text text-muted">
                {{ trans('fields.Script Before Start Hint') }}
            </small>
        </div>
        <button type="submit" class="btn btn-primary">{{ trans('Save') }}</button>
    </form>
</div>
@endsection

@section('script')
    <script>
        initial_view();
        (function() {
            const select = document.getElementById('script_before_start');
            const button = document.getElementById('script_before_start_edit_btn');
            if(!select || !button){
                return;
            }
            const toggle = function(){
                const value = select.value;
                const template = button.dataset.urlTemplate;
                if(value && template){
                    button.href = template.replace('SCRIPT_ID', value);
                    button.style.display = 'inline-block';
                }else{
                    button.removeAttribute('href');
                    button.style.display = 'none';
                }
            };
            toggle();
            select.addEventListener('change', toggle);
        })();
    </script>
@endsection
