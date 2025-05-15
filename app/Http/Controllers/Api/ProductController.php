<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Events\ElasticsearchIndexEvent;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Api\CoreController;
use App\Repositories\ElasticSearchRepository;

class ProductController extends CoreController
{
    public function __construct()
    {
        //$this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    public function index(Request $request)
    {
        if (config('services.elasticsearch_enabled')) {
            // Fetch products from Elasticsearch
            $products = (new ElasticSearchRepository())->findMany($request);
        } else {
            // Fetch products from local DB
            $products = Product::with(['attributes', 'reviews'])->get();
        }
        if (empty($products)) {
            return response()->json(['message' => 'No products found'], 404);
        }

        return $this->setData($products)
            ->sendResponse('Products retrieved successfully', 'success', 200);
    }

    public function show($id)
    {
        if (config('services.elasticsearch_enabled')) {
            // Fetch product from Elasticsearch
            $product = (new ElasticSearchRepository())->getById($id);
        } else {
            // Fetch product from local DB
            $product = Product::findOrFail($id);
        }
        if (empty($product)) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        return $this->setData($product)
            ->sendResponse('Product retrieved successfully', 'success', 200);
    }

    public function store(Request $request)
    {
        // Validate incoming data
        $validator = Validator::make($request->all(), [
            'name'        => 'required|string',
            'description' => 'nullable|string',
            'price'       => 'required|numeric',
            'in_stock'    => 'required|boolean',
            'category'    => 'required|string',
            'latitude'    => 'nullable|numeric',
            'longitude'   => 'nullable|numeric',
            'tags'        => 'nullable|array',
            'attributes'  => 'nullable|array',
            'attributes.*.name'  => 'required_with:attributes|string',
            'attributes.*.value' => 'required_with:attributes|string',
        ]);

        if ($validator->fails()) {
            return $this->setErrors($validator->errors())
                ->sendResponse('Validation error', 'error', 422);
        }

        // Create product
        $product = Product::create([
            'name'        => $request->name,
            'description' => $request->description,
            'price'       => $request->price,
            'in_stock'    => $request->in_stock,
            'category'    => $request->category,
            'latitude'    => $request->latitude,
            'longitude'   => $request->longitude,
            'tags'        => $request->tags,
        ]);

        // Create attributes if provided
        if ($request->has('attributes')) {
            foreach ($request->attributes as $attr) {
                $product->attributes()->create([
                    'name' => $attr['name'],
                    'value' => $attr['value'],
                ]);
            }
        }

        if (config('services.elasticsearch_enabled'))
            event(new ElasticsearchIndexEvent($product));

        unset($product['id']);

        return $this->setData($product)
            ->sendResponse('Product created successfully', 'success', 201);
    }
}
