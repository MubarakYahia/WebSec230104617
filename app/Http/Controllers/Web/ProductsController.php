<?php
namespace App\Http\Controllers\Web;

use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use DB;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Transaction;

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



public function buyProduct(Request $request, $productId)
{
    $product = Product::findOrFail($productId);
    $user = auth()->user();

    if ($user->credit < $product->price) {
        return redirect()->back()->with('error', 'Insufficient credit');
    }

    $user->credit -= $product->price;
    $user->save();

    $product->quantity -= 1;
    $product->save();

    Transaction::create([
        'user_id' => $user->id,
        'product_id' => $product->id,
        'quantity' => 1,
        'total_amount' => $product->price,
    ]);

    return redirect()->back()->with('success', 'Product purchased successfully');
}
public function addProduct(Request $request)
{
    if (auth()->user()->role->name !== 'Employee') {
        return redirect()->back()->with('error', 'Unauthorized');
    }

    Product::create([
        'name' => $request->name,
        'price' => $request->price,
        'quantity' => $request->quantity,
    ]);

    return redirect()->back()->with('success', 'Product added successfully');
}
public function handle($request, Closure $next, $role)
{
    if (auth()->user()->role->name !== $role) {
        return redirect()->back()->with('error', 'Unauthorized');
    }

    return $next($request);
{
    public function index()
    {
        $products = Product::all();
        return view('products.index', compact('products'));
    }

    public function create()
    {
        if (Auth::user()->role->name !== 'Employee') {
            abort(403);
        }

        return view('products.create');
    }

    public function store(Request $request)
    {
        if (Auth::user()->role->name !== 'Employee') {
            abort(403);
        }

        Product::create($request->only(['name', 'description', 'price', 'stock']));

        return redirect()->route('products.index')->with('success', 'Product added.');
    }

    public function edit(Product $product)
    {
        if (Auth::user()->role->name !== 'Employee') {
            abort(403);
        }

        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        if (Auth::user()->role->name !== 'Employee') {
            abort(403);
        }

        $product->update($request->only(['name', 'description', 'price', 'stock']));

        return redirect()->route('products.index')->with('success', 'Product updated.');
    }

    public function destroy(Product $product)
    {
        if (Auth::user()->role->name !== 'Employee') {
            abort(403);
        }

        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product deleted.');
    }
}use App\Models\Product;
use App\Models\Transaction;

public function buy(Product $product)
{
    $user = auth()->user();

    // Ensure only customers (role_id = 2) can buy
    if ($user->role_id !== 2) {
        abort(403, 'Only customers can purchase products.');
    }

    // Check if stock is available
    if ($product->quantity <= 0) {
        return redirect()->back()->with('error', 'Product is out of stock.');
    }

    // Check if customer has enough credit
    if ($user->credit < $product->price) {
        return redirect()->back()->with('error', 'Insufficient credit to buy this product.');
    }

    // Deduct price from credit and reduce product quantity
    $user->credit -= $product->price;
    $user->save();

    $product->quantity -= 1;
    $product->save();

    // Log transaction (optional)
    Transaction::create([
        'user_id' => $user->id,
        'product_id' => $product->id,
        'amount' => $product->price
    ]);

    return redirect()->back()->with('success', 'Product purchased successfully!');
}
} }