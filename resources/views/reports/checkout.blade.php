@extends('layouts/default')

{{-- Page title --}}
@section('title')
Отчет об отправленных запчастях
@parent
@stop

@section('header_right')
    {{ Form::open(['method' => 'post', 'class' => 'form-horizontal']) }}
    {{csrf_field()}}
    <button type="submit" class="btn btn-default">
        <x-icon type="download" />
        {{ trans('general.download_all') }}
    </button>
    {{ Form::close() }}
@stop

{{-- Page content --}}
@section('content')

<div class="row">
  <div class="col-md-12">
  <div class="box box-default">
  <div class="box-body">
<table
                    data-cookie-id-table="reportsCheckedoutTable"
                    data-pagination="true"
                    data-id-table="reportsCheckedoutTable"
                    data-search="true"
                    data-side-pagination="server"  
                    data-show-columns="true"
                    data-show-export="true"
                    data-show-footer="true"
                    data-show-refresh="true"
                    data-sort-order="asc"
                    data-sort-name="name"
                    id="reportsCheckedoutTable"
                    class="table table-striped snipe-table"
                    data-url="{{ route('api.checkout.index')}}"
                    data-export-options='{
                "ignoreColumn": ["actions","change","checkbox","checkincheckout","icon"]
                }'>
              <thead>
              <tr>
                <th data-searchable="false" data-sortable="false" data-field="name" data-formatter="hardwareLinkFormatter">
                <div class="columns" style="display: flex; gap: 5px; align-items: center;">
                    {{ trans('general.asset') }}
                    <select name="name" data-filter="assetName" class="form-control"> 
                    <option value=""></option>
                      @foreach ($assetName as $name)
                          <option value="{{ $name }}">{{ $name }}</option>
                      @endforeach
                        <!-- Добавьте больше опций по мере необходимости -->
                    </select>
                </div>
                  
                </th>
                <th data-searchable="false" data-sortable="false" data-field="component_name" >
                  
                  <div class="columns" style="display: flex; gap: 5px; align-items: center;">
                  Компонент
                    <select name="component_name" class="form-control" data-filter="component_name">
                    <option value=""></option>
                      @foreach ($componentName as $name)
                          <option value="{{ $name }}">{{ $name }}</option>
                      @endforeach
                        <!-- Добавьте больше опций по мере необходимости -->
                    </select>
                </div>
                </th>
                <th data-searchable="false" data-sortable="false" data-field="qty">
                  {{ trans('general.qty') }}
                </th>
                <th data-searchable="false" data-sortable="false" data-field="note">
                <div class="columns" style="display: flex; gap: 5px; align-items: center;">
                  {{ trans('general.notes') }}
                  </div>
                </th>
                <th data-searchable="false" data-sortable="false" data-field="ticketnum">
                <div class="columns" style="display: flex; gap: 5px; align-items: center;">
                {{ trans('Номер заявки') }}
                      <input name="ticketnum" type="text" class="form-control" data-filter="ticketnum"/>
                     
                  </div>
                  
                  
                </th>
                <th data-searchable="false" data-sortable="false" data-field="assigned_to_username" >
                  {{ trans('Инженер') }}
                </th>
                <th data-searchable="false" data-sortable="false" data-field="created_at" data-formatter="dateDisplayFormatter">
                  <div class="columns" style="display: flex; gap: 5px; align-items: center;">
                  {{ trans('general.date') }}
                      <input name="dateStart" type="date" class="form-control" data-filter="dateStart"/>
                      <input name="dateEnd" type="date" class="form-control" data-filter="dateEnd"/>
                     
                  </div>
                 
                </th>
              </tr>
              </thead>
            </table>
</div>
</div>
</div>
</div>
            @include ('partials.bootstrap-table', ['exportFile' => 'component-export', 'search' => false])
<script>
/*
$.ajax({
            url: "{{ route('api.checkout.index') }}", // Новый маршрут для поиска
            dataType: "json",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'GET',
            data: {
              //  query: request.term, // Передача введенного текста
              //  filter: 'name',
            },
            success: function (data) {
               console.log(data)
              
            },
        });*/

</script>
@stop
