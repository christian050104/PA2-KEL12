<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MenuStoreRequest;
use App\Models\Category;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $menus = Menu::all();
        return view('admin.menus.index', compact('menus'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all();
        return view('admin.menus.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MenuStoreRequest $request)
    {
        $image = $request->file('image')->store('public/menus');

        $menu = Menu::create([
            'name' => $request->name,
            'description' => $request->description,
            'image' => $image,
            'price' => $request->price
        ]);

        if ($request->has('categories')) {
            $menu->categories()->attach($request->categories);
        }
        return to_route('admin.menus.index');
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
    public function edit(Menu $menu)
    {
        $categories = Category::all();
        return view('admin.menus.edit', compact('menu', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Menu $menu)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'price' => 'required'
        ]);

        // Simpan lokasi gambar sebelum pembaruan
        $image = $menu->image;

        if ($request->hasFile('image')) {
            // Pastikan untuk menghapus gambar lama hanya jika ada
            Storage::delete($menu->image);

            // Simpan gambar baru
            $image = $request->file('image')->store('public/categories');
        }

        // Lakukan pembaruan data menu
        $menu->update([
            'name' => $request->name,
            'description' => $request->description,
            'image' => $image,
            'price' => $request->price
        ]);
        if ($request->has('categories')) {
            $menu->categories()->sync($request->categories);
        }

        // Redirect ke halaman indeks kategori
        return redirect()->route('admin.menus.index');
    }


    /**
     * Remove the specified resource from storage.
     */
    /**
 * Remove the specified resource from storage.
 */
public function destroy(Menu $menu)
{
    // Hapus terlebih dahulu hubungan terkait dari tabel pivot
    $menu->categories()->detach();

    // Hapus gambar terkait dari penyimpanan
    Storage::delete($menu->image);

    // Hapus entitas menu dari database
    $menu->delete();

    // Redirect ke halaman indeks menu
    return redirect()->route('admin.menus.index');
}

}
