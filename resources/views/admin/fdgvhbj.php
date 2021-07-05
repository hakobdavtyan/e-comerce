
if ($request->isMethod('post')) {
$data = $request->all();
$image = null;
if ($request->hasFile('image')) {
$image = $request->file('image')->getClientOriginalName();

$request->image->move(public_path('upload'), $image);
}

            $product = new Products;
            $product->category_id = $data['category_id'];
            $product->name = $data['product_name'];
            $product->code = $data['product_code'];
            $product->color = $data['product_color'];
            if (!empty($data['product_descr'])) {
                $product->description = $data['product_descr'];
            } else {
                $product->description = '';
            }
            $product->price = $data['product_price'];
            $product->image = $image;




            $product->save();
            return redirect('/admin/add-product')->with('flash_message_success', 'Product has been added successfully');

        }

        return view('admin.products.add-product');
    }
