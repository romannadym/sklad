@extends('layouts/default')

{{-- Page title --}}
@section('title')
{{ trans('Заявки на компоненты') }}
@parent
@stop

@section('header_right')
  @can('create', \App\Models\Ticket::class)
    <a href="{{ route('tickets.create') }}" {{$snipeSettings->shortcuts_enabled == 1 ? "accesskey=n" : ''}} class="btn btn-primary pull-right"> {{ trans('general.create') }}</a>
  @endcan
@stop

{{-- Page content --}}
@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="box box-default">
      <div class="box-body">
        <table
        data-columns="{{ \App\Presenters\TicketPresenter::dataTableLayout() }}"
        data-cookie-id-table="TicketsTable"
        data-pagination="true"
        data-id-table="TicketsTable"
        data-search="true"
        data-side-pagination="server"
        data-show-columns="true"
        data-show-fullscreen="true"
        data-show-export="true"
        data-show-footer="true"
        data-show-refresh="true"
        data-sort-order="asc"
        data-sort-name="name"
        id="TicketsTable"
        class="table table-striped snipe-table"
        data-url="{{ route('api.tickets.index') }}"
        data-export-options='{
        "fileName": "export-tickets-{{ date('Y-m-d') }}",
        "ignoreColumn": ["actions" ]
        }'>
</table>
</div><!-- /.box-body -->
</div><!-- /.box -->
</div>
</div>

@stop

@section('moar_scripts')
@include ('partials.bootstrap-table', ['exportFile' => 'tickets-export', 'search' => true, 'showFooter' => true, 'columns' => \App\Presenters\TicketPresenter::dataTableLayout()])
