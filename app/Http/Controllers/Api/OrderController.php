<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Order;
use App\Models\User;
use App\Models\OrderDetail;
use Auth;

class OrderController extends BaseController
{
    public function store(Request $request) 
    {
        try
        {
            $order = Order::create([
                'orderId' => 'ORD-'.strtoupper(Str::random(10)),
                'user_id' => Auth::user()->id,
                'vendor_id' => $request->vendor_id,
                'quantity' => $request->item_quantity,
                'total' => $request->total,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone' => $request->phone,
                'country' => $request->country,
                'address' => $request->address,
                'post_code' => $request->post_code,
            ]);

            foreach($request->product_id as $key => $id)
            {
                OrderDetail::create([
                    'order_id' => $order->id,
                    'product_id' => $id,
                    'color' => $request->color[$key],
                    'size' => $request->size[$key],
                    'qty' => $request->product_quantity[$key],
                ]);
            }

            return response()->json(['success'=>true,'msg'=>'Order Placed Successfully']);
        }
        catch(\Eception $e)
        {
            return $this->sendError($e->getMessage());
        }
    }

    public function user_order()
    {
        try
        {
            $order = Order::with('orderdetail','orderdetail.product','orderdetail.product.product_image')->where('user_id',Auth::user()->id)->get();
            return response()->json(['success'=>true,'msg'=>'Order List','orders' => $order]);
        }
        catch(\Eception $e)
        {
            return $this->sendError($e->getMessage());
        }    
    }


    public function vendor_list()
    {
        try
        {
            $order = User::where('role','vendor')->get();
            return response()->json(['success'=>true,'msg'=>'Vendor List','orders' => $order]);
        }
        catch(\Eception $e)
        {
            return $this->sendError($e->getMessage());
        }    
    }

    public function vendor_order()
    {
        try
        {
            $order = OrderDetail::with('order','order.product_image','order.user')->where('vendor_id',Auth::user()->id)->get();
            return response()->json(['success'=>true,'msg'=>'Order List','orders' => $order]);
        }
        catch(\Eception $e)
        {
            return $this->sendError($e->getMessage());
        }    
    }
}
