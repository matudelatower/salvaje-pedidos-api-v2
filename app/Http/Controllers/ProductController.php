<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Unit;
use App\Models\ProductMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with(['category', 'unit'])->paginate(10);
        return view('products.index', compact('products'));
    }

    public function create()
    {
        $categories = Category::where('active', true)->get();
        $units = Unit::where('active', true)->get();
        return view('products.create', compact('categories', 'units'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'ingredients' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'unit_id' => 'nullable|exists:units,id',
            'no_stock' => 'boolean',
            'active' => 'boolean',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'discount_start' => 'nullable|date',
            'discount_end' => 'nullable|date|after_or_equal:discount_start',
            'media_files.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,mp4,mov,avi|max:10240',
        ]);

        $data = $request->all();
        $data['no_stock'] = $request->has('no_stock');
        $data['active'] = $request->has('active');

        $product = Product::create($data);

        // Guardar archivos multimedia
        if ($request->hasFile('media_files')) {
            foreach ($request->file('media_files') as $index => $file) {
                $filePath = $file->store('products/' . $product->id, 'public');
                $fileType = in_array($file->getClientOriginalExtension(), ['mp4', 'mov', 'avi']) ? 'video' : 'image';
                
                ProductMedia::create([
                    'product_id' => $product->id,
                    'file_path' => $filePath,
                    'file_type' => $fileType,
                    'order' => $index
                ]);
            }
        }

        return redirect()->route('products.index')
            ->with('success', 'Producto creado exitosamente.');
    }

    public function show(Product $product)
    {
        $product->load(['category', 'unit', 'media' => function($query) {
            $query->orderBy('order');
        }]);
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::where('active', true)->get();
        $units = Unit::where('active', true)->get();
        $product->load(['media' => function($query) {
            $query->orderBy('order');
        }]);
        
        return view('products.edit', compact('product', 'categories', 'units'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'ingredients' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'unit_id' => 'nullable|exists:units,id',
            'no_stock' => 'boolean',
            'active' => 'boolean',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'discount_start' => 'nullable|date',
            'discount_end' => 'nullable|date|after_or_equal:discount_start',
            'media_files.*' => 'nullable|file|mimes:jpeg,png,jpg,gif,mp4,mov,avi|max:10240',
        ]);

        $data = $request->all();
        $data['no_stock'] = $request->has('no_stock');
        $data['active'] = $request->has('active');

        $product->update($data);

        // Agregar nuevos archivos multimedia
        if ($request->hasFile('media_files')) {
            $currentMaxOrder = $product->media()->max('order') ?? 0;
            
            foreach ($request->file('media_files') as $index => $file) {
                $filePath = $file->store('products/' . $product->id, 'public');
                $fileType = in_array($file->getClientOriginalExtension(), ['mp4', 'mov', 'avi']) ? 'video' : 'image';
                
                ProductMedia::create([
                    'product_id' => $product->id,
                    'file_path' => $filePath,
                    'file_type' => $fileType,
                    'order' => $currentMaxOrder + $index + 1
                ]);
            }
        }

        return redirect()->route('products.index')
            ->with('success', 'Producto actualizado exitosamente.');
    }

    public function destroy(Product $product)
    {
        // Eliminar archivos multimedia
        foreach ($product->media as $media) {
            Storage::disk('public')->delete($media->file_path);
            $media->delete();
        }
        
        $product->delete();
        return redirect()->route('products.index')
            ->with('success', 'Producto eliminado exitosamente.');
    }

    public function deleteMedia(ProductMedia $media)
    {
        Storage::disk('public')->delete($media->file_path);
        $media->delete();
        
        return response()->json(['success' => true]);
    }
}
