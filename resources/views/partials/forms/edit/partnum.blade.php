<script>
    
    $(document).ready(function() {
        // Автозаполнение по партномеру
        $("#partnum").autocomplete({
        source: function (request, response) {
            $.ajax({
                url: "{{ route('api.components.search') }}", // Новый маршрут для поиска по партномеру
                dataType: "json",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'GET',
                data: {
                    query: request.term, // Передача введенного текста
                    filter: 'partnum' // Передача введенного текста
                },
                success: function (data) {
                //    console.log(data);
                    response($.map(data, function (item) {
                        return {
                            label: item.partnum, // Партномер
                            value: item.partnum, // Партномер
                            name: item.name, // Нвие компонента
                        };
                    }));
                }, 
            });
        },
        minLength: 2, // Минимальное количество символов перед запросом
        select: function (event, ui) {
            // При выборе элемента из списка установить значения
            $('#name').val(ui.item.name); // Установить наименование компонента
            $('#{{ $fieldname }}').val(ui.item.value); // Установить наименование компонента
        }
    });
});
</script>
<!-- Partnum -->
<div class="form-group {{ $errors->has('partnum') ? ' has-error' : '' }}">
    <label for="{{ $fieldname }}" class="col-md-3 control-label">Партийный номер</label>
    <div class="col-md-7 col-sm-12">
        <input class="form-control" required type="text" name="{{ $fieldname }}" id="{{ $fieldname }}" value="{{ old((isset($old_val_name) ? $old_val_name : $fieldname), $item->partnum) }}"{{  (Helper::checkIfRequired($item, 'partnum')) ? ' required' : '' }} maxlength="191" />
        {!! $errors->first('partnum', '<span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i> :message</span>') !!}
    </div>
</div>
