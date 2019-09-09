@extends('layouts.admin')
@section('content')
<!-- Main content -->
<section class="content">
    <!-- Default box -->
    <div class="box">
        <div class="box-body">
            <div class="row">
                <div class="col-md-4">
                    <form method="POST" action="{{route('order.post.search.list.new')}}">
                        {{ csrf_field() }}
                        <div class="has-feedback">
                            <input name="search" type="text" value="{{$search}}" class="form-control" placeholder="{{__('label.customer_name')}}, {{__('label.order_name') . ',' . __('label.coupon_code') . ',' . __('label.phone')}}">
                        </div>
                        <button type="submit" class="btn btn-info" style="position: absolute;top: 0; right: 0; z-index: 2;">Tìm</button>

                    </form>

                </div>
                <div class="col-md-8 text-right">
                    <form class="form-inline" method="POST" action="{{route('order.option.list.new')}}">
                        {{ csrf_field() }}
                        <div class="form-group ">
                            <label>Trạng thái:</label>
                            <select name="status" class="form-control">
                                <option value="0" @if($status == "0") selected @endif>All</option>
                                <option value="1" @if($status == "1") selected @endif>Đang chờ</option>
                                <option value="2" @if($status == "2") selected @endif>Chưa giao</option>
                                <option value="3" @if($status == "3") selected @endif>Đang giao</option>
                                <option value="4" @if($status == "4") selected @endif>Đã hoàn thành</option>
                                <option value="5" @if($status == "5") selected @endif>Khách hủy</option>
                                <option value="6" @if($status == "6") selected @endif>IHT hủy</option>
                                <option value="7" @if($status == "7") selected @endif>Không thành công</option>
                            </select>
                        </div>
                        <div class="form-group ">
                            <label>Phương thức thanh toán:</label>
                            <select name="payment_type" class="form-control">
                                <option value="0" @if($payment_type == "0") selected @endif>All</option>
                                <option value="1" @if($payment_type == "1") selected @endif>Tiền mặt</option>
                                <option value="2" @if($payment_type == "2") selected @endif>Theo tháng</option>
                            </select>
                        </div>
                        <div class="btn-group"> <button type="submit" class="btn btn-info">Tìm</button></div>
                    </form>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-md-12">
                    <!-- Date range -->
                    <form class="form-inline" method="POST" action="{{route('order.post.search.date')}}">
                            {{ csrf_field() }}
                            <div class="form-group">
                                <label>{{__('label.end_date')}}:</label>
                                <div class="input-group">
                                    <div class="input-group-addon">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" name="date" value="" class="form-control pull-right" id="reservation">
                                </div>
                                <!-- /.input group -->
                            </div>
                            <button type="submit" class="btn btn-info">Tìm</button>
                            <!-- /.form group -->
                    </form>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-md-12">
                    <table id="tableItem" class="table table-bordered table-striped">
                        <thead>
                            <tr class="info">
                                <th>Mã bill</th>
                                <th>Tên đơn hàng</th>
                                <th>Trạng thái</th>
                                <th>Loại đơn hàng</th>
                                <th>Tổng tiền</th>
                                <th>Khách hàng</th>
                                <th>Địa chỉ gửi</th>
                                <th>Địa chỉ nhận</th>
                                <th>Ngày tạo</th>
                                @if(Auth::user()->level==1)
                                <th></th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $order)
                            <tr onclick="orderDetail({{$order->id}})">
                                <td>{{ $order->coupon_code }}</td>
                                <td>{{ $order->name }}</td>
                                <td>
                                    @switch($order->status)
                                    @case(1)
                                    <span class="bage-warning">Đang chờ</span>
                                    @break
                                    @case(2)
                                    <span class="bage-info">Chưa giao</span>
                                    @break
                                    @case(3)
                                    <span class="bage-danger">Đang giao</span>
                                    @break
                                    @case(4)
                                    <span class="bage-success">Đã hoàn thành</span>
                                    @break
                                    @case(5)
                                    <span class="bage-basic">Khách hủy</span>
                                    @break
                                    @case(6)
                                    <span class="bage-basic">IHT hủy</span>
                                    @break
                                    @case(7)
                                    <span class="bage-basic">Không thành công</span>
                                    @break

                                    @default
                                    <span>Không xác định</span>
                                    @endswitch
                                </td>
                                <td>
                                    @switch($order->car_option)
                                    @case(1)
                                    <span class="bage-success">Hàng hóa</span>
                                    @break
                                    @case(2)
                                    <span class="bage-warning">Chứng từ</span>
                                    @break
                                    @case(3)
                                    <span class="bage-success">Hàng hóa</span>
                                    @break

                                    @default
                                    <span class="bage-info">Làm hàng</span>
                                    @endswitch
                                </td>
                                <td>{{ number_format($order->total_price) }} </td>
                                <td><a href="{{url('admin/customer/detail/'.$order->customer_id.'')}}"> {{ $order->user_name }}</a></td>
                                <td>
                                    @if($order->sender_district_name ==null && $order->sender_province_name ==null)
                                    {{ $order->sender_address }}
                                    @else
                                    {{ $order->sender_district_name }} {{ $order->sender_province_name }}
                                    @endif
                                </td>
                                <td>
                                    @if($order->receive_district_name ==null && $order->receive_province_name ==null)
                                    {{ $order->receive_address }}
                                    @else
                                    {{ $order->receive_district_name }} {{ $order->receive_province_name }}
                                    @endif
                                </td>
                                <td>{{date('d/m/Y H:i:s', strtotime($order->created_at))}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $orders->links()}}
                </div>
            </div>
        </div>
    </div>
</section>

<!-- /.content -->
@endsection
@section('style')
<link rel="stylesheet" href="{{asset('public/admin')}}/plugins/datatables/dataTables.bootstrap.css">
<link rel="stylesheet" href="{{asset('public/admin')}}/plugins/datepicker/datepicker3.css">
<link rel="stylesheet" href="{{asset('public/admin')}}/plugins/daterangepicker/daterangepicker.css">

@endsection
@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
<script src="{{asset('public/admin')}}/plugins/daterangepicker/daterangepicker.js"></script>

<script src="{{asset('public/admin')}}/plugins/datatables/jquery.dataTables.min.js"></script>
<script src="{{asset('public/admin')}}/plugins/datatables/dataTables.bootstrap.min.js"></script>
<script src="{{asset('public/admin')}}/plugins/datepicker/bootstrap-datepicker.js"></script>

<script>
    function orderDetail(id)
        {
            window.open('detail/'+id, '_blank');
        }
    $(function() {
        $('#reservation').daterangepicker({
                locale: {
                    format: 'DD/MM/YYYY'
                }
            });
        $('#start_date, #end_date').datepicker({
            autoclose: true,
            format: 'dd/mm/yyyy'
        });
    });
</script>

@endsection