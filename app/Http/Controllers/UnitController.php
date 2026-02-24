<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function index()
    {
        $units = Unit::paginate(10);
        return view('units.index', compact('units'));
    }

    public function create()
    {
        return view('units.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:units',
            'abbreviation' => 'required|string|max:10|unique:units',
            'active' => 'boolean',
        ]);

        $data = $request->all();
        $data['active'] = $request->has('active');

        Unit::create($data);

        return redirect()->route('units.index')
            ->with('success', 'Unidad creada exitosamente.');
    }

    public function show(Unit $unit)
    {
        return view('units.show', compact('unit'));
    }

    public function edit(Unit $unit)
    {
        return view('units.edit', compact('unit'));
    }

    public function update(Request $request, Unit $unit)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:units,name,'.$unit->id,
            'abbreviation' => 'required|string|max:10|unique:units,abbreviation,'.$unit->id,
            'active' => 'boolean',
        ]);

        $data = $request->all();
        $data['active'] = $request->has('active');

        $unit->update($data);

        return redirect()->route('units.index')
            ->with('success', 'Unidad actualizada exitosamente.');
    }

    public function destroy(Unit $unit)
    {
        // Verificar si hay productos usando esta unidad
        if ($unit->products()->count() > 0) {
            return redirect()->route('units.index')
                ->with('error', 'No se puede eliminar la unidad porque está siendo utilizada por productos.');
        }

        $unit->delete();
        return redirect()->route('units.index')
            ->with('success', 'Unidad eliminada exitosamente.');
    }
}
