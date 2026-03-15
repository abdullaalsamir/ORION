<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Connect;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\ConnectMail;

class ConnectController extends Controller
{
    public function index()
    {
        $menu = Menu::where('slug', 'connect')->firstOrFail();

        $groupedQueries = Connect::latest()
            ->get()
            ->groupBy(function ($item) {
                return Carbon::parse($item->date)->format('Y-m-d');
            });

        return view('admin.connect.index', compact('menu', 'groupedQueries'));
    }

    public function frontendIndex($menu)
    {
        $date = now()->format('d/m/Y');

        return view('connect.index', compact('menu', 'date'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required',
            'subject' => 'required',
            'message' => 'required',
        ]);

        $data = $request->all();
        $data['date'] = now()->toDateString();

        $connect = Connect::create($data);

        try {
            Mail::to(config('mail.from.address'))->send(new ConnectMail($connect));
        } catch (\Exception $e) {
            \Log::error('Mail Error: ' . $e->getMessage());
        }

        return back()->with('success', 'Your message has been submitted successfully.');
    }

    public function delete(Connect $connect)
    {
        try {
            $connect->delete();
            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false], 500);
        }
    }
}