@extends('layouts/default')

{{-- Page title --}}
@section('title')
 {{ trans('admin/components/general.checkout') }}
@parent
@stop

{{-- Page content --}}
@section('content')

<div class="row">
  <div class="col-md-8">
    <form class="form-horizontal" id="checkout_form" method="post" action="" autocomplete="off">
      <!-- CSRF Token -->
      {{ csrf_field() }}

      <div class="box box-default">
        @if ($component->id)
        <div class="box-header with-border">
          <div class="box-heading">
            <h2 class="box-title">{{ $component->name }}  ({{ $component->numRemaining()  }}  {{ trans('admin/components/general.remaining') }})</h2>
          </div>
        </div><!-- /.box-header -->
        @endif

        <div class="box-body">
          <!-- Asset -->
            @include ('partials.forms.edit.asset-select', ['translated_name' => trans('general.select_asset'), 'fieldname' => 'asset_id'])
            @include ('partials.forms.edit.user-select', ['translated_name' => trans('Инженер'), 'fieldname' => 'assigned_to_user_id', 'label' => trans('Инженер')])
            <div class="form-group {{ $errors->has('assigned_qty') ? ' has-error' : '' }}">
              <label for="assigned_qty" class="col-md-3 control-label">
                {{ trans('general.qty') }}
              </label>
              <div class="col-md-2 col-sm-5 col-xs-5">
                <input class="form-control required col-md-12" type="number" name="assigned_qty" min="1" max="10" id="assigned_qty" value="{{ old('assigned_qty') ?? 1 }}" oninput="this.value = Math.min({{$component->remaining}}, Math.max(0, this.value))" />
              </div>
              @if ($errors->first('assigned_qty'))
                <div class="col-md-9 col-md-offset-3">
                  {!! $errors->first('assigned_qty', '<span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i> :message</span>') !!}
                </div>
              @endif
            </div>
            <div class="form-group" >
              <label for="ticketnum" class="col-md-3 control-label">
                {{ trans('Серийный номер') }}
            </label>
              <div class="col-md-2 col-sm-5 col-xs-5"> 
                <input class="form-control required col-md-12" required  type="text" name="serial[1]" id="serial_1" value="{{ empty($component->serial) ? old('serial.1',$component->serial[1] ?? '') : $component->serial }}"
                />
              </div>
              @if (isset(session('duplicate_serials')[1]))
                <div class="col-md-9 col-md-offset-3">
                <span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i> Этот серийный номер уже используется в другом компоненте</span>
                </div>
                @endif
            </div>
            <div id="serial_numbers_container">
           
            </div>
            <div class="form-group">
              <label for="ticketnum" class="col-md-3 control-label">
                {{ trans('Номер заявки') }}
              </label>
              <div class="col-md-2 col-sm-5 col-xs-5"> 
                <input class="form-control required col-md-12" type="text" name="ticketnum" id="ticketnum" value="{{ old('ticketnum') ?? '' }}"/>
              </div>
              
            </div>
            

            <!-- Note -->
            <div class="form-group{{ $errors->has('note') ? ' error' : '' }}">
              <label for="note" class="col-md-3 control-label">{{ trans('admin/hardware/form.notes') }}</label>
              <div class="col-md-7">
                <textarea class="col-md-6 form-control" id="note" name="note" style=" resize: vertical;">{{ old('note', $component->note) }}</textarea>
                {!! $errors->first('note', '<span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i> :message</span>') !!}
              </div>
            </div>


        </div> <!-- .BOX-BODY-->
          <x-redirect_submit_options
                  index_route="components.index"
                  :button_label="trans('general.checkout')"
                  :options="[
                                'index' => trans('admin/hardware/form.redirect_to_all', ['type' => trans('general.components')]),
                                'item' => trans('admin/hardware/form.redirect_to_type', ['type' => trans('general.component')]),
                                'target' => trans('admin/hardware/form.redirect_to_checked_out_to'),

                               ]"
          />
      </div> <!-- .box-default-->
    </form>
  </div> <!-- .col-md-9-->
</div> <!-- .row -->

@stop
<script src="{{ url(asset('js/jquery.js')) }}" nonce="{{ csrf_token() }}"></script>
<script>
  $(document).ready(function () {
    function updateSerialInputs() {
        let qty = parseInt($('#assigned_qty').val()) || 1;
        let container = $('#serial_numbers_container');
        let oldSerials = @json(old('serial', $component->serial ?? [])); // Передаём массив
        let duplicateSerials = @json(session('duplicate_serials', []));
        container.empty(); // Очищаем контейнер

        for (let i = 2; i <= qty; i++) {
            let serialValue = oldSerials[i] ?? ''; // Получаем значение из переданных данных
            let isDuplicate = duplicateSerials.hasOwnProperty(i); // Проверяем, есть ли индекс в дубликатах
            let serialInput = `
            <div class="form-group">
                <label for="serial_${i}" class="col-md-3 control-label">
                    {{ trans('Серийный номер') }} #${i}
                </label>
                <div class="col-md-2 col-sm-5 col-xs-5">
                    <input class="form-control required col-md-12" type="text" name="serial[${i}]" id="serial_${i}" value="${serialValue}" required />
                </div>
                ${isDuplicate ? ` <div class="col-md-9 col-md-offset-3">
                <span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i> Этот серийный номер уже используется в другом компоненте</span>
                </div>` : ''}
            </div>
            `;
            container.append(serialInput);
        }
    }

    // Вызываем при изменении количества
    $('#assigned_qty').on('input', updateSerialInputs);

    // Инициализация при загрузке
    updateSerialInputs();
});
</script>