@extends('admin.layout.master')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('public/assets/libs/dropzone/dropzone.css') }}">
<link href="{{ asset('public/assets/plugin/tagify/tagify.css') }}" rel="stylesheet" type="text/css" />
<script src="{{ asset('public/assets/js/ckeditor/ckeditor.js') }}"></script>

@endpush

@section('content')
<!-- Page Header -->
<div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
    <a class="btn btn-dark" href="{{ url()->previous() }}">Back</a>
    <div class="ms-md-1 ms-0">
        <nav>
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin-dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ ucfirst($action) }} Product Detail Section</li>
            </ol>
        </nav>
    </div>
</div>
<!-- Page Header Close -->
<div class="row">
    <div class="col-xl-12">
        <form action="" method="post" enctype="multipart/form-data" id="createBlogForm">
            @csrf
            <div class="card custom-card">
                <div class="card-header">
                    <div class="card-title">
                        Create Product Detail Section
                    </div>
                </div>
                <div class="card-body add-products p-0">
                    <div class="p-4">
                        <div class="row gx-5">
                            <div class="col-xxl-12 col-xl-12 col-lg-12 col-md-12">
                                <div class="card custom-card shadow-none mb-0 border-0">
                                    <div class="card-body p-0">
                                        <div class="row gy-3">

                                            <div class="col-xl-6">
                                                <label for="section_name" class="form-label"><span class="text-danger">*
                                                    </span>Section Name</label>
                                                <input type="text"
                                                    class="form-control @error('section_name') is-invalid @enderror" id="section_name"
                                                    name="section_name"
                                                    value="{{isset($productDetailManager->section_name) ? $productDetailManager->section_name: old('section_name')}}"
                                                    placeholder="Section Name">
                                                @if ($errors->has('section_name'))
                                                <div class=" invalid-feedback">
                                                    {{ $errors->first('section_name') }}
                                                </div>
                                                @endif
                                            </div>

                                            <div class="col-xl-6">
                                                <label for="field_type" class="form-label">Field Type</label>
                                                <select name="field_type" id="field_type" class="form-control @error('field_type') is-invalid @enderror select2init">
                                                    <option value="ckeditor" {{ (isset($productDetailManager->field_type) && $productDetailManager->field_type == 'ckeditor') ? 'selected' : '' }}>CK Editor</option>
                                                    <option value="textbox" {{ (isset($productDetailManager->field_type) && $productDetailManager->field_type == 'textbox') ? 'selected' : '' }}>TextBox</option>
                                                    <option value="textarea" {{ (isset($productDetailManager->field_type) && $productDetailManager->field_type == 'textarea') ? 'selected' : '' }}>Textarea</option>
                                                    
                                                    @if ($errors->has('field_type'))
                                                    <div class=" invalid-feedback">
                                                        {{ $errors->first('field_type') }}
                                                    </div>
                                                    @endif
                                                </select>
                                            </div>

                                            <div class="col-xl-12">
                                                <label for="content" class="form-label"><span class="text-danger">*
                                                    </span>Sample Preview</label>
                                                <div class="content-input-field" style="display:none;">
                                                    <input type="text"
                                                        class="form-control @error('content') is-invalid @enderror ContentText"
                                                        id="contentTextbox"
                                                        name="content"
                                                        value="{{isset($productDetailManager->content) ? $productDetailManager->content: old('content')}}"
                                                        placeholder="Sample Preview"
                                                        style="height: 100px;" disabled>
                                                </div>

                                                <div class="content-textarea-field" style="display:none;">
                                                    <textarea 
                                                        class="form-control @error('content') is-invalid @enderror ContentTextArea"
                                                        id="contentTextarea"
                                                        name="content"
                                                        rows="4"
                                                        placeholder="Sample Preview"
                                                        style="height: 100px;" disabled>{{ isset($productDetailManager->content) ? $productDetailManager->content : old('content') }}</textarea>
                                                </div>

                                                <div class="content-ckeditor-field" style="display:none;">
                                                    <textarea 
                                                        class="form-control ck_content @error('content') is-invalid @enderror ContentCkeditor"
                                                        id="contentCkeditor"
                                                        name="content"
                                                        rows="4"
                                                        placeholder="Sample Preview"
                                                        style="height: 100px;" disabled>{{ isset($productDetailManager->content) ? $productDetailManager->content : old('content') }}</textarea>
                                                </div>

                                                @if ($errors->has('content'))
                                                <div class=" invalid-feedback">
                                                    {{ $errors->first('content') }}
                                                </div>
                                                @endif
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="px-4 py-3 border-top border-block-start-dashed d-sm-flex justify-content-end">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>


        </form>
    </div>
</div>

@endsection

@push('scripts')
<!-- Select2 Cdn -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{ asset('public/assets/plugin/tagify/tagify.min.js') }}"></script>

<!-- Internal Select-2.js -->
<script src="{{ asset('public/assets/js/select2.js') }}"></script>
<script src="{{ asset('public/assets/libs/dropzone/dropzone-min.js') }}"></script>
<script src="{{ asset('public/assets/js/custom/product.js') }}"></script>
<script src="{{ asset('public/assets/plugin/jquery-validation/jquery.validate.min.js') }}"></script>
<script src="{{ asset('public/assets/js/repeater.js')}}"></script>
<script>
    function initContentField() {
        var selectedValue = $('#field_type').val();

        $('.content-input-field, .content-textarea-field, .content-ckeditor-field').hide().find('input, textarea').prop('disabled', true);

        if (selectedValue === 'textbox') {
            $('.content-input-field').show().find('input').prop('disabled', false);
        } else if (selectedValue === 'textarea') {
            $('.content-textarea-field').show().find('textarea').prop('disabled', false);
        } else if (selectedValue === 'ckeditor') {
            $('.content-ckeditor-field').show().find('textarea').prop('disabled', false);
            if (typeof CKEDITOR !== 'undefined' && !CKEDITOR.instances['contentCkeditor']) {
                CKEDITOR.replace('contentCkeditor', {
                    filebrowserUploadUrl: '{{ url('base/uploder') }}',
                    enterMode: CKEDITOR.ENTER_BR
                });
                CKEDITOR.config.allowedContent = true;
            }
        }
    }

    $(document).ready(function() {
        $('#field_type').on('change', initContentField);
        initContentField();
    });
</script>

@endpush