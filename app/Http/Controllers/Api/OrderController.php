<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Order;
use App\Models\User;
use App\Models\Wallet;
use App\Models\OrderDetail;
use Auth;
use Stripe;

class OrderController extends BaseController
{
    public function __construct()
    {
        $stripe = \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
    }

    public function index(Request $request) 
    {
        $data = Order::where('vendor_id',Auth::user()->id)->get();
        return response()->json(['success'=>true,'msg'=>'Order List', 'order_list' => $data]);
    }
    
    public function order_status(Request $request,$id) 
    {
        $data = Order::find($id);
        $data->status = $request->status;
        $data->save();
        return response()->json(['success'=>true,'msg'=>'Order Status Update']);
    }

    public function store(Request $request) 
    {
        try
        {
            if($request->payment_method != 'cod')
            {
                $token = $request->input('stripeToken');
                Stripe\Charge::create ([
                    "amount" => $request->total_amount * 100,
                    "currency" => "usd",
                    "source" => $request->stripeToken,
                    "description" => "This is a Texas Source Checkout transaction" 
                ]);
            }
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
                'payment_method' => $request->payment_method,
            ]);

            foreach($request->product_id as $key => $id)
            {
                OrderDetail::create([
                    'order_id' => $order->id,
                    'product_id' => $id,
                    // 'color' => $request->color[$key],
                    // 'size' => $request->size[$key],
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

    public function wallet()
    {
        try
        {
            $order = Wallet::where('user_id',Auth::user()->id)->first();
            $order['earning_history'] = Order::with('orderdetail','orderdetail.product','orderdetail.product.product_image')->where('vendor_id',Auth::user()->id)->get();
            return response()->json(['success'=>true,'msg'=>'My Wallet','wallet_info' => $order]);
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
            $order = Order::with('orderdetail','orderdetail.product','orderdetail.product.product_image')->where('vendor_id',Auth::user()->id)->get();
            return response()->json(['success'=>true,'msg'=>'Order List','orders' => $order]);
        }
        catch(\Eception $e)
        {
            return $this->sendError($e->getMessage());
        }    
    }
}
