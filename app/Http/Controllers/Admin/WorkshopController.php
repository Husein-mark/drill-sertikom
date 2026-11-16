<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Workshop;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class WorkshopController extends Controller
{
    /**
     * Tampilkan seluruh workshop yang dikelola panitia.
     */
    public function index(Request $request): View
    {
        $query = Workshop::query()->withCount('registrations');

        if ($search = $request->input('search')) {
            $query->where('title', 'like', "%{$search}%")
                ->orWhere('category', 'like', "%{$search}%")
                ->orWhere('instructor_name', 'like', "%{$search}%");
        }

        $workshops = $query->latest()->paginate(10)->withQueryString();

        return view('admin.workshops.index', [
            'workshops' => $workshops,
            'search' => $search,
        ]);
    }

    /**
     * Tampilkan formulir penambahan workshop baru.
     */
    public function create(): View
    {
        return view('admin.workshops.create');
    }

    /**
     * Simpan workshop baru ke database.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'instructor_name' => ['required', 'string', 'max:150'],
            'instructor_title' => ['nullable', 'string', 'max:200'],
            'schedule_date' => ['required', 'date', 'after:now'],
            'duration_hours' => ['required', 'integer', 'min:1', 'max:12'],
            'location' => ['required', 'string', 'max:255'],
            'price' => ['required', 'integer', 'min:0'],
            'quota' => ['required', 'integer', 'min:1'],
            'description' => ['required', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'title.required' => 'Judul workshop wajib diisi.',
            'category.required' => 'Kategori workshop wajib diisi.',
            'instructor_name.required' => 'Nama instruktur wajib diisi.',
            'schedule_date.required' => 'Tanggal & jam pelaksanaan wajib ditentukan.',
            'schedule_date.after' => 'Jadwal workshop harus tanggal di masa depan.',
            'price.required' => 'Harga tiket workshop wajib diisi (0 untuk gratis).',
            'quota.required' => 'Kapasitas kuota peserta wajib ditentukan.',
            'description.required' => 'Deskripsi materi workshop wajib diisi.',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['slug'] = Str::slug($validated['title']).'-'.Str::lower(Str::random(5));

        Workshop::create($validated);

        return redirect()->route('admin.workshops.index')
            ->with('success', 'Workshop baru berhasil dibuat dan dipublikasikan.');
    }

    /**
     * Tampilkan formulir edit workshop.
     */
    public function edit(Workshop $workshop): View
    {
        return view('admin.workshops.edit', [
            'workshop' => $workshop,
        ]);
    }

    /**
     * Perbarui data workshop.
     */
    public function update(Request $request, Workshop $workshop): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:100'],
            'instructor_name' => ['required', 'string', 'max:150'],
            'instructor_title' => ['nullable', 'string', 'max:200'],
            'schedule_date' => ['required', 'date'],
            'duration_hours' => ['required', 'integer', 'min:1', 'max:12'],
            'location' => ['required', 'string', 'max:255'],
            'price' => ['required', 'integer', 'min:0'],
            'quota' => ['required', 'integer', 'min:1'],
            'description' => ['required', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $workshop->update($validated);

        return redirect()->route('admin.workshops.index')
            ->with('success', 'Data workshop berhasil diperbarui.');
    }

    /**
     * Hapus workshop.
     */
    public function destroy(Workshop $workshop): RedirectResponse
    {
        // Cek apakah sudah ada peserta terdaftar
        if ($workshop->registrations()->count() > 0) {
            return back()->with('error', 'Workshop tidak dapat dihapus karena sudah memiliki peserta yang terdaftar.');
        }

        $workshop->delete();

        return redirect()->route('admin.workshops.index')
            ->with('success', 'Workshop berhasil dihapus.');
    }
}
