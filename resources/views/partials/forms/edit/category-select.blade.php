<!-- Asset Model -->
<div id="{{ $fieldname }}" class="form-group{{ $errors->has($fieldname) ? ' has-error' : '' }}">

    {{ Form::label($fieldname, $translated_name, array('class' => 'col-md-3 control-label')) }}

    <div class="col-md-7">
        <select class="js-data-ajax" data-endpoint="categories/{{ (isset($category_type)) ? $category_type : 'assets' }}" data-placeholder="{{ trans('general.select_category') }}" name="{{ $fieldname }}" style="width: 100%" id="category_select_id" aria-label="{{ $fieldname }}" {!!  ((isset($item)) && (Helper::checkIfRequired($item, $fieldname))) ? ' required ' : '' !!}{{ (isset($multiple) && ($multiple=='true')) ? " multiple='multiple'" : '' }}>
            @if ($category_id = old($fieldname, (isset($item)) ? $item->{$fieldname} : ''))
                <option value="{{ $category_id }}" selected="selected" role="option" aria-selected="true"  role="option">
                    {{ (\App\Models\Category::find($category_id)) ? \App\Models\Category::find($category_id)->name : '' }}
                </option>
            @endif

        </select>
    </div>
    <div class="col-md-1 col-sm-1 text-left">
        @can('create', \App\Models\Category::class)
            @if ((!isset($hide_new)) || ($hide_new!='true'))
                <a href='{{ route('modal.show',['type' => 'category', 'category_type' => isset($category_type) ? $category_type : 'assets' ]) }}' data-toggle="modal"  data-target="#createModal" data-select='category_select_id' class="btn btn-sm btn-primary">{{ trans('button.new') }}</a>
            @endif
        @endcan
    </div>


    {!! $errors->first($fieldname, '<div class="col-md-8 col-md-offset-3"><span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i> :message</span></div>') !!}

    {!! $errors->first('category_type', '<div class="col-md-8 col-md-offset-3"><span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i> :message</span></div>') !!}
</div>

<script>
  
               // Функция для загрузки категорий
               function loadCategories() {
        var endpoint = "/api/v1/categories/component/selectlist?page=1"; // Укажите ваш API-эндпоинт
        $.ajax({
            url: endpoint,
            type: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            dataType: 'json',
            success: function(data) {
              //  console.log(data)
                var $categorySelect = $('#category_select_id');
                $categorySelect.empty(); // Очистить текущее содержимое
                $categorySelect.append($('<option>', {
                    value: '',
                    text: '{{ trans('general.select_category') }}', // Подсказка для выбора
                }));
                $.each(data.results, function(index, item) {
                    $categorySelect.append($('<option>', {
                        value: item.id,
                        text: item.text,
                    }));
                });
                // Проверка выбранной категории
            var selectedCategoryId = "{{ old($fieldname, (isset($item)) ? $item->{$fieldname} : '') }}";
        //    console.log("Selected category ID before check: ", selectedCategoryId); // Отладка
            if (selectedCategoryId) {
             //   console.log("Setting selected value to: ", selectedCategoryId); // Отладка
                $categorySelect.val(selectedCategoryId).trigger('change');
            }
            },
            error: function(xhr, status, error) {
                console.error('Error loading categories:', error);
            }
        });
    }

    // Вызов функции загрузки категорий
    loadCategories();// Загрузка категорийв списоквыборакатегорийв полеввода.

</script>