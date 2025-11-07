@props(['type' => 'success', 'message' => '', 'dismissible' => true])

<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;">
    <div class="toast show align-items-center text-white bg-{{ $type }} border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                @if($type === 'success')
                    <i class="bi bi-check-circle-fill me-2"></i>
                @elseif($type === 'danger')
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                @elseif($type === 'warning')
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                @elseif($type === 'info')
                    <i class="bi bi-info-circle-fill me-2"></i>
                @endif
                {{ $message }}
            </div>
            @if($dismissible)
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            @endif
        </div>
    </div>
</div>

