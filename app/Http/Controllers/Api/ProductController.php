<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Auth;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Validator;

class ProductController extends BaseController
{
    public function index()
    {
        try
        {
            $product = Product::with(['product_image'])->where('user_id',Auth::user()->id)->get();
            return response()->json(['success'=>true,'data'=>$product]);
        }
        catch(\Eception $e)
        {
            return $this->sendError($e->getMessage());
        }        
    }

    public function create()
    {
        
    }

    public function store(Request $request)
    {
        // print_r( $request->all());die;
        try
        {
            $valiadator = Validator::make($request->all(),[
                'title' =>'required',
                'photo' =>'required',
                'category' => 'required',
                'quantity' => 'required',
                'price' => 'required',
                'size' => 'required',
                'color' => 'required',
            ]);

            if($valiadator->fails())
            {
                return $this->sendError($valiadator->errors()->first());
            }   

            $product = Product::create([
                'user_id' => Auth::user()->id,
                'title' =>$request->title,
                'category' =>$request->category,
                'quantity' =>$request->quantity,
                'price' =>$request->price,
                'size' => json_encode($request->size),
                'color' =>json_encode($request->color),
                'status' => 'Active'
            ]);

            $fileName = null;
            if(request()->hasFile('photo')) 
            {
                foreach($request->photo as $file)
                {   
                    $path = public_path('/uploads/products/');
                    $fileName = md5($file->getClientOriginalName()) . time() . "." . $file->getClientOriginalExtension();
                    $file->move($path, $fileName);

                    ProductImage::create([
                        'product_id' => $product->id,
                        'photo' => url('/uploads/products/'.$fileName),
                    ]);
                }
            }
            
            return response()->json(['success'=>true,'msg'=>'Product Create Successfully']);
        }
        catch(\Eception $e)
        {
            return $this->sendError($e->getMessage());
        }
    }

    public function show($id)
    {
        try
        {
            $product = Product::with(['product_image'])->find($id);
            return response()->json(['success'=>true,'data'=>$product]);
        }
        catch(\Eception $e)
        {
            return $this->sendError($e->getMessage());
        } 
    }

    public function update(Request $request, $id)
    {
        try
        {
            $product = Product::find($id);
            $images = ProductImage::where('product_id',$id)->get();
            foreach($images as $image)
            {
                $image->delete();
                $photo = substr($image->photo,strlen(url('/')));
                if(\File::exists(public_path($photo))){
                    \File::delete(public_path($photo));
                }
            }
            $valiadator = Validator::make($request->all(),[
                'title' =>'required',
            //    'photo' =>'required',
                'category' => 'required',
                'quantity' => 'required',
                'price' => 'required',
                'size' => 'required',
                'color' => 'required',
            ]);

            if($valiadator->fails())
            {
                return $this->sendError($valiadator->errors()->first());
            }   

            $product->update([
                'user_id' => Auth::user()->id,
                'title' =>$request->title,
                'category' =>$request->category,
                'quantity' =>$request->quantity,
                'price' =>$request->price,
                'size' =>$request->size,
                'color' =>$request->color,
                'status' => 'Active'
            ]);

            $fileName = null;
            if(request()->hasFile('photo')) 
            {
                foreach($request->photo as $file)
                {   
                    $path = public_path('/uploads/products/');
                    $fileName = md5($file->getClientOriginalName()) . time() . "." . $file->getClientOriginalExtension();
                    $file->move($path, $fileName);

                    ProductImage::create([
                        'product_id' => $product->id,
                        'photo' => url('/uploads/products/'.$fileName),
                    ]);
                }
            }
            
            return response()->json(['success'=>true,'msg'=>'Product Updated Successfully']);
        }
        catch(\Eception $e)
        {
            return $this->sendError($e->getMessage());
        }
    }

    public function destroy($id)
    {
        try
        {
            $product = Product::find($id);
            $images = ProductImage::where('product_id',$id)->get();
            foreach($images as $image)
            {
                $photo = substr($image->photo,strlen(url('/')));
                if(\File::exists(public_path($photo)))
                {
                    \File::delete(public_path($photo));
                }
                $image->delete();
            }
            $product->delete();
            return response()->json(['success'=>true,'msg'=>'Product Delete Successfully']);
        }
        catch(\Eception $e)
        {
            return $this->sendError($e->getMessage());
        }
    }
}
