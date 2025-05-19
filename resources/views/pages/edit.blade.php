@extends('adminlte::page')

@section('title', ucwords(str_replace('-', ' ', $page->slug)))

@section('content_header')
<h1>Edit {{ ucwords(str_replace('-', ' ', $page->slug)) }}</h1>
@stop

@section('content')
<div class="container my-5">
    @foreach ($errors->all() as $error)
            <div class="mt-3 alert alert-danger"><li>{{ $error }}</li>
            </div>
        @endforeach
    @if(session('success'))
        <div class="mt-3 alert alert-success alert-dismissible fade show">{{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
        </div>
    @endif
    <div class="">
        <div class="card card-outline card-info">
            {{-- <div class="card-header">
                <h3 class="card-title">
                    Enter content here to be displayed on the frontend. Your changes will be saved and shown live once published.
                </h3>
            </div> --}}
            <!-- /.card-header -->
            <div class="card-body">
                <form id='pageform' action="{{ route('pages.update', $page->slug) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <div id="editor" style="height: 400px;">{!! $page->content !!}</div>
                        <textarea name="content" id="content" style="display:none;"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Update</button>
                </form>
            </div>
        </div>
    </div>
</div>
@stop

@section('css')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script> console.log("Hi, I'm using the Laravel-AdminLTE package!"); </script>
    <script
			  src="https://code.jquery.com/jquery-3.7.1.slim.js"
			  integrity="sha256-UgvvN8vBkgO0luPSUl2s8TIlOSYRoGFAX4jlCIm9Adc="
			  crossorigin="anonymous"></script>
<script>
    var toolbarOptions = [
        ['bold', 'italic', 'underline', 'strike'],        // toggled buttons
        [{ 'header': 1 }, { 'header': 2 }],               // custom button values
        [{ 'list': 'bullet' }],                             // lists
        ['blockquote', 'code-block'],                      // blocks
        [{ 'color': [] }],          // dropdown with defaults from theme
        [{ 'align': [] }],                                // text alignment
    ];

        var quill = new Quill('#editor', {
            theme: 'snow',
            modules: {
            toolbar: toolbarOptions
        }
        });
        console.log(document.getElementById('pageform'));

        // Populate hidden textarea with Quill content before form submission
    var form = document.getElementById('pageform');
    form.onsubmit = function() {
        var content = document.querySelector('textarea#content');
        content.value = quill.root.innerHTML; // Ensure this is correct
    };
    </script>
@stop