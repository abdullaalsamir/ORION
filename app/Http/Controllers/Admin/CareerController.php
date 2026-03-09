<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Career;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CareerController extends Controller
{
    public function index()
    {
        $careers = Career::orderBy('order')->get();
        return view('admin.career.index', compact('careers'));
    }

    public function store(Request $request)
    {
        $data = $this->validateCareer($request);
        $data['order'] = Career::max('order') + 1;

        $career = new Career($data);
        $career->slug = Career::generateUniqueSlug($career->title);

        $this->handleFiles($request, $career);
        $career->save();

        return back()->with('success', 'Job posted successfully');
    }

    public function update(Request $request, Career $career)
    {
        $data = $this->validateCareer($request);
        $data['is_active'] = $request->has('is_active');

        $career->fill($data);
        $this->handleFiles($request, $career);
        $career->save();

        return back()->with('success', 'Job updated successfully');
    }

    public function delete(Career $career)
    {
        if ($career->file_path) {
            Storage::disk('public')->delete($career->file_path);
        }
        if ($career->converted_images) {
            foreach ($career->converted_images as $img) {
                Storage::disk('public')->delete($img);
            }
        }
        $career->delete();

        return response()->json(['success' => true, 'message' => 'Job deleted successfully']);
    }

    public function updateOrder(Request $request)
    {
        foreach ($request->items as $item) {
            Career::where('id', $item['id'])->update(['order' => $item['order']]);
        }
        return response()->json(['success' => true]);
    }

    private function validateCareer(Request $request)
    {
        return $request->validate([
            'title' => 'required|string',
            'location' => 'required|string',
            'on_from' => 'nullable|date',
            'on_to' => 'nullable|date',
            'job_type' => 'required|in:Internship,Part-Time,Full-Time',
            'apply_type' => 'required|in:Online,Offline',
            'description' => 'nullable|string',
        ]);
    }

    private function handleFiles(Request $request, Career $career)
    {
        $slugTitle = $career->slug;

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $ext = strtolower($file->getClientOriginalExtension());

            if ($ext === 'pdf') {
                $filename = $slugTitle . '.pdf';
                $file->storeAs('career/pdf', $filename, 'public');
                $career->file_path = 'career/pdf/' . $filename;
            } else {
                $filename = $slugTitle . '.webp';
                $destPath = storage_path('app/public/career/image/' . $filename);

                if (!file_exists(dirname($destPath))) {
                    mkdir(dirname($destPath), 0755, true);
                }
                $this->processCareerImage($file->getPathname(), $destPath, $career);

                $career->file_path = 'career/image/' . $filename;
                $career->converted_images = null;
            }
        }

        if ($request->has('pdf_images')) {
            $imagePaths = [];
            $totalPages = count($request->pdf_images);

            foreach ($request->pdf_images as $index => $base64Data) {
                if ($totalPages === 1) {
                    $filename = $slugTitle . '.webp';
                } else {
                    $filename = $slugTitle . '-' . ($index + 1) . '.webp';
                }

                $destPath = storage_path('app/public/career/image/' . $filename);

                if (!file_exists(dirname($destPath))) {
                    mkdir(dirname($destPath), 0755, true);
                }

                $imageParts = explode(";base64,", $base64Data);
                $imageDecoded = base64_decode($imageParts[1]);
                file_put_contents($destPath, $imageDecoded);

                if ($index === 0) {
                    list($w, $h) = getimagesize($destPath);
                    $career->image_width = $w;
                    $career->image_height = $h;
                }

                $imagePaths[] = 'career/image/' . $filename;
            }

            $career->converted_images = $imagePaths;
        }
    }

    private function processCareerImage($sourcePath, $destinationPath, Career $career)
    {
        ini_set('memory_limit', '1024M');

        $info = getimagesize($sourcePath);
        $width = $info[0];
        $height = $info[1];
        $type = $info[2];

        switch ($type) {
            case IMAGETYPE_JPEG:
                $src = imagecreatefromjpeg($sourcePath);
                break;
            case IMAGETYPE_PNG:
                $src = imagecreatefrompng($sourcePath);
                break;
            case IMAGETYPE_WEBP:
                $src = imagecreatefromwebp($sourcePath);
                break;
            default:
                throw new \Exception('Unsupported image type.');
        }

        imagepalettetotruecolor($src);
        imagealphablending($src, true);
        imagesavealpha($src, true);

        $finalWidth = $width > 2000 ? 2000 : $width;
        $finalHeight = ($finalWidth / $width) * $height;

        $dst = imagecreatetruecolor($finalWidth, $finalHeight);
        imagealphablending($dst, false);
        imagesavealpha($dst, true);

        imagecopyresampled($dst, $src, 0, 0, 0, 0, $finalWidth, $finalHeight, $width, $height);
        imagewebp($dst, $destinationPath, 70);

        imagedestroy($src);
        imagedestroy($dst);

        $career->image_width = (int) $finalWidth;
        $career->image_height = (int) $finalHeight;
    }

    public function careerIndex()
    {
        $jobs = Career::where('is_active', 1)
            ->orderBy('order')
            ->get();

        $menu = (object) [
            'name' => 'Career',
            'content' => null
        ];

        return view('career.index', compact('jobs', 'menu'));
    }

    public function careerShow($slug)
    {
        $job = Career::where('slug', $slug)
            ->where('is_active', 1)
            ->firstOrFail();

        $menu = (object) [
            'name' => 'Career',
            'content' => null
        ];

        return view('career.show', compact('job', 'menu'));
    }

    public function submitApplication(Request $request, $slug)
    {
        $job = Career::where('slug', $slug)->firstOrFail();

        $request->validate([
            'cv' => 'required|mimes:pdf|max:102400',
        ]);

        $file = $request->file('cv');

        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $filename = time() . '_' . Str::slug($originalName) . '.pdf';

        $file->storeAs('career/uploadedCVs/' . $job->slug, $filename, 'public');

        return response()->json(['success' => true]);
    }

    public function serveCareerImage($filename)
    {
        $path = storage_path('app/public/career/image/' . $filename);

        if (!file_exists($path)) {
            abort(404);
        }

        return response()->file($path, ['Content-Type' => 'image/webp']);
    }
}