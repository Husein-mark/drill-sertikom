<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use App\Models\Workshop;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RegistrationManagerController extends Controller
{
    /**
     * Tampilkan daftar seluruh pendaftar dan instrumen komparasi query N+1 vs Eager Loading.
     * Sesuai ketentuan Langkah 05 LKPD.
     */
    public function index(Request $request): View
    {
        $mode = $request->input('mode', 'eager'); // 'eager' atau 'lazy'
        $statusFilter = $request->input('status');
        $workshopId = $request->input('workshop_id');

        // Aktifkan pemantauan log query SQL
        DB::flushQueryLog();
        DB::enableQueryLog();
        $startTime = microtime(true);

        if ($mode === 'lazy') {
            // SIMULASI N+1 QUERY (Tanpa Eager Loading)
            $query = Registration::query();

            if ($statusFilter) {
                $query->where('status', $statusFilter);
            }
            if ($workshopId) {
                $query->where('workshop_id', $workshopId);
            }

            $registrations = $query->latest()->get();

            // Memaksa evaluasi relasi satu per satu (N+1)
            foreach ($registrations as $item) {
                $userName = $item->user->name ?? '-';
                $workshopTitle = $item->workshop->title ?? '-';
            }
        } else {
            // DENGAN EAGER LOADING (with 'user' & 'workshop')
            $query = Registration::query()->with(['user', 'workshop']);

            if ($statusFilter) {
                $query->where('status', $statusFilter);
            }
            if ($workshopId) {
                $query->where('workshop_id', $workshopId);
            }

            $registrations = $query->latest()->get();

            foreach ($registrations as $item) {
                $userName = $item->user->name ?? '-';
                $workshopTitle = $item->workshop->title ?? '-';
            }
        }

        $executionTime = round((microtime(true) - $startTime) * 1000, 2);
        $queries = DB::getQueryLog();
        $queryCount = count($queries);

        $workshops = Workshop::query()->orderBy('title')->get();

        return view('admin.registrations.index', [
            'registrations' => $registrations,
            'workshops' => $workshops,
            'mode' => $mode,
            'queryCount' => $queryCount,
            'executionTime' => $executionTime,
            'queries' => $queries,
            'statusFilter' => $statusFilter,
            'workshopId' => $workshopId,
        ]);
    }

    /**
     * Verifikasi pembayaran secara manual oleh panitia.
     */
    public function manualVerify(Registration $registration): RedirectResponse
    {
        $registration->markAsPaid('Verifikasi Manual Panitia', 'PANITIA-MANUAL-'.strtoupper(uniqid()));

        return back()->with('success', 'Pendaftaran '.$registration->registration_number.' telah diverifikasi lunas. Tiket telah aktif.');
    }

    /**
     * Batalkan pendaftaran.
     */
    public function cancel(Registration $registration): RedirectResponse
    {
        $registration->update(['status' => 'cancelled']);

        return back()->with('info', 'Pendaftaran '.$registration->registration_number.' telah dibatalkan.');
    }
}
