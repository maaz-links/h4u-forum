@extends('adminlte::page')

@section('title', 'Mail Configuration')

@section('content_header')
    <h1>Mail Configuration</h1>
@stop

@section('content')
<div class="container">
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    @if($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    <form method="POST" action="{{ route('mail-config.update') }}">
        @csrf
        @method('PUT')

        <div class="card mb-4">
            <div class="card-header">Support Email Address</div>
            <div class="card-body">
                <div class="form-group">
                    <label for="h4u.email.support_address">Support Email Address</label>
                    <input type="text" class="form-control" id="h4u.email.support_address" name="h4u_email_support_address" 
                           value="{{ config('h4u.email.support_address') }}" required>
                </div>
            </div>
        </div>
        <div class="alert alert-info">
            Mail Settings are sometimes cached by the queue. Therefore, Server queue should be restarted in order for changes to take effect.
        </div>
        <div class="card mb-6">
            <div class="card-header">General Mail Settings</div>
            <div class="card-body">
                <div class="form-group">
                    <label for="mail.default">Default Mailer</label>
                    <input type="text" class="form-control" id="mail.default" name="mail_default" 
                           value="{{ config('mail.default') }}" required>
                </div>
                
                <div class="form-group">
                    <label for="mail.from.address">From Address</label>
                    <input type="email" class="form-control" id="mail.from.address" name="mail_from_address" 
                           value="{{ config('mail.from.address') }}" required>
                </div>
                
                <div class="form-group">
                    <label for="mail.from.name">From Name</label>
                    <input type="text" class="form-control" id="mail.from.name" name="mail_from_name" 
                           value="{{ config('mail.from.name') }}" required>
                </div>
            </div>
        </div>
        
        <div class="card mb-4">
            <div class="card-header">SMTP Settings</div>
            <div class="card-body">
                <div class="form-group">
                    <label for="mail.mailers.smtp.host">SMTP Host</label>
                    <input type="text" class="form-control" id="mail.mailers.smtp.host" name="mail_smtp_host" 
                           value="{{ config('mail.mailers.smtp.host') }}" required>
                </div>
                
                <div class="form-group">
                    <label for="mail.mailers.smtp.port">SMTP Port</label>
                    <input type="number" class="form-control" id="mail.mailers.smtp.port" name="mail_smtp_port" 
                           value="{{ config('mail.mailers.smtp.port') }}" required>
                </div>

                <div class="form-group">
                    <label for="mail.mailers.smtp.encryption">SMTP Encryption</label>
                    <input type="text" class="form-control" id="mail.mailers.smtp.encryption" name="mail_smtp_encryption" 
                           value="{{ config('mail.mailers.smtp.encryption') }}">
                </div>
                
                <div class="form-group">
                    <label for="mail.mailers.smtp.username">SMTP Username</label>
                    <input type="text" class="form-control" id="mail.mailers.smtp.username" name="mail_smtp_username" 
                           value="{{ config('mail.mailers.smtp.username') }}">
                </div>
                
                <div class="form-group">
                    <label for="mail.mailers.smtp.password">SMTP Password</label>
                    <input type="text" class="form-control" id="mail.mailers.smtp.password" name="mail_smtp_password" 
                           value="{{ config('mail.mailers.smtp.password')}}">
                </div>
            </div>
        </div>
        
        <button type="submit" class="btn btn-primary">Save Changes</button>
    </form>
</div>
@endsection
@section('css')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .badge {
            font-size: 0.85em;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.03);
        }
    </style>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>
@stop
