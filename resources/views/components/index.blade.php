@extends('layouts/default')

{{-- Page title --}}
@section('title')
{{ trans('general.components') }}
@parent
@stop

@section('header_right')
  @can('create', \App\Models\Component::class)
    <a href="{{ route('components.create') }}" {{$snipeSettings->shortcuts_enabled == 1 ? "accesskey=n" : ''}} class="btn btn-primary pull-right"> {{ trans('general.create') }}</a>
  @endcan
@stop

{{-- Page content --}}
@section('content')
<div class="row">
  <div class="col-md-12">
    <div class="box box-default">
      <div class="box-body">
        <table
                data-columns="{{ \App\Presenters\ComponentPresenter::dataTableLayout() }}"
                data-cookie-id-table="componentsTable"
                data-pagination="true"
                data-id-table="componentsTable"
                data-search="true"
                data-side-pagination="server"
                data-show-columns="true"
                data-show-fullscreen="true"
                data-show-export="true"
                data-show-footer="true"
                data-show-refresh="true"
                data-sort-order="asc"
                data-sort-name="name"
                id="componentsTable"
                class="table table-striped snipe-table"
                data-url="{{ route('api.components.index') }}"
                data-export-options='{
                "fileName": "export-components-{{ date('Y-m-d') }}",
                "ignoreColumn": ["actions","image","change","checkbox","checkincheckout","icon"]
                }'>
        </table>
      </div><!-- /.box-body -->
    </div><!-- /.box -->
  </div>
</div>

@stop

@section('moar_scripts')
@include ('partials.bootstrap-table', ['exportFile' => 'components-export', 'search' => true, 'showFooter' => true, 'columns' => \App\Presenters\ComponentPresenter::dataTableLayout()])
<div class="modal fade" id="imageModal" tabindex="-1" role="dialog" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">Просмотр изображения</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                  <div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
                        <!-- Indicators -->
                        <ol class="carousel-indicators"></ol>

                        <!-- Wrapper for slides -->
                        <div class="carousel-inner" role="listbox"></div>

                        <!-- Controls -->
                        <a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">
                            <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                            <span class="sr-only">Previous</span>
                        </a>
                        <a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">
                            <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                            <span class="sr-only">Next</span>
                        </a>
                  </div>
            </div>
        </div>
    </div>
</div>
<script>
   $(document).ready(function() {
    $(document).on('click', '.images', function() {
      var imageSrc = $(this).data('images');
                // Ссылки на контейнеры индикаторов и слайдов
          const indicatorsContainer = $('#carousel-example-generic .carousel-indicators');
          const slidesContainer = $('#carousel-example-generic .carousel-inner');
          indicatorsContainer.html(''); // очистка индикаторов
          slidesContainer.html(''); // очистка слайдов
          // Генерация индикаторов и слайдов
          imageSrc.forEach((src, index) => {
              // Создание индикатора
              const indicator = $('<li>')
                  .attr('data-target', '#carousel-example-generic')
                  .attr('data-slide-to', index)
                  .addClass(index === 0 ? 'active' : '');
              indicatorsContainer.append(indicator);

              // Создание слайда
              const slide = $('<div>')
                  .addClass('item')
                  .addClass(index === 0 ? 'active' : '');
              const img = $('<img>').attr('src', src).attr('alt', `Image ${index + 1}`);
              const caption = $('<div>').addClass('carousel-caption').text(`Image ${index + 1}`);

              slide.append(img).append(caption);
              slidesContainer.append(slide);
          });
        $('#imageModal').modal('show');
    });
});
</script>


@stop
