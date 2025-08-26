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
          @if(isset($assetId) && $assetId)
            @include ('partials.forms.edit.asset-select', [
                'translated_name' => trans('general.select_asset'),
                'fieldname' => 'asset_visible',
                'asset' => \App\Models\Asset::find($assetId), // передаем найденный asset
                'disabled' => true
            ])
            <script type="text/javascript">
            let asset_id = $('#assigned_asset_select').val();
            $('#assigned_asset').append(`
              <select class="hidden" name="asset_id">
                <option value="${asset_id}" selected="selected"></option>
              </select>
              `)
            </script>

          @else
            @include ('partials.forms.edit.asset-select', [
                'translated_name' => trans('general.select_asset'),
                'fieldname' => 'asset_id'
            ])
          @endif
          @php
              $selectedUser = isset($userId) && $userId ? \App\Models\User::find($userId) : null;
          @endphp
            @if($selectedUser)
              @include ('partials.forms.edit.user-select', [
                  'translated_name' => trans('Инженер'),
                  'fieldname' => 'assigned_to_user_visible',
                  'label' => trans('Инженер'),
                  'item' => (object)['assigned_to_user_visible' => $selectedUser ? $selectedUser->id : null],
                  'disabled' => isset($ticketId) ? true : false
              ])
              <script type="text/javascript">
              let user_id = $('#assigned_user_select').val();
              $('#assigned_user').append(`
                <select class="hidden" name="assigned_to_user_id">
                  <option value="${user_id}" selected="selected"></option>
                </select>
                `)
              </script>
            @else
              @include ('partials.forms.edit.user-select', [
                  'translated_name' => trans('Инженер'),
                  'fieldname' => 'assigned_to_user_id',
                  'label' => trans('Инженер'),
              ])
            @endif
            <div class="form-group {{ $errors->has('assigned_qty') ? ' has-error' : '' }}">
              <label for="assigned_qty" class="col-md-3 control-label">
                {{ trans('general.qty') }}
              </label>
              <div class="col-md-2 col-sm-5 col-xs-5">
                <input class="form-control required col-md-12" {{ isset($ticketId) ? 'readonly ' : '' }}  type="number" name="assigned_qty"  min="1" max="10" id="assigned_qty" value="{{ old('assigned_qty') ?? 1 }}" oninput="this.value = Math.min({{$component->remaining}}, Math.max(0, this.value))" />
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
                <input class="form-control required col-md-12" required  {{ isset($ticketId) ? '' : '' }} type="text"  name="serial[1]" id="serial_1" value="{{ empty($component->serial) ? old('serial.1',$component->serial[1] ?? '') : $component->serial }}"
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
                <input class="form-control required col-md-12" {{ isset($ticketId) ? 'readonly' : '' }}  type="text" name="ticketnum" id="ticketnum" value="{{ old('ticketnum', $ticketId ?? '') }}"/>
              </div>

            </div>


            <!-- Note -->
            <div class="form-group{{ $errors->has('note') ? ' error' : '' }}">
              <label for="note" class="col-md-3 control-label">{{ trans('admin/hardware/form.notes') }}</label>
              <div class="col-md-7">
                <textarea class="col-md-6 form-control disabled" id="note" {{ isset($ticketId) ? 'readonly' : '' }} name="note" style=" resize: vertical;">{{ old('note', $note ?? $component->note ?? '') }}</textarea>
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
