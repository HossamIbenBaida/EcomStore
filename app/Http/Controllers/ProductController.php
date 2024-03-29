<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Category;
use App\Models\Product;
class ProductController extends Controller
{
    public function addproduct(){
        $categories = Category::All()->pluck('category_name' ,'category_name');
return view('admin.addproduct')->with('categories',$categories);;
    }
 public function saveproduct(Request $request){
        $this->validate($request,['product_name'=>'required' ,
                                   'product_price'=>'required' ,
                                   'product_category'=>'required' ,
                                    'product_image'=>'image|nullable|max:1999']);
        if($request->hasFile('product_image')){
            //1 : get file name with exte
            $fileNameWithExt = $request->file('product_image')->getClientOriginalName();
            //2 : get just file name 
            $fileName = pathinfo($fileNameWithExt , PATHINFO_FILENAME);
            //3 get just file extension
            $extension = $request->file('product_image')->getClientOriginalExtension();
            //4 : file name to store
            $fileNameToStore = $fileName.'_'.time().'.'.$extension ;
            //upload image
            $path=$request->file('product_image')->storeAs('public/product_images', $fileNameToStore);

        }else{
            $fileNameToStore = 'noimage.jpg';

        }
        $product = new Product();
        $product->product_name = $request->input('product_name');                            
        $product->product_price = $request->input('product_price');  
        $product->product_category = $request->input('product_category');  
        $product->product_image = $fileNameToStore;  
        $product->status = 1;  
        $product->save();
        return back()->with('status','the product has been successfully saved !!');
    }
    public function products(){
        $products = Product::All();
    
        return view('admin.products')->with('products',$products);
            }

     public function editproduct( $id){
          $product = Product::find($id);
          $categories = Category::All()->pluck('category_name' ,'category_name');
         return view('admin.editproduct', compact(['product' ,'categories']));
     }


     public function updateproduct(Request $request ){
        $this->validate($request,['product_name'=>'required' ,
                                   'product_price'=>'required' ,
                                   'product_category'=>'required' ,
                                    'product_image'=>'image|nullable|max:1999']);

        $product = Product::find($request->input('id'));                         
        if($request->hasFile('product_image')){
            //1 : get file name with exte
            $fileNameWithExt = $request->file('product_image')->getClientOriginalName();
            //2 : get just file name 
            $fileName = pathinfo($fileNameWithExt , PATHINFO_FILENAME);
            //3 get just file extension
            $extension = $request->file('product_image')->getClientOriginalExtension();
            //4 : file name to store
            $fileNameToStore = $fileName.'_'.time().'.'.$extension ;
            
            
            //upload image
            $path=$request->file('product_image')->storeAs('public/product_images', $fileNameToStore);
            if($product->product_image != 'noimage.jpg'){
                Storage::delete('public/product_images/'.$product->product_image);
            }

            $product->product_image = $fileNameToStore;

        } 
        $product->product_name = $request->input('product_name');                            
        $product->product_price = $request->input('product_price');  
        $product->product_category = $request->input('product_category');  
          

        $product->update();
        return redirect('/products')->with('status','the product has been successfully saved !!');
    }
    public function deleteproduct( $id){
        $product = Product::find($id);
        if($product->product_image !='noimage.jpg'){
            Storage::delete('public/product_images/'.$product->product_image);
        }

        $product->delete();
        return back()->with('status','the product has been successfully deleted !!');
   }
   public function UnActivate( $id){
    $product = Product::find($id);
    $product->status=0;
    $product->update();
    return back()->with('status','the product has been successfully deactivated !!');
    }
    public function Activate( $id){
        $product = Product::find($id);
        $product->status=1;
        $product->update();
        return back()->with('status','the product has been successfully activated !!');
    }

    public function view_product_by_category($name){
    $products = Product::All()->where('product_category', $name)->where('status' ,1);
    $categories = Category::All();

    return view('client.shop')->with('categories' ,$categories )->with('products' ,$products );

    }
}

