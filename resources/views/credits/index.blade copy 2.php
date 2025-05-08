@extends('adminlte::page')

@section('title', 'Credits Configuration')

@section('content_header')
    <h1>Credits Configurations</h1>
@stop

@section('content')
<div class="container my-5">
    
    @if(session('success'))
        <div class="mt-3 alert alert-success alert-dismissible fade show">{{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
        </div>
    @endif

    <div class='mt-3'>
        <div class="container col-6">
            <div class="card p-3">
                <h3 class="mb-5">Change Credit cost to unlock chat</h3>
                <form id="creditsForm" action="{{ route('credits.store') }}" method="POST">
                    @csrf
                    @php
                        $profileFields = [
                            [
                                'label' => 'Standard Profile',
                                'name' => 'standard',
                                'value' => config('h4u.chatcost.standard')
                            ],
                            [
                                'label' => 'Verified Profile',
                                'name' => 'verified',
                                'value' => config('h4u.chatcost.verified')
                            ],
                            [
                                'label' => 'Top Profile',
                                'name' => 'topprofile',
                                'value' => config('h4u.chatcost.topprofile')
                            ],
                            [
                                'label' => 'Verified Top Profile',
                                'name' => 'verified_topprofile',
                                'value' => config('h4u.chatcost.verified_topprofile')
                            ]
                        ];
                    @endphp

                    @foreach($profileFields as $field)
                        <div class="form-group row mb-3">
                            <label class="col-sm-5 col-form-label text-end">{{ $field['label'] }}</label>
                            <div class="col-sm-3">
                                <input type="number" 
                                    min="0" 
                                    name="{{ $field['name'] }}" 
                                    class="form-control" 
                                    value="{{ $field['value'] }}" 
                                    required>
                            </div>
                            <span class="col-sm-3 col-form-label">credits</span>
                        </div>
                    @endforeach
                    
                    <div class="text-center">
                        <button type="button" onclick="submitWithConfirmation('amogus')" class="btn px-3 btn-success">Save</button>
                    </div>
                </form>
            </div>
            @foreach ($errors->all() as $error)
                <div class="mt-3 alert alert-danger"><li>{{ $error }}</li>
                </div>
            @endforeach
        </div>
    </div>
</div>
@stop

@section('css')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>
<script
  src="https://code.jquery.com/jquery-3.7.1.slim.js"
  integrity="sha256-UgvvN8vBkgO0luPSUl2s8TIlOSYRoGFAX4jlCIm9Adc="
  crossorigin="anonymous"></script>

<script>
function submitWithConfirmation(value1) {
    Swal.fire({
        title: 'Update Credit Costs',
        html: `
            <p>Enter reason to perform this action ${value1}</p>
            <input id="reason" class="swal2-input">
        `,
        // icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Confirm',
        cancelButtonText: 'Cancel',
        preConfirm: () => {
            const reason = Swal.getPopup().querySelector('#reason').value;
            if (!reason) {
                Swal.showValidationMessage(`Please enter a reason`);
                return false;
            }
            return reason;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Create a hidden input for the reason
            const reasonInput = document.createElement('input');
            reasonInput.type = 'hidden';
            reasonInput.name = 'admin_reason';
            reasonInput.value = result.value;
            
            // Append to form
            const form = document.getElementById('creditsForm');
            form.appendChild(reasonInput);
            
            // Submit the form
            form.submit();
        }
    });
}
</script>
@stop