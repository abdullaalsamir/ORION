<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CorporateGovernance;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CorporateGovernanceController extends Controller
{
    public function index()
    {
        $menu = Menu::where('slug', 'corporate-governance')->firstOrFail();

        $groupedItems = CorporateGovernance::orderBy('publication_date', 'desc')
            ->orderBy('order', 'asc')
            ->get()
            ->groupBy(function ($item) {
                return \Carbon\Carbon::parse($item->publication_date)->format('Y');
            });

        return view('admin.corporate-governance.index', compact('menu', 'groupedItems'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pdf' => 'required|mimes:pdf|max:102400',
            'title' => 'required|string',
            'publication_date' => 'required|date',
            'description' => 'nullable|string|max:500'
        ]);

        try {
            $slug = $this->generateUniqueFilename($request->title);
            $filename = $slug . '.pdf';
            $request->file('pdf')->storeAs("corporate-governance", $filename, 'public');

            CorporateGovernance::create([
                'title' => $request->title,
                'filename' => $filename,
                'description' => $request->description,
                'publication_date' => $request->publication_date,
                'is_active' => 1,
                'order' => (CorporateGovernance::max('order') ?? 0) + 1
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, CorporateGovernance $corporateGovernance)
    {
        $request->validate([
            'pdf' => 'nullable|mimes:pdf|max:102400',
            'title' => 'required|string',
            'publication_date' => 'required|date',
            'description' => 'nullable|string|max:500'
        ]);

        try {
            $oldFilename = $corporateGovernance->filename;
            $newSlug = $this->generateUniqueFilename($request->title, $corporateGovernance->id);
            $newFilename = $newSlug . '.pdf';

            if ($request->hasFile('pdf')) {
                Storage::disk('public')->delete("corporate-governance/{$oldFilename}");
                $request->file('pdf')->storeAs("corporate-governance", $newFilename, 'public');
            } elseif ($oldFilename !== $newFilename) {
                Storage::disk('public')->move("corporate-governance/{$oldFilename}", "corporate-governance/{$newFilename}");
            }

            $corporateGovernance->update([
                'title' => $request->title,
                'filename' => $newFilename,
                'description' => $request->description,
                'publication_date' => $request->publication_date,
                'is_active' => $request->input('is_active') == 1 ? 1 : 0
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function updateOrder(Request $request)
    {
        foreach ($request->orders as $item) {
            CorporateGovernance::where('id', $item['id'])->update(['order' => $item['order']]);
        }
        return response()->json(['success' => true]);
    }

    public function delete(CorporateGovernance $corporateGovernance)
    {
        try {
            Storage::disk('public')->delete("corporate-governance/{$corporateGovernance->filename}");
            $corporateGovernance->delete();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    private function generateUniqueFilename($title, $ignoreId = null)
    {
        $base = Str::slug(str_replace('&', 'and', $title));
        $slug = $base;
        $counter = 2;
        while (
            CorporateGovernance::where('filename', $slug . '.pdf')
                ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base . '-' . $counter;
            $counter++;
        }
        return $slug;
    }

    public function servePdf($path, $filename)
    {
        $storagePath = storage_path("app/public/corporate-governance/{$filename}");

        if (!file_exists($storagePath)) {
            abort(404);
        }

        return response()->file($storagePath, [
            'Content-Type' => 'application/pdf',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }

    public function frontendIndex($menu)
    {
        $items = CorporateGovernance::where('is_active', 1)
            ->orderBy('publication_date', 'desc')
            ->orderBy('order', 'desc')
            ->get();

        return view('corporate-governance.index', compact('items', 'menu'));
    }
}