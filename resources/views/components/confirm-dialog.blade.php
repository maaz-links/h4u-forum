@props([
    //'formId' => '',
    'buttonText' => 'Confirm',
    'cancelText' => 'Cancel',
    'title' => 'Confirmation Required',
    'message' => 'Are you sure you want to perform this action?',
    'inputLabel' => 'Reason',
    'inputName' => 'admin_reason',
    'inputPlaceholder' => 'Enter reason for this action',
    'buttonClass' => 'btn btn-primary',
    'value' => ''
])

<script>
function confirmWithReason(formId = '') {
   
    Swal.fire({
        title: '{{ $title }}',
        html: `
            <p>{{ $message }}</p>
            <input id="reason" class="swal2-input" placeholder="{{ $inputPlaceholder }}">
        `,
        showCancelButton: true,
        confirmButtonText: '{{ $buttonText }}',
        cancelButtonText: '{{ $cancelText }}',
        preConfirm: () => {
            const reason = Swal.getPopup().querySelector('#reason').value;
            if (!reason) {
                Swal.showValidationMessage('{{ $inputLabel }} is required');
                return false;
            }
            return reason;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            console.log(formId);
            const form = document.getElementById(formId);
            console.log(form);
            if (form) {
                // Remove any existing reason input first
                const existingInput = form.querySelector('input[name="{{ $inputName }}"]');
                if (existingInput) existingInput.remove();
                
                // Add new reason input
                const reasonInput = document.createElement('input');
                reasonInput.type = 'hidden';
                reasonInput.name = '{{ $inputName }}';
                reasonInput.value = result.value;
                form.appendChild(reasonInput);
                
                // Submit the form
                form.submit();
            }
        }
    });
}
</script>