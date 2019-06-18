@extends('layouts.admin')

@section('content')
    <!-- Main content -->
    <section class="content">

        <!-- Default box -->
        <div class="box">
            <div class="box-header with-border">
                @can('create-price')
                    <div class="box-tools">
                        <div class="row">
                            <div class="col-md-4 col-xs-12">
                                <a href="{{route('price.add', \App\Helpers\Business::PRICE_BY_TH1)}}" class="btn btn-success btn-sm">
                                    <i class="fa fa-plus"></i> @lang('label.th1')</a>
                            </div>
                            <div class="col-md-5 col-xs-12">
                                <a href="{{route('price.add', \App\Helpers\Business::PRICE_BY_TH2)}}" class="btn btn-success btn-sm">
                                    <i class="fa fa-plus"></i> @lang('label.th2')</a>
                            </div>
                            <div class="col-md-3 col-xs-12">
                                <a href="{{route('price.add', \App\Helpers\Business::PRICE_BY_TH3)}}" class="btn btn-success btn-sm">
                                    <i class="fa fa-plus"></i> @lang('label.th3')</a>
                            </div>
                        </div>
                    </div>
                @endcan
                    <br>
            </div>
            <div class="box-body">
                <table id="tableItem" class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th>{{ __('label.type_car') }}</th>
                        <th>{{ __('label.th') }}</th>
                        <th>{{ __('label.min') }}</th>
                        <th>{{ __('label.min_value') }}</th>
                        <th>{{ __('label.increase') }}</th>
                        <th>{{ __('label.increase_value') }}</th>
                        <th>{{ __('label.status') }}</th>
                        <th>{{ __('label.action') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($listResult as $item)
                        <tr>
                            <td><a href="{{route('price.detail', $item->id)}}"><span class="label {{$listTypeCarColor[$item->type_car]}}">{{$listTypeCar[$item->type_car]}}</span></a></td>
                            <td>@if($item->type)<span class="label {{$listTypeColor[$item->type]}}">{{$listType[$item->type]}}@endif</span></td>
                            <td>{{ $item->min }}</td>
                            <td class="price">{{ $item->min_value }}</td>
                            <td>{{ $item->increase }}</td>
                            <td class="price">{{ $item->increase_value }}</td>
                            <td> <span class="label {{$listPublishColor[$item->publish]}}">{{ $listPublish[$item->publish] }}</span></td>
                            <td class="text-center">
                                @can('delete-user')
                                        <a onclick="return confirm_delete('{{ __('label.are_you_sure') }}')" href="{{route('price.delete', $item->id)}}" class="btn btn-danger btn-sm">
                                            <i class="fa fa-close"></i>
                                        </a>
                                @endcan

                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <!-- /.box -->

    </section>
    <!-- /.content -->
@endsection

@section('style')
    <link rel="stylesheet" href="{{asset('admin')}}/plugins/datatables/dataTables.bootstrap.css">
@endsection

@section('script')
    <script src="{{asset('admin')}}/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="{{asset('admin')}}/plugins/datatables/dataTables.bootstrap.min.js"></script>
    <script src="{!! asset('/admin/dist/js/jquery.number.min.js') !!}"></script>

    <script>
        $(function () {
            $('.price').number( true, 0 );

            if($("#tableItem").length > 0) {
                $("#tableItem").DataTable(
                    {
                        "order": [[ 5, "desc" ]],

                        "language": {
                            "lengthMenu": "{{ __('label.lengthMenu') }}",
                            "zeroRecords": "{{ __('label.zeroRecords') }}",
                            "info": "{{ __('label.info') }}",
                            "infoEmpty": "{{ __('label.infoEmpty') }}",
                            "search": "{{ __('label.search') }}",
                            "paginate": {
                                "first":      "{{ __('label.first') }}",
                                "last":       "{{ __('label.last') }}",
                                "next":       "{{ __('label.next') }}",
                                "previous":   "{{ __('label.previous') }}"
                            },
                            "infoFiltered": "({{ __('label.infoFiltered') }})"
                        }
                    }
                );
            }
        });
    </script>

@endsection