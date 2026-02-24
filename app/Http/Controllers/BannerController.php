<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::orderBy('order')->paginate(10);
        return view('banners.index', compact('banners'));
    }

    public function create()
    {
        return view('banners.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'url' => 'nullable|url',
            'type' => 'required|in:principal,publicitario',
            'active' => 'boolean',
            'order' => 'nullable|integer|min:0',
        ]);

        $data = $request->all();
        
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('banners', 'public');
            $data['image'] = $imagePath;
        }

        $data['active'] = $request->has('active');
        $data['order'] = $request->order ?? 0;

        Banner::create($data);

        return redirect()->route('banners.index')
            ->with('success', 'Banner creado exitosamente.');
    }

    public function show(Banner $banner)
    {
        return view('banners.show', compact('banner'));
    }

    public function edit(Banner $banner)
    {
        return view('banners.edit', compact('banner'));
    }

    public function update(Request $request, Banner $banner)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'url' => 'nullable|url',
            'type' => 'required|in:principal,publicitario',
            'active' => 'boolean',
            'order' => 'nullable|integer|min:0',
        ]);

        $data = $request->all();
        
        if ($request->hasFile('image')) {
            // Eliminar imagen anterior si existe
            if ($banner->image) {
                Storage::disk('public')->delete($banner->image);
            }
            
            $imagePath = $request->file('image')->store('banners', 'public');
            $data['image'] = $imagePath;
        }

        $data['active'] = $request->has('active');
        $data['order'] = $request->order ?? $banner->order;

        $banner->update($data);

        return redirect()->route('banners.index')
            ->with('success', 'Banner actualizado exitosamente.');
    }

    public function destroy(Banner $banner)
    {
        // Eliminar imagen si existe
        if ($banner->image) {
            Storage::disk('public')->delete($banner->image);
        }
        
        $banner->delete();
        return redirect()->route('banners.index')
            ->with('success', 'Banner eliminado exitosamente.');
    }

    public function reorder(Request $request)
    {
        $banners = $request->input('banners');
        
        foreach ($banners as $index => $bannerId) {
            Banner::where('id', $bannerId)->update(['order' => $index]);
        }
        
        return response()->json(['success' => true]);
    }
}
