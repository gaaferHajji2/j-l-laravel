<?php

namespace App\Http\Controllers;

use App\Service\ProductService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;

        $this->middleware('permission:products.view')->only(['index', 'show']);
        $this->middleware('permission:products.create')->only('store');
        $this->middleware('permission:products.edit')->only('update');
        $this->middleware('permissions:products.delete')->only('destroy');
    }

    public function index(): JsonResponse
    {
        $products = $this->productService->getAllProducts();
        return response()->json($products);
    }

    public function show(int $id): JsonResponse
    {
        try{
            $product = $this->productService->getProduct($id);
            return response()->json($product);
        } catch(NotFoundHttpException $e) {
            return response()->json([], 404);
        }
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'description' => 'nullable|string',
        ]);

        $product = $this->productService->createProduct($validated);
        return response()->json($product, 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'price' => 'sometimes|numeric',
            'description' => 'nullable|string',
        ]);

        $product = $this->productService->updateProduct($id, $validated);
        return response()->json($product);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->productService->deleteProduct($id);
        return response()->json(['message' => 'Product deleted'], 204);
    }
}
