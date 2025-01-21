@extends('layouts/default')

{{-- Page title --}}
@section('title')
Отчет об остатках
@parent
@stop

@section('header_right')
    {{ Form::open(['method' => 'post', 'class' => 'form-horizontal']) }}
    {{csrf_field()}}
    {{ Form::close() }}
@stop

{{-- Page content --}}
@section('content')

<div class="row">
  <div class="col-md-12">
  <div class="box box-default">
  <div class="box-body">
<table
                    data-cookie-id-table="reportsComponentsTable"
                    data-pagination="true"
                    data-id-table="reportsComponentsTable"
                    data-search="true"
                    data-side-pagination="server"  
                    data-show-columns="true"
                    data-show-export="true"
                    data-show-footer="true"
                    data-show-refresh="true"
                    data-sort-order="asc"
                    data-sort-name="name"
                    id="reportsComponentsTable"
                    class="table table-striped snipe-table"
                    data-url="{{ route('api.components.report')}}"
                    data-export-options='{
                "ignoreColumn": ["actions","change","checkbox","checkincheckout","icon"]
                }'>
              <thead>
              <tr>
                <th data-searchable="false" data-sortable="false" data-field="name" >
                <div class="columns" style="display: flex; gap: 5px; align-items: center;">
                    Наименование
                    <select name="name" data-filter="name" class="form-control"> 
                    <option value=""></option>
                      @foreach ($name as $n)
                          <option value="{{ $n }}">{{ $n }}</option>
                      @endforeach
                        <!-- Добавьте больше опций по мере необходимости -->
                    </select>
                </div>
                    
                </th>
                <th data-searchable="false" data-sortable="false" data-field="partnum" >
                <div class="columns" style="display: flex; gap: 5px; align-items: center;">
                    Партномер
                    <select name="partnum" data-filter="partnum" class="form-control"> 
                    <option value=""></option>
                      @foreach ($partnum as $p)
                          <option value="{{ $p }}">{{ $p }}</option>
                      @endforeach
                        <!-- Добавьте больше опций по мере необходимости -->
                    </select>
                </div>
                  
                </th>
                <th data-searchable="false" data-sortable="false" data-field="qty">
                  {{ trans('general.qty') }}
                </th>
               
              </tr>
              </thead>
            </table>
</div>
</div>
</div>
</div>
            @include ('partials.bootstrap-table', ['exportFile' => 'component-export', 'search' => false])
@stop
