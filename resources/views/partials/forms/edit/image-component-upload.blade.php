<!-- Загруженные изображения -->
@if (isset($item) && ($item->image))
<div class="form-group">
    <label class="col-md-3 control-label">Загруженные изображения</label>
    <div class="col-md-9">
        <div class="row">
            @foreach ($item->image as $index => $image)
                <div class="col-md-3 image-wrapper" style="margin-bottom: 15px;">
                    <img src="{{ Storage::disk('public')->url($image_path . e($image)) }}" 
                         class="img-responsive thumbnail" 
                         style="max-height: 200px; max-width: 200px; min-height: 200px; min-width: 200px; cursor: pointer;" 
                         data-toggle="modal" 
                         data-target="#imageModal" 
                         data-image="{{ Storage::disk('public')->url($image_path . e($image)) }}">
                    <label class="form-control">
                        {{ Form::checkbox('image_delete[]', $image, false, ['aria-label' => 'delete_image_' . $index]) }}
                        {{ trans('general.image_delete') }}
                    </label>
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Модальное окно для увеличения изображения -->
<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">Просмотр изобоажения</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" class="img-fluid" alt="" style="max-width: 100%; cursor: pointer;" >
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        $('.image-wrapper img').on('click', function() {
            var imageSrc = $(this).data('image');
            $('#modalImage').attr('src', imageSrc);
           
        });
    });
</script>
@endif

<!-- Загрузка изображений -->
<div class="form-group {{ $errors->has((isset($fieldname) ? $fieldname : 'image')) ? 'has-error' : '' }}">
    <label class="col-md-3 control-label" for="{{ (isset($fieldname) ? $fieldname : 'image') }}">{{ trans('general.image_upload') }}</label>
    <div class="col-md-9">
        <input 
            type="file" 
            id="{{ (isset($fieldname) ? $fieldname : 'image') }}" 
            name="{{ (isset($fieldname) ? $fieldname : 'image') }}[]" 
            aria-label="{{ (isset($fieldname) ? $fieldname : 'image') }}" 
            multiple 
            class="form-control">

        <p class="help-block">
            {{ trans('general.image_filetypes_help', ['size' => Helper::file_upload_max_size_readable()]) }} {{ $help_text ?? '' }}
        </p>

        {!! $errors->first('image', '<span class="alert-msg">:message</span>') !!}
    </div>
</div>

<!-- Превью загруженных изображений -->
<div class="form-group">
    <label class="col-md-3 control-label"></label>
    <div class="col-md-9" id="uploadedImagesPreview" style="display: flex; flex-wrap: wrap; gap: 10px;">
        <!-- JavaScript добавит сюда превью -->
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const uploadInput = document.getElementById('{{ (isset($fieldname) ? $fieldname : 'image') }}');
        const previewContainer = document.getElementById('uploadedImagesPreview');

        uploadInput.addEventListener('change', function () {
            previewContainer.innerHTML = ''; // Очистить предыдущие превью
            Array.from(this.files).forEach(file => {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.style.maxWidth = '100px';
                    img.style.marginRight = '10px';
                    previewContainer.appendChild(img);
                };
                reader.readAsDataURL(file);
            });
        });
    });
</script>
