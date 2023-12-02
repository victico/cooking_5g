<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\DataTables;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $order = Order::withCount('products')->with('user')->get();
        foreach ($order as $key ) {
            # code...
            $key->getStatusLabelAttribute();
        }

        return $this->returnSuccess(200, $order );
    }
    public function getOrdersTable()
    {
        $order = Order::with('user')->get();
        foreach ($order as $key ) {
            # code...
            $key->getStatusLabelAttribute();
        }

        return DataTables::of($order)->filter(function ($query) {
                
            if(!empty(request('filter_start_date'))){
              $query->whereBetween('created_at', [request('filter_start_date'), request('filter_end_date')]);
            }
          })->toJson();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getLastByType(Request $request) {
        

        $order = Order::withCount('products')->with('user')->take(5)->where('status',$request->type)->get();
        foreach ($order as $key ) {
            # code...
            $key->getStatusLabelAttribute();
        }
        return $this->returnSuccess(200, $order );
    }
    public function getOrderById($id) {
        

        $order = Order::withCount('products')->with(['user', 'products'])->find($id);
        $order->getStatusLabelAttribute();
        return $this->returnSuccess(200, $order );
    }
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
