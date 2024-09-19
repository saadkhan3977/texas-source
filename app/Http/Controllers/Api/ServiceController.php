<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Auth;
use App\Models\Service;
use App\Models\ServiceImage;

use Illuminate\Http\Request;
use Validator;

class ServiceController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try
        {
            $product = Service::with('images')->where('user_id',Auth::user()->id)->first();
            return response()->json(['success'=>true,'data'=>$product]);
        }
        catch(\Eception $e)
        {
            return $this->sendError($e->getMessage());
        }  
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try
        {
            $valiadator = Validator::make($request->all(),[
                'shop_name' =>'required',
                'photo' =>'required',
                'category' => 'required',
                'charges' => 'required',
                'description' => 'required',
            ]);

            if($valiadator->fails())
            {
                return $this->sendError($valiadator->errors()->first());
            }   

            $product = Service::create([
                'user_id' => Auth::user()->id,
                'shop_name' =>$request->shop_name,
                'category' =>$request->category,
                'charges' =>$request->charges,
                'description' =>$request->description,
            ]);

            $fileName = null;
            if(request()->hasFile('photo')) 
            {
                foreach($request->photo as $file)
                {   
                    $path = public_path('/uploads/service/');
                    $fileName = md5($file->getClientOriginalName()) . time() . "." . $file->getClientOriginalExtension();
                    $file->move($path, $fileName);

                    ServiceImage::create([
                        'service_id' => $product->id,
                        'photo' => url('/uploads/service/'.$fileName),
                    ]);
                }
            }
            
            return response()->json(['success'=>true,'msg'=>'Service Create Successfully']);
        }
        catch(\Eception $e)
        {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try
        {
            $product = Service::with(['images'])->find($id);
            return response()->json(['success'=>true,'data'=>$product]);
        }
        catch(\Eception $e)
        {
            return $this->sendError($e->getMessage());
        } 
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Service $service)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,$id)
    {
        try
        {
            //return $request->all();
            $valiadator = Validator::make($request->all(),[
                'shop_name' =>'required',
                'photo' =>'required',
                'category' => 'required',
                'category' => 'required',
                'charges' => 'required',
                'description' => 'required',
            ]);

            if($valiadator->fails())
            {
                return $this->sendError($valiadator->errors()->first());
            }   

            $service = Service::find($id);
            $images = ServiceImage::where('service_id',$id)->get();
            foreach($images as $image)
            {
                $photo = substr($image->photo,strlen(url('/')));
                if(\File::exists(public_path($photo))){
                    \File::delete(public_path($photo));
                }
                $image->delete();
            }

            $product = $service->update([
                'user_id' => Auth::user()->id,
                'shop_name' =>$request->shop_name,
                'category' =>$request->category,
                'charges' =>$request->charges,
                'description' =>$request->description,
            ]);

            $fileName = null;
            if(request()->hasFile('photo')) 
            {
                foreach($request->photo as $file)
                {   
                    $path = public_path('/uploads/service/');
                    $fileName = md5($file->getClientOriginalName()) . time() . "." . $file->getClientOriginalExtension();
                    $file->move($path, $fileName);

                    ServiceImage::create([
                        'service_id' => $id,
                        'photo' => url('/uploads/service/'.$fileName),
                    ]);
                }
            }
            
            return response()->json(['success'=>true,'msg'=>'Service Update Successfully']);
        }
        catch(\Eception $e)
        {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try{
            $product = Service::find($id);
            $images = ServiceImage::where('service_id',$id)->get();
            foreach($images as $image)
            {
                $photo = substr($image->photo,strlen(url('/')));
                if(\File::exists(public_path($photo))){
                    \File::delete(public_path($photo));
                }
                $image->delete();
            }
            $product->delete();
            return response()->json(['success'=>true,'msg'=>'Service Delete Successfully']);
        }
        catch(\Eception $e)
        {
            return $this->sendError($e->getMessage());
        }
    }
}
