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
                    data-cookie-id-table="componentsCheckedoutTable"
                    data-pagination="true"
                    data-id-table="componentsCheckedoutTable"
                    data-search="true"
                    data-side-pagination="server"  
                    data-show-columns="true"
                    data-show-export="true"
                    data-show-footer="true"
                    data-show-refresh="true"
                    data-sort-order="asc"
                    data-sort-name="name"
                    id="componentsCheckedoutTable"
                    class="table table-striped snipe-table"
                    data-url="{{ route('api.checkout.index')}}"
                    data-export-options='{
                "ignoreColumn": ["actions","change","checkbox","checkincheckout","icon"]
                }'>
              <thead>
              <tr>
                <th data-searchable="false" data-sortable="false" data-field="name" data-formatter="hardwareLinkFormatter">
                  {{ trans('general.asset') }}
                </th>
                <th data-searchable="false" data-sortable="false" data-field="component_name" >
                  Компонент
                </th>
                <th data-searchable="false" data-sortable="false" data-field="qty">
                  {{ trans('general.qty') }}
                </th>
                <th data-searchable="false" data-sortable="false" data-field="note">
                  {{ trans('general.notes') }}
                </th>
                <th data-searchable="false" data-sortable="false" data-field="ticketnum">
                  {{ trans('Номер заявки') }}
                </th>
                <th data-searchable="false" data-sortable="false" data-field="assigned_to_username" >
                  {{ trans('Инженер') }}
                </th>
                <th data-searchable="false" data-sortable="false" data-field="created_at" data-formatter="dateDisplayFormatter">
                  {{ trans('general.date') }}
                </th>
              </tr>
              </thead>
            </table>
</div>
</div>
</div>
</div>
            @include ('partials.bootstrap-table', ['exportFile' => 'component-export', 'search' => false])
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
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