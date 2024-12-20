<!-- begin redirect submit options -->
@props([
    'index_route',
    'button_label',
    'disabled_select' => false,
    'options' => [],
])

<div class="box-footer">
    <div class="row">

        <div class="col-md-3">
            <a class="btn btn-link" href="{{ $index_route ? route($index_route) : url()->previous() }}">{{ trans('button.cancel') }}</a>
        </div>

        <div class="col-md-9 text-right">
            <div class="btn-group text-left">

                @if (($options) && (count($options) > 0))
                <select class="redirect-options form-control select2" data-minimum-results-for-search="Infinity" name="redirect_option" style="min-width: 250px"{{ ($disabled_select ? ' disabled' : '') }}>
                    @foreach ($options as $key => $value)
                        <option value="{{ $key }}"{{ Session::get('redirect_option') == $key ? ' selected' : ''}}>
                            {{ $value }}
                        </option>
                    @endforeach
                </select>
                @endif

                <button type="submit" class="btn btn-primary pull-right{{ ($disabled_select ? ' disabled' : '') }}" style="margin-left:5px; border-radius: 3px;"{!! ($disabled_select ? ' data-tooltip="true" title="'.trans('admin/hardware/general.edit').'" disabled' : '') !!}>
                    <x-icon type="checkmark" />
                    {{ $button_label }}
                </button>
                @if(isset($options['print']) && $options['print'] == true)
                <a href="#" class="btn btn-primary pull-right" id="print" style="margin-left:5px; border-radius: 3px;" >
                    <x-icon type="print" />
                </a>   
                <script>
                    $(document).ready(function(){
                        $('#print').click(function(event){
                            openPrintWindow();
                        });
                        function openPrintWindow() {
                            // Данные для печати
                            const printContent = `
                                <html>
                                <head>
                                    <title>Печать</title>
                                    <style>
                                        body { font-family: Arial, sans-serif; margin: 54px; }
                                        table { border-collapse: collapse; width: 100%; }
                                        th, td { border: 1px solid black; padding: 8px; text-align: left; font-size:42px; }
                                        .header { font-size: 24px; margin-bottom: 20px; }
                                    </style>
                                </head>
                                <body>
                                    <div class="header"></div>
                                    <table>
                                        <tr>
                                            <th>Партийный номер</th>
                                            <th>Имя</th>
                                            <th>Поставка</th>
                                            <th>Кол-во</th>
                                        </tr>
                                        <tr>
                                            <td>${$('#partnum').val() || 'N/A'}</td>
                                            <td>${$('#name').val() || 'N/A'}</td>
                                            <td>${$('#purchase_date').val() || 'N/A'}</td>
                                            <td>${$('#qty').val() || 'N/A'}</td>
                                        </tr>
                                    </table>
                                </body>
                                </html>
                            `;

                            // Открытие нового окна
                            const printWindow = window.open();
                           
                                printWindow.document.open();
                                printWindow.document.write(printContent);
                                printWindow.document.close();
                                printWindow.print();
                        }

                    });
                </script>
                @endif
            </div><!-- /.btn-group -->
        </div><!-- /.col-md-9 -->
    </div><!-- /.row -->
</div> <!-- /.box-footer -->
<!-- end redirect submit options -->