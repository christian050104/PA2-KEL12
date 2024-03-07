<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryStoreRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use function PHPUnit\Framework\returnSelf;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::all();
        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryStoreRequest $request)
    {
        $image = $request->file('image')->store('public/categories');

        Category::create([
            'name' => $request->name,
            'description' => $request->description,
            'image' => $image
        ]);
        return to_route('admin.categories.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
{
    $request->validate([
        'name' => 'required',
        'description' => 'required'
    ]);

    // Simpan lokasi gambar sebelum pembaruan
    $image = $category->image;

    if ($request->hasFile('image')) {
        // Jika ada file gambar baru yang diunggah, hapus gambar lama
        if ($category->image) {
            // Pastikan untuk menghapus gambar lama hanya jika ada
            Storage::delete($category->image);
        }
        
        // Simpan gambar baru
        $image = $request->file('image')->store('public/categories');
    }

    // Lakukan pembaruan data kategori
    $category->update([
        'name' => $request->name,
        'description' => $request->description,
        'image' => $image
    ]);

    // Redirect ke halaman indeks kategori
    return redirect()->route('admin.categories.index');
}


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        Storage::delete($category->image);
        $category->delete();

        return to_route('admin.categories.index');
    }
}
