<div class="card">
    <div class="card-body">
        <div class="form-group">
            <label for="type">Type</label>
            <select name="type" id="type" class="form-control @error('type') is-invalid @enderror" required>
                <option value="keyword" {{ old('type', $rule->type ?? '') == 'keyword' ? 'selected' : '' }}>Keyword</option>
                <option value="regex" {{ old('type', $rule->type ?? '') == 'regex' ? 'selected' : '' }}>Regular Expression</option>
            </select>
            @error('type')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="form-group" id="name-field-group" style="{{ old('type', $rule->type ?? 'keyword') == 'keyword' ? 'display: none;' : '' }}">
            <label for="name">Name</label>
            <input type="text" name="name" id="name" 
                   class="form-control @error('name') is-invalid @enderror" 
                   value="{{ old('name', $rule->name ?? '') }}">
            @error('name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="form-group">
            <label for="pattern">Keyword/Pattern</label>
            <textarea name="pattern" id="pattern" rows="3" 
                      class="form-control @error('pattern') is-invalid @enderror" 
                      required>{{ old('pattern', $rule->pattern ?? '') }}</textarea>
            @error('pattern')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
            <small id='instruction-text' class="form-text text-muted">
                For regex patterns, include the delimiters (e.g., /pattern/).
            </small>
        </div>
    </div>
</div>

@section('js')
<script>
    $(document).ready(function() {
    function toggleNameField() {
        if ($('#type').val() === 'keyword') {
            $('#name-field-group').hide();
            $('#name').val('');
            $('#instruction-text').text('For exact word match, wrap keyword in double quotes (e.g., "ass") ');
        } else {
            $('#name-field-group').show();
            $('#instruction-text').text('Uses PHP regex patterns. Also include the delimiters (e.g., /pattern/).');
        }
    }
    
    // Initial toggle
    toggleNameField();
    
    // Bind to change event
    $('#type').change(toggleNameField);
});
</script>
@stop