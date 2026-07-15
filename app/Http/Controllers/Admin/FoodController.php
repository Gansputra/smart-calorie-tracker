<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Food;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FoodController extends Controller
{
    /**
     * Display a listing of all foods.
     */
    public function index(Request $request)
    {
        $query = Food::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $foods      = $query->orderBy('name')->paginate(15)->withQueryString();
        $categories = Food::categories();

        return view('admin.foods.index', compact('foods', 'categories'));
    }

    /**
     * Show the create food form.
     */
    public function create()
    {
        $categories = Food::categories();

        return view('admin.foods.create', compact('categories'));
    }

    /**
     * Store a new food.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'              => ['required', 'string', 'max:255'],
            'category'          => ['required', 'string', 'max:100'],
            'calories_per_100g' => ['required', 'numeric', 'min:0'],
            'protein_per_100g'  => ['required', 'numeric', 'min:0'],
            'carbs_per_100g'    => ['nullable', 'numeric', 'min:0'],
            'fat_per_100g'      => ['nullable', 'numeric', 'min:0'],
            'description'       => ['nullable', 'string', 'max:1000'],
            'image'             => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
            'is_active'         => ['boolean'],
        ], [
            'name.required'              => 'Nama makanan wajib diisi.',
            'calories_per_100g.required' => 'Kalori per 100g wajib diisi.',
            'protein_per_100g.required'  => 'Protein per 100g wajib diisi.',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('foods', 'public');
        }

        $validated['is_active'] = $request->boolean('is_active', true);

        Food::create($validated);

        return redirect()->route('admin.foods.index')
            ->with('success', 'Makanan berhasil ditambahkan! ✅');
    }

    /**
     * Show the edit food form.
     */
    public function edit(Food $food)
    {
        $categories = Food::categories();

        return view('admin.foods.edit', compact('food', 'categories'));
    }

    /**
     * Update an existing food.
     */
    public function update(Request $request, Food $food)
    {
        $validated = $request->validate([
            'name'              => ['required', 'string', 'max:255'],
            'category'          => ['required', 'string', 'max:100'],
            'calories_per_100g' => ['required', 'numeric', 'min:0'],
            'protein_per_100g'  => ['required', 'numeric', 'min:0'],
            'carbs_per_100g'    => ['nullable', 'numeric', 'min:0'],
            'fat_per_100g'      => ['nullable', 'numeric', 'min:0'],
            'description'       => ['nullable', 'string', 'max:1000'],
            'image'             => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
            'is_active'         => ['boolean'],
        ]);

        if ($request->hasFile('image')) {
            if ($food->image) {
                Storage::disk('public')->delete($food->image);
            }
            $validated['image'] = $request->file('image')->store('foods', 'public');
        }

        $validated['is_active'] = $request->boolean('is_active', true);

        $food->update($validated);

        return redirect()->route('admin.foods.index')
            ->with('success', 'Makanan berhasil diperbarui! ✅');
    }

    /**
     * Delete a food.
     */
    public function destroy(Food $food)
    {
        if ($food->image) {
            Storage::disk('public')->delete($food->image);
        }

        $food->delete();

        return redirect()->route('admin.foods.index')
            ->with('success', 'Makanan berhasil dihapus.');
    }
}
