<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MedicalJournal;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MedicalJournalController extends Controller
{
    public function index()
    {
        $menu = Menu::where('slug', 'medical-journals')->firstOrFail();
        $groupedJournals = MedicalJournal::orderBy('year', 'desc')
            ->orderBy('order', 'asc')
            ->get()
            ->groupBy('year');

        return view('admin.medical-journals.index', compact('menu', 'groupedJournals'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pdf' => 'required|mimes:pdf|max:102400',
            'title' => 'required|string',
            'year' => 'required|integer'
        ]);

        try {
            $slug = $this->generateUniqueFilename($request->title);
            $filename = $slug . '.pdf';

            $request->file('pdf')->storeAs("medical-journals/{$request->year}", $filename, 'public');

            MedicalJournal::create([
                'title' => $request->title,
                'filename' => $filename,
                'year' => $request->year,
                'is_active' => 1,
                'order' => (MedicalJournal::where('year', $request->year)->max('order') ?? 0) + 1
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, MedicalJournal $medicalJournal)
    {
        $request->validate([
            'pdf' => 'nullable|mimes:pdf|max:102400',
            'title' => 'required|string',
            'year' => 'required|integer'
        ]);

        try {
            $oldYear = $medicalJournal->year;
            $oldFilename = $medicalJournal->filename;
            $newSlug = $this->generateUniqueFilename($request->title, $medicalJournal->id);
            $newFilename = $newSlug . '.pdf';

            $updateData = [
                'title' => $request->title,
                'filename' => $newFilename,
                'year' => $request->year,
                'is_active' => $request->input('is_active') == 1 ? 1 : 0
            ];

            if ($oldYear != $request->year) {
                $updateData['order'] = (MedicalJournal::where('year', $request->year)->max('order') ?? 0) + 1;
            }

            if ($request->hasFile('pdf')) {
                Storage::disk('public')->delete("medical-journals/{$oldYear}/{$oldFilename}");
                $request->file('pdf')->storeAs("medical-journals/{$request->year}", $newFilename, 'public');

                if ($oldYear != $request->year) {
                    $this->cleanupFolders($oldYear);
                }
            } elseif ($oldYear != $request->year || $oldFilename != $newFilename) {
                if (!Storage::disk('public')->exists("medical-journals/{$request->year}")) {
                    Storage::disk('public')->makeDirectory("medical-journals/{$request->year}");
                }
                Storage::disk('public')->move("medical-journals/{$oldYear}/{$oldFilename}", "medical-journals/{$request->year}/{$newFilename}");

                $this->cleanupFolders($oldYear);
            }

            $medicalJournal->update($updateData);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function updateOrder(Request $request)
    {
        foreach ($request->orders as $item) {
            MedicalJournal::where('id', $item['id'])->update(['order' => $item['order']]);
        }
        return response()->json(['success' => true]);
    }

    public function delete(MedicalJournal $medicalJournal)
    {
        try {
            $year = $medicalJournal->year;
            Storage::disk('public')->delete("medical-journals/{$year}/{$medicalJournal->filename}");

            $medicalJournal->delete();

            $this->cleanupFolders($year);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    private function cleanupFolders($year)
    {
        $yearPath = "medical-journals/{$year}";
        $rootPath = "medical-journals";

        if (Storage::disk('public')->exists($yearPath)) {
            $files = Storage::disk('public')->allFiles($yearPath);
            if (empty($files)) {
                Storage::disk('public')->deleteDirectory($yearPath);
            }
        }

        if (Storage::disk('public')->exists($rootPath)) {
            $subFolders = Storage::disk('public')->directories($rootPath);
            $rootFiles = Storage::disk('public')->files($rootPath);

            if (empty($subFolders) && empty($rootFiles)) {
                Storage::disk('public')->deleteDirectory($rootPath);
            }
        }
    }

    private function generateUniqueFilename($title, $ignoreId = null)
    {
        $base = Str::slug(str_replace('&', 'and', $title));
        $slug = $base;
        $counter = 2;

        while (
            MedicalJournal::where('filename', $slug . '.pdf')
                ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $base . '-' . $counter;
            $counter++;
        }
        return $slug;
    }

    public function servePdf($path, $year, $filename)
    {
        $storagePath = storage_path("app/public/medical-journals/{$year}/{$filename}");

        abort_if(!file_exists($storagePath), 404);

        $journal = MedicalJournal::where('year', $year)
            ->where('filename', $filename)
            ->firstOrFail();

        return response()->download(
            $storagePath,
            $journal->title . '.pdf',
            [
                'Content-Type' => 'application/pdf',
                'Cache-Control' => 'public, max-age=86400',
            ]
        );
    }

    public function frontendIndex($menu)
    {
        $groupedJournals = MedicalJournal::where('is_active', 1)
            ->orderBy('year', 'desc')
            ->orderBy('order', 'asc')
            ->get()
            ->groupBy('year');

        return view('medical-journals.index', compact('groupedJournals', 'menu'));
    }
}