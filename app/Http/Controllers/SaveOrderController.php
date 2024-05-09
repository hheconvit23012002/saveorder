<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SaveOrderController extends Controller
{
    public function saveOrder(Request $request){
        try {
            $orderData = $request->get('order');
            $item = $request->get('item') ?? [];
            $order = new Order($orderData);
            $order->save();
            $orderDetail = [];
            foreach ($item as $each){
                $orderDetail[] = [
                    'order_id' => $order->id,
                    'product_id' => $each["product_id"],
                    'number' => $each["number"],
                    'price' => $each["price"],
                    'name_product' => $each["name_product"],
                    'image_product' => $each["image_product"],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            OrderDetail::insert($orderDetail);

            return response()->json([
                'order_id' => $order->id,
            ]);
        }catch(\Exception $e){
            return response()->json([
               'success' => false
            ],500);
        }
    }
    public function getListOrder(Request $request){
        try {
            $user = $request->get('user');
            $order = Order::with("orderDetail")->where('user_id', $user["id"])->get();
            return response()->json([
                "success" => true,
                "data" => $order
            ]);
        }catch (\Exception $e){
            return response()->json([
                "success" => false,
                "data" => [],
                "message" => $e->getMessage()
            ],500);
        }
    }

    public function getProductBuyMonth(){
        try {
            $firstDayOfMonth = Carbon::now()->firstOfMonth();
            $endOfMonth = Carbon::now()->endOfMonth();
            $orderByMonth = OrderDetail::whereBetween('created_at',[$firstDayOfMonth,$endOfMonth])
                ->get();
            $data = $orderByMonth->groupBy('product_id')
                ->map(function ($byMonth){
                    return [
                        'product_id' => $byMonth->first()->product_id,
                        'number' => $byMonth->sum('number'),
                    ];
                })
                ->sortByDesc(function ($value){
                    return $value['number'];
                })
                ->take(10)
            ;
            return response()->json($data);

        }catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => $e->getMessage(),
            ]);
        }
    }
    public function staticOrderInYear(){
        try {
            $year = date("Y");
            $order = Order::whereYear('created_at',$year)
                ->get();
            $month = [];
            for ($i = 1; $i <= 12; $i++) {
                $month[($i < 10 ? '0'.$i : $i) . "-" . now()->year] = 0;
            }
            $data = $order->groupBy(function ($order) {
                return $order->created_at->format('m-Y');
            })->map(function ($orderByMonth) {
                    return $orderByMonth->count();
                })
                    ->toArray() + $month;
            ksort($data);
            return response()->json([
                "data" => $data,
            ]);
        }catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => [],
                'message' => $e->getMessage(),
            ]);
        }
    }
    //
}
