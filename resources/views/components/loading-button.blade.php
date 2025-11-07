@props(['text' => 'Submit', 'id' => 'submitBtn'])

<button type="submit" class="btn btn-primary {{ $attributes->get('class') }}" id="{{ $id }}" {{ $attributes->except('class') }}>
    <span class="spinner-border spinner-border-sm d-none me-2" id="{{ $id }}Spinner" role="status" aria-hidden="true"></span>
    <span id="{{ $id }}Text">{{ $text }}</span>
</button>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const submitBtn = document.getElementById('{{ $id }}');
    const spinner = document.getElementById('{{ $id }}Spinner');
    const text = document.getElementById('{{ $id }}Text');
    
    if (form && submitBtn) {
        form.addEventListener('submit', function() {
            if (spinner) spinner.classList.remove('d-none');
            if (text) text.textContent = 'Processing...';
            submitBtn.disabled = true;
            submitBtn.classList.add('btn-loading');
        });
    }
});
</script>

