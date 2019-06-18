<?php
/**
 * Created by PhpStorm.
 * User: thai
 * Date: 24/6/2018
 * Time: 10:57 PM
 */

namespace App\Http\Controllers\Admin;


use App\Helpers\Business;
use App\Helpers\HttpCode;
use App\Http\Controllers\Controller;
use App\Http\Requests\OrderRequest;
use App\Models\Data\District;
use App\Models\Data\Other;
use App\Models\Data\Province;
use App\Models\OrderDetail;
use App\Models\Warehouse;
use App\Repositories\Order\OrderRepositoryContract;
use App\Repositories\OrderDetail\OrderDetailRepositoryContract;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * @var OrderRepositoryContract
     */
    public $repository;

    /**
     * OrderController constructor.
     * @param OrderRepositoryContract $repositoryContract
     */
    public function __construct(OrderRepositoryContract $repositoryContract)
    {
        $this->repository = $repositoryContract;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getList()
    {
        $title = __('label.order');
        $orderStatus = array(
            Business::ORDER_STATUS_WAITING => __('label.waiting'),
            Business::ORDER_STATUS_NO_DELIVERY => __('label.no_delivery'),
            Business::ORDER_STATUS_BEING_DELIVERY => __('label.being_delivery'),
            Business::ORDER_STATUS_DONE_DELIVERY => __('label.done_delivery'),
            Business::ORDER_STATUS_CUSTOMER_CANCEL => __('label.customer_cancel'),
            Business::ORDER_STATUS_IHT_CANCEL => __('label.iht_cancel'),
            Business::ORDER_STATUS_FAIL => __('label.order_fail')
        );
        $orderStatusColor = array(
            Business::ORDER_STATUS_WAITING => 'label-warning',
            Business::ORDER_STATUS_NO_DELIVERY => 'label-primary',
            Business::ORDER_STATUS_BEING_DELIVERY => 'label-info',
            Business::ORDER_STATUS_DONE_DELIVERY => 'label-success',
            Business::ORDER_STATUS_CUSTOMER_CANCEL => 'label-danger',
            Business::ORDER_STATUS_IHT_CANCEL => 'label-danger',
            Business::ORDER_STATUS_FAIL => 'label-danger'
        );

//        $orderType = [
//            Business::CAR_TYPE_MOTORBIKE => __('label.motorbike'),
//            Business::CAR_TYPE_TRUCK => __('label.truck'),
//        ];
//
//        $orderTypeColor = [
//            Business::CAR_TYPE_MOTORBIKE => 'label-primary',
//            Business::CAR_TYPE_TRUCK => 'label-danger',
//        ];
        $listCar = Other::select('id', 'name')->where(['type' => Business::OTHER_TYPE_CAR])->get();
        $orderType = $this->convertObjectToArray($listCar);

        $orderPayment = array(
            Business::ORDER_STATUS_PAYMENT => __('label.payment_yes'),
            Business::ORDER_STATUS_NO_PAYMENT => __('label.payment_no')
        );
        $orderPaymentColor = array(
            Business::ORDER_STATUS_PAYMENT => 'label-success',
            Business::ORDER_STATUS_NO_PAYMENT => 'label-danger'
        );

        return view('admin.order.list', compact('orderType', 'title', 'orderTypeColor', 'orderStatus',
            'orderStatusColor', 'orderPayment', 'orderPaymentColor'));
    }

    private function convertObjectToArray($objectCar)
    {
        $result = [];
        foreach ($objectCar as $car){
            $result[$car->id] = $car->name;
        }
        return $result;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function getListOrder(Request $request)
    {
        $listCustomer = $this->repository->getOrderDataTable($request);
        return $listCustomer;
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function detail($id)
    {
        $item = $this->repository->find($id);
        if (!$item){
            return abort(404);
        }
        $orderStatus = array(
            Business::ORDER_STATUS_WAITING => __('label.waiting'),
            Business::ORDER_STATUS_NO_DELIVERY => __('label.no_delivery'),
            Business::ORDER_STATUS_BEING_DELIVERY => __('label.being_delivery'),
            Business::ORDER_STATUS_DONE_DELIVERY => __('label.done_delivery'),
            Business::ORDER_STATUS_CUSTOMER_CANCEL => __('label.customer_cancel'),
            Business::ORDER_STATUS_IHT_CANCEL => __('label.iht_cancel'),
            Business::ORDER_STATUS_FAIL => __('label.order_fail')
        );
        $orderStatusColor = array(
            Business::ORDER_STATUS_WAITING => 'label-warning',
            Business::ORDER_STATUS_NO_DELIVERY => 'label-primary',
            Business::ORDER_STATUS_BEING_DELIVERY => 'label-info',
            Business::ORDER_STATUS_DONE_DELIVERY => 'label-success',
            Business::ORDER_STATUS_CUSTOMER_CANCEL => 'label-danger',
            Business::ORDER_STATUS_IHT_CANCEL => 'label-danger',
            Business::ORDER_STATUS_FAIL => 'label-danger'
        );

//        $orderType = [
//            Business::CAR_TYPE_MOTORBIKE => __('label.motorbike'),
//            Business::CAR_TYPE_TRUCK => __('label.truck'),
//        ];
//
//        $orderTypeColor = [
//            Business::CAR_TYPE_MOTORBIKE => 'label-primary',
//            Business::CAR_TYPE_TRUCK => 'label-danger',
//        ];

        $listCar = Other::select('id', 'name')->where(['type' => Business::OTHER_TYPE_CAR])->get();
        $orderType = $this->convertObjectToArray($listCar);

        $genderType = array(
            Business::GENDER_MALE => __('label.male'),
            Business::GENDER_FEMALE => __('label.female'),
        );

        $orderMethod = array(
            Business::PAYMENT_METHOD_CASH => __('label.method_cash'),
            Business::PAYMENT_METHOD_MONTH =>__('label.method_month'),
            Business::PAYMENT_METHOD_OTHER => __('label.method_other')
        );
        $orderMethodColor = array(
            Business::PAYMENT_METHOD_CASH => 'label-danger',
            Business::PAYMENT_METHOD_MONTH => 'label-info',
            Business::PAYMENT_METHOD_OTHER => 'label-warning'
        );

//        $orderPayment = array(
//            Business::ORDER_STATUS_PAYMENT => __('label.payment_yes'),
//            Business::ORDER_STATUS_NO_PAYMENT => __('label.payment_no')
//        );
//        $orderPaymentColor = array(
//            Business::ORDER_STATUS_PAYMENT => 'label-success',
//            Business::ORDER_STATUS_NO_PAYMENT => 'label-danger'
//        );

        $orderPayment = array(
            Business::PAYMENT_DONE => __('label.payment_done'),
            Business::PAYMENT_DEPT => __('label.payment_dept')
        );
        $orderPaymentColor = array(
            Business::PAYMENT_DONE => 'label-success',
            Business::PAYMENT_DEPT => 'label-danger'
        );

        $listType = [
            Business::PRICE_BY_TH1 => __('label.th1'),
            Business::PRICE_BY_TH2 => __('label.th2'),
            Business::PRICE_BY_TH3 => __('label.th3'),
        ];

        $listTypeColor = [
            Business::PRICE_BY_TH1 => 'label-primary',
            Business::PRICE_BY_TH2 => 'label-danger',
            Business::PRICE_BY_TH3 => 'label-success',
        ];

        $listWarehouse = Warehouse::all();

        $config = $this->setConfigMaps();
        $config['directionsStart'] = $item->detail->sender_address . ', ' . optional(optional($item->detail)->districtSender)->name . ', ' . optional(optional($item->detail)->provinceSender)->name;
        $config['directionsEnd'] = $item->detail->receive_address . ', ' . optional(optional($item->detail)->districtReceive)->name . ', ' . optional(optional($item->detail)->provinceReceive)->name;
        app('map')->initialize($config);

        $map = app('map')->create_map();

        $title = $item->name;
        return view('admin.order.detail', compact('map', 'orderMethod', 'orderMethodColor', 'item', 'title',
            'orderStatusColor', 'orderStatus', 'orderType', 'orderTypeColor', 'genderType', 'orderPayment', 'orderPaymentColor',
            'listType', 'listTypeColor', 'listWarehouse'));
    }

    /**
     * @param null $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($id=null, Request $request)
    {
        if ($request->ajax()){
            $order = $this->repository->find($id);
            if ($order){
                $order->status = $request->status;
                if ($order->save()){
                    return response()->json(['code' => 200], HttpCode::SUCCESS);
                }
            }
        }
        return response()->json(['code' => 401], HttpCode::SUCCESS);
    }

    /**
     * @param null $id
     * @param int $status
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStatus($id=null, $status=Business::ORDER_STATUS_IHT_CANCEL)
    {
        $order = $this->repository->find($id);
        if ($order && $order->status == Business::ORDER_STATUS_WAITING){
            $order->status = $status;
            if ($order->save()){
                return redirect()->back()->with($this->messageResponse());
            }
        }
        return redirect()->back()->with($this->messageResponse('danger', __('label.failed')));
    }

    public function ajaxPrice($id, Request $request)
    {
        if ($request->ajax()){
            $order = $this->repository->find($id);
            $order->total_price = $request->price ? str_replace(',', '', $request->price) : '-1';
            if ($order->save()){
                return redirect()->back()->with($this->messageResponse());
            }
            return redirect()->back()->with($this->messageResponse('danger', __('label.failed')));
        }
        return redirect()->back()->with($this->messageResponse('danger', __('label.failed')));
    }

    /**
     * @return array
     */
    private function setConfigMaps()
    {
        $config = array();
        $config['zoom'] = '14';
        $config['height'] = 'auto';
        $config['width'] = 'auto';
        $config['directions'] = TRUE;
        $config['directionsDivID'] = 'directionsDiv';
        return $config;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function ajaxSelect2(Request $request)
    {
        return response()->json($this->repository->ajaxSelect2($request));
    }

    public function warehouse(Request $request)
    {
        $order = OrderDetail::where(['order_id' => $request->order_id])->first();
        if ($order){
            $order->warehouse_id = $request->id_warehouse;
            if ($order->save()){
                return redirect()->back()->with($this->messageResponse());
            }
            return redirect()->back()->with($this->messageResponse('danger', __('label.failed')));
        }
        return redirect()->back()->with($this->messageResponse('danger', __('label.failed')));
    }

    public function create()
    {
        $title = __('label.create');
        $item = false;
        $orderMethod = array(
            Business::PAYMENT_METHOD_CASH => __('label.method_cash'),
            Business::PAYMENT_METHOD_MONTH =>__('label.method_month'),
            Business::PAYMENT_METHOD_OTHER => __('label.method_other')
        );
        $orderPayment = array(
            Business::ORDER_STATUS_PAYMENT => __('label.payment_yes'),
            Business::ORDER_STATUS_NO_PAYMENT => __('label.payment_no')
        );

        $listType = [
            Business::PRICE_BY_TH1 => __('label.th1'),
            Business::PRICE_BY_TH2 => __('label.th2'),
            Business::PRICE_BY_TH3 => __('label.th3'),
        ];

        $listProvince = Province::where(['publish' => 1])->get();

        $listCar = Other::where(['type' => Business::OTHER_TYPE_CAR])->get();
        return view('admin.order.form', compact('title', 'item', 'orderMethod', 'orderPayment', 'listType', 'listCar', 'listProvince'));
    }

    public function store(OrderRequest $request, OrderDetailRepositoryContract $detailRepositoryContract)
    {
        $dataOrder = $request->only('name', 'type', 'payment_type','car_type', 'car_option', 'user_id', 'coupon_code');
        $dataOrder['total_price'] = str_replace(',', '', $request->total_price);
        $dataOrder['is_admin'] = 1;
        $orderId = $this->repository->store($dataOrder);
        if ($orderId){
            $dataOrderDetail =  $request->only('sender_name', 'sender_phone','sender_address', 'receive_name', 'receive_phone', 'price_id',
                'receive_address', 'km', 'weight', 'sender_province_id', 'sender_district_id', 'receive_province_id', 'receive_district_id', 'note');
            $dataOrderDetail['order_id'] = $orderId;
            $dataOrderDetail['sender_date'] = ($request->sender_date) ? Carbon::createFromFormat('d/m/Y', $request->sender_date)->format('Y-m-d') : date('Y-m-d');
            $dataOrderDetail['receive_date'] = ($request->receive_date) ? Carbon::createFromFormat('d/m/Y', $request->receive_date)->format('Y-m-d') : null;

            if ($detailRepositoryContract->store($dataOrderDetail)){
                return redirect(route('order.list'))->with($this->messageResponse());
            }else{
                $this->repository->delete($orderId);
            }
        }
        return redirect(route('order.list'))->with($this->messageResponse('danger', __('label.failed')));
    }

    public function district($provinceID, District $district)
    {
        $listDistrict = $district->select('id', 'name as text')->where(['province_id' => $provinceID, 'publish' => 1])->get();
        return response(['district' => $listDistrict]);
    }

    public function payment($id, Request $request)
    {
        $order = $this->repository->find($id);
        if ($order){
            $order->is_payment = $request->is_payment;
            if ($order->save()){
                return redirect()->back()->with($this->messageResponse());
            }
        }
        return redirect()->back()->with($this->messageResponse('danger', __('label.failed')));
    }
    public function couponCode($id, Request $request)
    {
        $request->validate([
            'coupon_code' => 'required|unique:orders,coupon_code'
        ]);

        $order = $this->repository->find($id);
        if ($order) {
            $order->coupon_code = $request->coupon_code;
            if ($order->save()) {
                return redirect()->back()->with($this->messageResponse());
            }
        }
        return redirect()->back()->with($this->messageResponse('danger', __('label.failed')));
    }
}