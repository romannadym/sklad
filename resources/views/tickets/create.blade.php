@extends('layouts.default')

{{-- Page title --}}
@section('title')
 Содать заявку на компонент
@parent
@stop

@section('header_right')
<a href="{{ URL::previous() }}" class="btn btn-primary pull-right">
    {{ trans('general.back') }}</a>
@stop



{{-- Page content --}}

@section('content')
<!-- Name -->
 <!-- row -->
<div class="row">
    <!-- col-md-8 -->
    <div class="col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1 col-sm-12 col-sm-offset-0">

        <form id="create-form" class="form-horizontal" method="post" action="{{ route('tickets.store') }}" autocomplete="on" role="form" enctype="multipart/form-data">

          <!-- box -->
          <div class="box box-default">

              <div class="box-header with-border">

                      @if ((isset($topSubmit) && ($topSubmit=='true')) || (isset($item->id)))

                      <div class="col-md-12 box-title text-right" style="padding: 0px; margin: 0px;">
                              <div class="col-md-9 text-left">
                                  @if ($item->id)
                                      <h2 class="box-title" style="padding-top: 8px; padding-bottom: 7px;">
                                          {{ $item->display_name }}
                                      </h2>
                                  @endif
                              </div>
                              @if (isset($topSubmit) && ($topSubmit=='true'))
                              <div class="col-md-3 text-right" style="padding-right: 10px;">
                                  <button type="submit" class="btn btn-primary pull-right">
                                      <x-icon type="checkmark" />
                                      {{ trans('general.save') }}
                                  </button>
                              </div>
                              @endif
                      </div>

                      @endif
               </div><!-- /.box-header -->
               <!-- box-body -->
               <div class="box-body">
                          <div class="form-group">
                              <label for="name" class="col-md-3 control-label"> Название актива</label>
                              <div class="col-md-8 col-sm-12">
                                  <input class="form-control" style="width:100%;" type="text" name="name" aria-label="asset_name" id="asset_name" value="" required/>
                                  {!! $errors->first('asset_name', '<span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i>Это поле не может быть пустым </span>') !!}
                              </div>
                          </div>
                          <div class="form-group">
                              <label for="name" class="col-md-3 control-label"> Серийный номер актива</label>
                              <div class="col-md-8 col-sm-12">
                                  <input class="form-control" style="width:100%;" type="text" name="asset_serial" aria-label="asset_serial" id="asset_serial" value="{{ old('asset_serial.1', $item->asset_serial) }}" required/>
                                  {!! $errors->first('partnum', '<span class="alert-msg" aria-hidden="true"><i class="fas fa-times" aria-hidden="true"></i>Это поле не может быть пустым </span>') !!}
                                  {!! $errors->first('partnums', '<span class="alert-msg"><i class="fas fa-times"></i>  Партийный номер должен быть уникальным.</span>') !!}
                                  @if($errors->has('duplicate'))
                                   <span class="alert-msg"><i class="fas fa-times"></i>  Партийный номер должен быть уникальным.</span>
                                   @endif
                             </div>



                        </div>
                        @include ('partials.forms.edit.asset-select', ['translated_name' => trans('general.asset'), 'fieldname' => 'asset_id', 'required' => true])
                        @include ('partials.forms.edit.component-select', ['translated_name' => trans('general.component'), 'fieldname' => 'component_id', 'required' => true])
                        <div style="padding-top: 30px;">
                            @if ($item->id)
                            {{ method_field('PUT') }}
                            @endif

                            <!-- CSRF Token -->
                            {{ csrf_field() }}
                            @yield('inputFields')
                                <x-redirect_submit_options
                                        :index_route="$index_route ?? null"
                                        :button_label="trans('general.save')"
                                        :options="$options ?? []"
                                />

                        </div>
              </div> <!-- ./box-body -->
          </div> <!--box -->
        </form>


      </div><!-- col-md-8 -->
</div><!-- ./row -->
<script src="{{ url(asset('js/jquery.js')) }}" nonce="{{ csrf_token() }}"></script>
<script type="text/javascript">
 $(document).ready(function () {
    $('#assigned_asset_select').on('change', function(){
      const selectedText = $(this).find('option:selected').text();
      const match = selectedText.match(/(?:[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,})?(.*)\(S\/n:\s*([^)]+)\)/i);
      if (match) {
          const deviceName = match[1]; // "Dell EMC VMAX250"
          const serialNumber = match[2]; // "2978018372"
          console.log("Device:", deviceName);
          console.log("Serial:", serialNumber);
          $('#asset_name').val(deviceName);
          $('#asset_serial').val(serialNumber);
        } else {
          console.log("Не удалось распарсить строку.");
      }

    })
  })
</script>
@stop
