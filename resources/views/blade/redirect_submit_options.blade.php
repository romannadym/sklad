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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Template</title>
   <link href="https://fonts.googleapis.com/css2?family=PT+Sans:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'PT Sans', sans-serif;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f9f9f9;
            font-size: 48px;
        }
        .container {
            border: 1px solid #ccc;
            width: 70%;
            height: 70%;
            background-color: #ffffff;
            border-radius: 5px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }
        .item {
            margin-bottom: 20px;
        }
        .label {
            font-weight: bold;
            font-size: 1.2em;
            margin-bottom: 5px;
        }
       
    </style>
</head>
<body>
    <div class="container">
        <div class="item">
            <span class="value" id="part-number">${$('#partnum').val() || 'N/A'}</span>
        </div>
        <div class="item">
            <span class="value" id="name">${$('#name').val() || 'N/A'}</span>
        </div>
        <div class="item">
            <span class="value" id="delivery-date">${$('#purchase_date').val() || 'N/A'}</span>
        </div>
        <div class="item">
            <span class="value" id="total">${$('#qty').val() || 'N/A'}</span>
            <span>шт.</span>
        </div>
    </div>
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