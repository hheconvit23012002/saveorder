<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDetail;
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
            $order = Order::with("orderDetail")->where('user_id', $user->id)->get();
            return response()->json([
                "success" => true,
                "data" => $order
            ]);
        }catch (\Exception $e){
            return response()->json([
                "success" => false,
                "data" => [],
            ]);
        }
    }
    //
}
