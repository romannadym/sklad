<!-- Подключение jQuery UI (если не подключено) -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<style>
    /* Стили для списка автозаполнения */
    .ui-autocomplete {
        max-height: 200px; /* Максимальная высота списка */
        overflow-y: auto; /* Включить вертикальную прокрутку */
        overflow-x: hidden; /* Скрыть горизонтальную прокрутку */
        z-index: 1000; /* Обеспечить, чтобы выпадающий список был над другими элементами */
    }
</style>
<script>

    $(document).ready(function() {



        $("#name").autocomplete({
    source: function (request, response) {
        $.ajax({
            url: "{{ route('api.components.search') }}", // Новый маршрут для поиска
            dataType: "json",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'GET',
            data: {
                query: request.term, // Передача введенного текста
                filter: 'name',
            },
            success: function (data) {
           //     console.log(data)
                response($.map(data, function (item) {
                    
                    return {
                        label: item.name,
                        value: item.name,
                        partnum: item.partnum // Сохраняем номер как часть объекта
                    };
                }));
            },
        });
    },
    minLength: 2, // Минимальное количество символов перед запросом
    select: function (event, ui) {
        // При выборе элемента из списка, установить значение в скрытое поле
        $('#partnum').val(ui.item.partnum); // Здесь используется переменная $fieldname
    }
});
    });
</script>
<!-- Name -->
<div class="form-group {{ $errors->has('name') ? ' has-error' : '' }}">
    <label for="name" class="col-md-3 control-label">{{ $translated_name }}</label>
    <div class="col-md-8 col-sm-12">
        <input class="form-control" style="width:100%;" type="text" name="name" aria-label="name" id="name" value="{{ old('name', $item->name) }}"{!!  (Helper::checkIfRequired($item, 'name')) ? ' required' : '' !!} maxlength="191" />
        {!! $errors->first('name', '<span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i> :message</span>') !!}
    </div>
</div>
