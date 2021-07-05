<?php

namespace App\Http\Controllers;

use App\Models\Banners;
use App\Models\Category;
use App\Models\Products;
use App\Models\ProductsAttributes;
use App\Models\ProductsImages;
use DB;
use Image;
use Str;
use Illuminate\Http\Request;
use Session;
use RealRashid\SweetAlert\Facades\Alert;


use Symfony\Component\Console\Input\Input;

class ProductsController extends Controller
{
    public function addProduct(Request $request, $image = '')
    {
        if ($request->isMethod('post')) {
            $data = $request->all();

            if ($request->hasFile('image')) {
                $image = $request->file('image')->getClientOriginalName();

                $request->image->move(public_path('/uploads/products/'), $image);
            }

            $product = new Products;
            // $product->category_id = $data['category_id'];
            $product->name = $data['product_name'];
            $product->category_id = $data['category_id'];
            $product->code = $data['product_code'];
            $product->color = $data['product_color'];
            if (!empty($data['product_description'])) {
                $product->description = $data['product_description'];
            } else {
                $product->description = '';
            }
            $product->price = $data['product_price'];
            $product->image = $image;


            $product->save();
            return redirect('admin/view-products')->with('flash_message_success', 'Product has been added successfully');

        }
        //Categories Dropdown menu Code
        $categories = Category::where(['parent_id' => 0])->get();
        $categories_dropdown = "<option value=''selected disabled>Select</option>";
        foreach ($categories as $cat) {
            $categories_dropdown .= "<option value='" . $cat->id . "'>" . $cat->name . "</option>";
            $sub_categories = Category::where(['parent_id' => $cat->id])->get();
            foreach ($sub_categories as $sub_cat) {
                $categories_dropdown .= "<option value='" . $sub_cat->id . "'>&nbsp;--&nbsp" . $sub_cat->name . "</option>";

            }
        }

        return view('admin.products.add_product')->with(compact('categories_dropdown'));


    }

    public function viewProducts()
    {
        $products = Products::get();
        return view('admin.products.view_products')->with(compact('products'));
    }

    public function editProducts(Request $request, $id = null)
    {
        if ($request->isMethod('post')) {
            $data = $request->all();
        }
        return view('admin.products.edit_product');
    }

    public function deleteProduct($id = null)
    {
        Products::where(['id' => $id])->delete();
        Alert::success('Deleted Successfully', 'Success Message');
        return redirect()->back()->with('flash_message_error', 'Product Deleted');
    }

    public function updateStatus(Request $request, $id = null)
    {
        $data = $request->all();
        Products::where('id', $data['id'])->update(['status' => $data['status']]);
    }

    public function products($id = null)
    {
        $productDetails = Products::with('attributes')->where('id', $id)->first();
        $ProductsAltImages = ProductsImages::where('product_id', $id)->get();
        $featuredProducts = Products::where(['featured_products' => 1])->get();
        // dd($featuredProducts);
        return view('wayshop.product_details')->with(compact('productDetails', 'ProductsAltImages', 'featuredProducts'));
    }

    public function addAttributes(Request $request, $id = null)
    {
        $productDetails = Products::with('attributes')->where(['id' => $id])->first();
        if ($request->isMethod('post')) {
            $data = $request->all();
//            echo "<pre>";print_r($data);die;
            foreach ($data['sku'] as $key => $val) {
                if (!empty($val)) {
                    //Prevent duplicate SKU Record
                    $attrCountSKU = ProductsAttributes::where('sku', $val)->count();
                    if ($attrCountSKU > 0) {
                        return redirect('/admin/add_attributes/' . $id)->with('flash_message_error', 'SKU is already exist please select another sku');
                    }
                    //Prevent duplicate Size Record
                    $attrCountSizes = ProductsAttributes::where(['product_id' => $id, 'size' => $data['size']
                    [$key]])->count();
                    if ($attrCountSizes > 0) {
                        return redirect('/admin/add_attributes/' . $id)->with('flash_message_error', 'SKU is already exist please select another sizes');
                    }
                    $attribute = new ProductsAttributes();
                    $attribute->product_id = $id;
                    $attribute->sku = $val;
                    $attribute->size = $data['size'][$key];
                    $attribute->price = $data['price'][$key];
                    $attribute->stock = $data['stock'][$key];
                    $attribute->save();
                }
            }
            return redirect('/admin/add_attributes/' . $id)->with('flash_message_success', 'Products attributes added successfully!');
        }
        return view('admin.products.add_attributes')->with(compact('productDetails'));
    }

    public function deleteAttributes($id = null)
    {
        ProductsAttributes::where(['id' => $id])->delete();
        return redirect()->back()->with('flash_message_error', 'Product Attribute is deleted!');
    }

    public function editAttributes(Request $request, $id = null)
    {
        if ($request->isMethod('post')) {
            $data = $request->all();
            foreach ($data['attr'] as $key => $attr) {
                ProductsAttributes::where(['id' => $data['attr'][$key]])->update(['sku' => $data['sku'][$key],
                    'size' => $data['size'][$key], 'price' => $data['price'][$key], 'stock' => $data['stock'][$key]]);
            }
        }
    }

