<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use App\Models\User;
use App\Models\Workshop;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Dashboard ringkasan panitia.
     */
    public function index(): View
    {
        $totalWorkshops = Workshop::count();
        $totalRegistrations = Registration::count();
        $totalPaid = Registration::where('status', 'paid')->count();
        $totalPending = Registration::where('status', 'pending')->count();
        $totalRevenue = Registration::where('status', 'paid')->sum('amount');
        $totalPeserta = User::where('role', 'peserta')->count();

        $recentRegistrations = Registration::with(['user', 'workshop'])
            ->latest()
            ->take(6)
            ->get();

        $workshops = Workshop::withCount([
            'registrations as paid_count' => function ($query) {
                $query->where('status', 'paid');
            },
        ])->latest()->take(5)->get();

        return view('admin.dashboard', [
            'totalWorkshops' => $totalWorkshops,
            'totalRegistrations' => $totalRegistrations,
            'totalPaid' => $totalPaid,
            'totalPending' => $totalPending,
            'totalRevenue' => $totalRevenue,
            'totalPeserta' => $totalPeserta,
            'recentRegistrations' => $recentRegistrations,
            'workshops' => $workshops,
        ]);
    }
}
