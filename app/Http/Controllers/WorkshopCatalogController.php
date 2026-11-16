<?php

namespace App\Http\Controllers;

use App\Models\Workshop;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WorkshopCatalogController extends Controller
{
    /**
     * Tampilkan katalog workshop yang tersedia untuk umum / peserta.
     */
    public function index(Request $request): View
    {
        $query = Workshop::query()->where('is_active', true);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search): void {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('instructor_name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($category = $request->input('category')) {
            $query->where('category', $category);
        }

        $categories = Workshop::query()
            ->where('is_active', true)
            ->distinct()
            ->pluck('category');

        $workshops = $query->orderBy('schedule_date', 'asc')->paginate(9)->withQueryString();

        return view('catalog.index', [
            'workshops' => $workshops,
            'categories' => $categories,
            'selectedCategory' => $request->input('category'),
            'search' => $request->input('search'),
        ]);
    }

    /**
     * Tampilkan detail workshop.
     */
    public function show(Workshop $workshop): View
    {
        $isRegistered = false;
        $existingRegistration = null;

        if (auth()->check()) {
            $existingRegistration = auth()->user()->registrations()
                ->where('workshop_id', $workshop->id)
                ->first();

            $isRegistered = $existingRegistration !== null;
        }

        return view('catalog.show', [
            'workshop' => $workshop,
            'isRegistered' => $isRegistered,
            'existingRegistration' => $existingRegistration,
        ]);
    }
}
