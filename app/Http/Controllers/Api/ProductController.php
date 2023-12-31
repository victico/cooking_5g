<?php

namespace App\Http\Controllers\Api;

use stdClass;
use Exception;
use App\Models\Lot;
use App\Models\Product;
use App\Models\Dismantling;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use PhpParser\Node\Expr\FuncCall;
use App\Http\Controllers\Controller;
use Illuminate\Database\Query\Builder;
use Yajra\DataTables\CollectionDataTable;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->returnSuccess(200, Product::with(['dismantling.products_pieces', 'lotes:due_date'])->get());
    }
    public function getProductsTable(Request $request){
        $products = Product::query()->with(['dismantling.products_pieces']);
        
        
        if(!empty(request('order_title')))  $products->orderBy('title', request('order_title'));

        if(!empty(request('order_stock')))  $products->orderBy('stock', request('order_stock'));
        
        if (!empty(request('filter_product_title'))) {
            $products->where('title','like','%'.request('filter_product_title').'%');
        }
        if(!empty(request('order_due_date'))) {
            if(!empty(request('order_due_date')))  $products->orderBy('due_date', request('order_due_date'));

        }
        

        return DataTables::of($products)->toJson();
  
    }
    public function getProductsCriticalStock()
    {
        return $this->returnSuccess(200, Product::orderBy('stock', 'asc')->where('stock', '<', '20')->take(5)->get());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function storeProduct(Request $request)
    {
        $imgPath = '';
        if ($request->img) {
            $imgPath = 'images/product/' . trim(str_replace(' ', '_', $request->title ));
            $request->file('img')->move(public_path() . '/images/product/', $imgPath);
        }

        $validated = $this->validateRequiredFields($request->all());

        if (!$validated['validated']) return $this->returnFail(400, $validated['message']);
        
        try {
            $product = Product::create([
                'title'             => $request->title,
                'description'       => $request->description,
                'short_description' => $request->short_description,
                'stock'             => $request->initial_stock,
                'type_of_unit'      => $request->type_unit,
                'img'               => $imgPath,
                'is_dismantling'    => $request->is_dismantling,
                'created_by'        => $request->user()->id,
                'updated_by'        => $request->user()->id
            ]);
        } catch (Exception $th) {
            return $this->returnSuccess(400, $th->getMessage() );
        }
    
        if($request->is_dismantling){
             $this->addDismantling($product->id, $request->dismantling);
        }

        $formatLote = $this->formatInputLot($request);
        $lote = $this->addLote($product->id, $formatLote);
        
        $this->setMostEarlyDueDate($product, $lote->due_date);
        return $this->returnSuccess(200, $product);

    }
    public function updateProduct(Request $request, $id)
    {
        $product = Product::find($id);

        if(!$product) return $this->returnFail(400, 'Producto no encontrado');

        $imgPath = $product->img;
        if ($request->img !== 'undefined') {
            $imgPath = 'images/product/' . trim(str_replace(' ', '_', $request->title )).'_2';
            $request->file('img')->move(public_path() . '/images/product/', $imgPath);
        }

        $validated = $this->validateRequiredFields($request->all(), 'update');

        if (!$validated['validated']) return $this->returnFail(400, $validated['message']);
        
        try {
            $product->title             = $request->title;
            $product->description       = $request->description;
            $product->short_description = $request->short_description;
            $product->type_of_unit      = $request->type_unit;
            $product->img               = $imgPath;
            $product->is_dismantling    = $request->is_dismantling;
            $product->updated_by        = $request->user()->id;


            if($request->is_dismantling){
                $this->returnSuccess(200, [$this->addDismantling($product->id, $request->dismantling), json_decode($request->dismantling,true)] );
           }

            $product->save();
            
        } catch (Exception $th) {
            return $this->returnSuccess(400, $th->getMessage());
        }
        
        return $this->returnSuccess(200, Product::with(['dismantling.products_pieces'])->find($product->id));
    }
    public function addStock(Request $request, $id)
    {
        $product = Product::find($id);
        if(!$product) return $this->returnFail(400, 'Producto no encontrado');

        if($request->type == 1){ $lote = $this->addLote($id, $request); $this->setMostEarlyDueDate($product, $lote->due_date);}  
        if($request->type == 2){ $lote = $this->reduceLote($id, $request); $this->setMostEarlyDueDate($product, $lote, 'reduce');} 

        try {
            $product->stock = $request->type == 1 
                ? $product->stock  + $request->quantity
                : $product->stock  - $request->quantity;
            $product->save();
            
        } catch (Exception $th) {
            return $this->returnSuccess(400, $th->getMessage());
        }
    
        
        

        return $this->returnSuccess(200, [$request->type, $request->quantity]);
    }
    public function getProductById($id){
        return $this->returnSuccess(200, Product::with(['dismantling.products_pieces','lotes'])->find($id));
    }
    public function getProductBySearch(Request $request){
        $products = Product::query()->with(['dismantling.products_pieces','lotes']);

        if(!empty($request->title)){
            $products->where('title', 'like', '%'.$request->title.'%');
        }

        return $this->returnSuccess(200, $products->take(10)->get());
    }   
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteProduct($productId)
    {
        if (!$productId) {
            return $this->returnFail(400, "El identificador del usuario.");
        }

        $product = Product::find($productId);
        $dismantlingsOfProducts= Dismantling::where('piece_product_id', $productId)->delete();


        if (!$product) {
            return $this->returnFail(404, "Usuario no encontrado.");
        }

        $product->delete();


        return $this->returnSuccess(200, ['id' => $productId, 'deleted_at' => $product->deleted_at]);
    }
    public function getLastLoteFromProduct($productId){
       return Lot::where('product_id', $productId)->count();
    }
    private function validateRequiredFields($inputRequest, $type = "create" )
    {
        
        if (!array_key_exists('title', $inputRequest) && empty($inputRequest['title']) )  {
            return [
                'validated' => false,
                'message' => 'El titulo del producto es requerido.',
            ];
        }
        if (!array_key_exists('description', $inputRequest) && empty($inputRequest['description']) ) {
            return [
                'validated' => false,
                'message' => 'La descripción del producto es requerida.',
            ];
        }
        if (!array_key_exists('short_description', $inputRequest) && empty($inputRequest['short_description']) ){
            return [
                'validated' => false,
                'message' => 'La descripción corta es requerida.',
            ];
        }
        if ( !array_key_exists('initial_stock', $inputRequest) && empty($inputRequest['initial_stock']) && $type == "create"){ 
            return [
                'validated' => false,
                'message' => 'El stock inicial es requerido.',
            ];
        }
        if (!array_key_exists('type_unit', $inputRequest) && empty($inputRequest['type_unit'])) {
            return [
                'validated' => false,
                'message' => 'El tipo de unidad es requerido.',
            ];
        }
        if (!array_key_exists('is_dismantling', $inputRequest) ){
            return [
                'validated' => false,
                'message' => 'Se requiere que especifique si trae despecies.',
            ];
        }

        return [
            'validated' => true,
        ];
    }
    private function addDismantling($productId, $dismantlingsProducts){
        $dismantlingsProductsArrayFormat = json_decode($dismantlingsProducts,true); 
        Dismantling::where('product_id', $productId)->forceDelete();

        if( $dismantlingsProductsArrayFormat !== null ){
            foreach ($dismantlingsProductsArrayFormat as $dismantlingProduct) {
    
                Dismantling::create([
                    'product_id'        => $productId,
                    'piece_product_id'  => $dismantlingProduct['piece_product_id'],
                    'quantity'          => $dismantlingProduct['quantity'],
                ]);
            }
        }
    } 
    private function addLote($productId, $data){

    
        return Lot::create([
            'lote_code'  => $data->lote,
            'product_id' => $productId,
            'quantity'   => $data->quantity,
            'due_date'   => $data->due_date ,
        ]);
            
        
    } 
    private function setMostEarlyDueDate(Product $product, $due_date, $type = "add" ){
        

        if(!$product->due_date){
            $product->due_date = $due_date;
            $product->save();
            return;
        }
        if($type == 'reduce'){
            $product->due_date = $due_date->quantity == 0
            ? Lot::where('quantity','>','0')->where('product_id', $product->id)->first()->due_date
            : $due_date->due_date;
            $product->save();
            return;
        }

        $product->due_date = strtotime($product->due_date) < strtotime($due_date)
            ? $product->due_date
            : $due_date;
            
        $product->save();

        return;
    }
    private function reduceLote($productId, $data){

        $lote = Lot::where('id', $data->lote)->where('product_id', $productId)->first();

        $lote->quantity = $lote->quantity - $data->quantity;
        $lote->save();
        return $lote;
    } 
    private function formatInputLot($inputs){
        $formatInputs = new stdClass(); 
        $formatInputs->lote = $inputs->initial_lote;
        $formatInputs->quantity = $inputs->initial_stock;
        $formatInputs->due_date = $inputs->due_date;

        return $formatInputs;

    }
}
