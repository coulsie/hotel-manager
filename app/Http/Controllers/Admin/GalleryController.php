<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GalleryController extends Controller
{
    public function index()
    {
        $items = DB::table('gallery_items')
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->get();

        return view('admin.gallery.index', compact('items'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category'    => 'required|string|in:chambres,restaurant,piscine,salles,spectacle',
            'title'       => 'required|string|max:191',
            'description' => 'nullable|string|max:255',
            'image'       => 'required|image|mimes:jpeg,png,jpg,webp|max:4096',
        ]);

        $path = $this->storeOptimizedImage($request->file('image'));

        DB::table('gallery_items')->insert([
            'category'    => $validated['category'],
            'title'       => $validated['title'],
            'description' => $validated['description'] ?? null,
            'image'       => $path,
            'sort_order'  => (int) DB::table('gallery_items')->max('sort_order') + 1,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        return back()->with('success', 'La photo a été ajoutée à l\'album de l\'hôtel.');
    }

    public function update(Request $request, int $id)
    {
        $item = DB::table('gallery_items')->where('id', $id)->firstOrFail();

        $validated = $request->validate([
            'category'    => 'required|string|in:chambres,restaurant,piscine,salles,spectacle',
            'title'       => 'required|string|max:191',
            'description' => 'nullable|string|max:255',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:4096',
        ]);

        $data = [
            'category'    => $validated['category'],
            'title'       => $validated['title'],
            'description' => $validated['description'] ?? null,
            'updated_at'  => now(),
        ];

        if ($request->hasFile('image')) {
            // Supprimer l'ancien fichier uniquement s'il est dans gallery/ (ne pas supprimer rooms/*)
            if (Str::startsWith($item->image, 'gallery/') && Storage::disk('public')->exists($item->image)) {
                Storage::disk('public')->delete($item->image);
            }
            $data['image'] = $this->storeOptimizedImage($request->file('image'));
        }

        DB::table('gallery_items')->where('id', $id)->update($data);

        return back()->with('success', 'L\'élément de la galerie a été mis à jour.');
    }

    public function destroy(int $id)
    {
        $item = DB::table('gallery_items')->where('id', $id)->firstOrFail();

        if (Str::startsWith($item->image, 'gallery/') && Storage::disk('public')->exists($item->image)) {
            Storage::disk('public')->delete($item->image);
        }

        DB::table('gallery_items')->where('id', $id)->delete();

        return back()->with('success', 'La photo a été supprimée de l\'album.');
    }

    /**
     * Redimensionne et compresse automatiquement l'image en 800x500 (qualité 80%)
     * pour garantir un chargement ultra-rapide sur le site public.
     */
    private function storeOptimizedImage($uploadedFile): string
    {
        $filename = 'gallery/' . uniqid('hotel_', true) . '.jpg';
        $raw = file_get_contents($uploadedFile->getRealPath());
        $src = @imagecreatefromstring($raw);

        if ($src) {
            $dst = imagecreatetruecolor(800, 500);
            imagecopyresampled($dst, $src, 0, 0, 0, 0, 800, 500, imagesx($src), imagesy($src));
            ob_start();
            imagejpeg($dst, null, 80);
            $optimized = ob_get_clean();
            imagedestroy($src);
            imagedestroy($dst);

            Storage::disk('public')->put($filename, $optimized);
            return $filename;
        }

        return $uploadedFile->store('gallery', 'public');
    }
}
