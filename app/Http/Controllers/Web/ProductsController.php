<?php
namespace App\Http\Controllers\Web;

use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use DB;

use App\Http\Controllers\Controller;
use App\Models\Product;

class ProductsController extends Controller {

	use ValidatesRequests;

	public function __construct()
    {
        $this->middleware('auth:web')->except('list');
    }

	public function list(Request $request) {

		$query = Product::select("products.*");

		$query->when($request->keywords, 
		fn($q)=> $q->where("name", "like", "%$request->keywords%"));

		$query->when($request->min_price, 
		fn($q)=> $q->where("price", ">=", $request->min_price));
		
		$query->when($request->max_price, fn($q)=> 
		$q->where("price", "<=", $request->max_price));
		
		$query->when($request->order_by, 
		fn($q)=> $q->orderBy($request->order_by, $request->order_direction??"ASC"));

		$products = $query->get();

		return view('products.list', compact('products'));
	}
	public function buyProduct(Request $request, $id) {
		$product = Product::findOrFail($id);
		$user = auth()->user();
	
		if ($user->credit < $product->price) {
			return redirect()->back()->with('error', 'Not enough credit to purchase this product.');
		}
	
		if ($product->quantity <= 0) {
			return redirect()->back()->with('error', 'This product is out of stock.');
		}
	
		$product->quantity -= 1;
		$product->save();
	
		$user->credit -= $product->price;
		$user->save();
	
		return redirect()->back()->with('success', 'Purchase successful!');
	}
	
	
	

	public function edit(Request $request, Product $product = null) {

		if(!auth()->user()) return redirect('/');

		$product = $product??new Product();

		return view('products.edit', compact('product'));
	}

	public function save(Request $request, Product $product = null) {

		$this->validate($request, [
	        'code' => ['required', 'string', 'max:32'],
	        'name' => ['required', 'string', 'max:128'],
	        'model' => ['required', 'string', 'max:256'],
	        'description' => ['required', 'string', 'max:1024'],
			'quantity' => ['required', 'numeric'],
	        'price' => ['required', 'numeric'],
	    ]);

		$product = $product??new Product();
		$product->fill($request->all());
		$product->save();

		return redirect()->route('products_list');
	}

	public function delete(Request $request, Product $product) {

		if(!auth()->user()->hasPermissionTo('delete_products')) abort(401);

		$product->delete();

		return redirect()->route('products_list');
	}
	public function submitReview(Request $request, $id) {
		if (!Auth::user()->can('submit_review')) {
			abort(403, 'Unauthorized');
		}
	
		$request->validate([
			'review' => 'required|string',
		]);
	
		Product::where('id', $id)->update([
			'review' => $request->review,
		]);
	
		return back()->with('success', 'Review submitted successfully!');
	}
	
} 