    public function addImages(Request $request, $id = null)
    {
        $productDetails = Products::where(['id' => $id])->first();
        if ($request->isMethod('post')) {
            $data = $request->all();
            if ($request->hasFile('image')) {
                $image = $request->file('image')->getClientOriginalName();
                $request->image->move(public_path('uploads/image'), $image);
            }
            $images = new ProductsImages();

            $images->image = $image;

            $images->product_id = $data['product_id'];
            $images->save();
            return redirect('/admin/add_images/' . $id)->with('flash_message_success', 'Image has been updated');
        }


        $productImages = ProductsImages::where(['product_id' => $id])->get();
        return view('admin.products.add_images')->with(compact('productDetails', 'productImages'));
    }

    public function deleteAltImage($id = null)
    {
        $productImage = ProductsImages::where(['id' => $id])->first();

        $image_path = 'uploads/products/';
        if (file_exists($image_path . $productImage->image)) {
            unlink($image_path . $productImage->image);
        }
        ProductsImages::where(['id' => $id])->delete();
        Alert::success('Deleted', 'Success Message');
        return redirect()->back();
    }

    public function updateFeatured(Request $request, $id = null)
    {
        $data = $request->all();
        Products::where('id', $data['id'])->update(['featured_products' => $data['status']]);
    }

    public function getprice(Request $request)
    {
        $data = $request->all();
        $proArr = explode("-", $data['idSize']);
        $proAttr = ProductsAttributes::where(['product_id' => $proArr[0], 'size' => $proArr[1]])->first();
        echo $proAttr->price;
    }
    public function addtoCart(Request $request){
        Session::forget('CouponAmount');
        Session::forget('CouponCode');
    $data = $request->all();
    if (empty($data['user_email'])){
        $data['user_email']= '';
    }
    $session_id =Session::get('session_id');
    if (empty($session_id)){
        $session_id =  Str::random(40);
        Session::put('session_id',$session_id);
    }

    $sizeArr = explode('-',$data['size']);
    $countProducts = DB::table('cart')->where(['product_id'=>$data['product_id'],
        'product_color'=>$data['product_color'],'size'=>$sizeArr['1'],
        'price'=>$data['price'], 'session_id'=>$session_id])->count();
        if ($countProducts>0){
           return redirect()->back()->with('flash_message_error','Product already exists in cart');
        }else{
            DB::table('cart')->insert(['product_id'=>$data['product_id'],'product_name'=>$data['product_name'],
            'product_code'=>$data['product_code'],'product_color'=>$data['product_color'],'size'=>$sizeArr['1'],
            'price'=>$data['price'],'quantity'=>$data['quantity'],'user_email'=>$data['user_email'],
            'session_id'=>$session_id]);
        }


    return redirect('/cart')->with('flash_message_success','Product has been added in cart');

    }
    public function cart(Request $request){
        $session_id = Session::get('session_id');
        $userCart = DB::table('cart')->where(['session_id'=>$session_id])->get();
        foreach($userCart as $key=>$products){
            $productDetails = Products::where(['id'=>$products->product_id])->first();
            $userCart[$key]->image = $productDetails->image;
        }
        return view('wayshop.products.cart')->with(compact('userCart'));
    }
    public function deleteCartProduct($id = null){
        Session::forget('CouponAmount');
        Session::forget('CouponCode');
        DB::table('cart')->where('id',$id)->delete();
        return redirect('/cart')->with('flash_message_error','Product has been deleted!');
    }
    public function updateCartQuantity($id=null,$quantity=null){
        Session::forget('CouponAmount');
        Session::forget('CouponCode');
        DB::table('cart')->where('id',$id)->increment('quantity',$quantity);
        return redirect('/cart')->with('flash_message_success','Product Quantity has been updated Successfully');
    }
    public function applyCoupon(Request $request){
            Session::forget('CouponAmount');
            Session::forget('CouponCode');
        if ($request->isMethod('post')){
            $data = $request->all();
            $couponCount = Category::where('coupon_code',$data['coupon_code'])->count();
            if ($couponCount == 0){
                return redirect()->back()->with('flash_message_error','Coupon code does not exists');
            }else{
                $couponDetails = Category::where('coupon_code',$data['coupon_code'])->first();
                //Coupon code status
                if ($couponDetails->status==0){
                      return redirect()->back()->with('flash_message_error','Coupon code is not active');
                }
                //Check coupon expiry date
                $expiry_date = $couponDetails->expiry_date;
                $current_data = $data('Y-m-d');
                if ($expiry_date < $current_data){
                    return redirect()->back()->with('flash_message_error','Coupon code is Expired');
                }
                //Coupon is ready for discount
                $session_id = Session::get('session_id');
                $userCart = DB::table('cart')->where(['session_id'=>$session_id])->get();
                $total_amount = 0;
                foreach ($userCart as $item){
                    $total_amount = $total_amount + ($item->price*$item->quantity);
                }
                //Check if coupon amount is fixed or percentage
                if ($couponDetails->amount_type=="Fixed"){
                    $couponAmount = $couponDetails->amount;
                }else{
                    $couponAmount = $total_amount * ($couponDetails->amount/100);
                }
                //Add Coupon code in session
                Session::put('CouponAmount',$couponAmount);
                Session::put('CouponAmount',$data['coupon_code']);
                return redirect()->back()->with('flash_message_success','Coupon Code is Successffully Applied.You are Availing Discount');
            }
        }
    }

}
