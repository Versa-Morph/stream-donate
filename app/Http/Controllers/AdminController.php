<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Donation;
use App\Models\Streamer;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AdminController extends Controller
{
    /**
     * Dashboard admin
     */
    public function dashboard(): View
    {
        $totalStreamers  = Streamer::count();
        $totalUsers      = User::count();
        $totalDonations  = Donation::count();
        $totalAmount     = Donation::sum('amount');
        $todayAmount     = Donation::whereDate('created_at', today())->sum('amount');
        $todayCount      = Donation::whereDate('created_at', today())->count();

        $recentDonations = Donation::with('streamer')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        $recentLogs = ActivityLog::with(['user', 'streamer'])
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        // Per-streamer summary
        $streamerStats = Streamer::with('user')
            ->withCount('donations')
            ->withSum('donations', 'amount')
            ->orderByDesc('donations_sum_amount')
            ->get();

        return view('admin.dashboard', compact(
            'totalStreamers', 'totalUsers', 'totalDonations', 'totalAmount',
            'todayAmount', 'todayCount', 'recentDonations', 'recentLogs', 'streamerStats'
        ));
    }

    /**
     * Daftar semua user
     */
    public function users(Request $request): View
    {
        $query = User::with('streamer')->orderBy('created_at', 'desc');

        if ($search = $request->input('search')) {
            // Escape LIKE wildcards to prevent slow query attacks
            $escapedSearch = $this->escapeLikeWildcards($search);
            $query->where(function ($q) use ($escapedSearch) {
                $q->where('name', 'like', "%{$escapedSearch}%")
                  ->orWhere('email', 'like', "%{$escapedSearch}%");
            });
        }

        if ($role = $request->input('role')) {
            $query->where('role', $role);
        }

        $users = $query->paginate(20)->withQueryString();

        return view('admin.users', compact('users'));
    }

    /**
     * Form tambah user baru
     */
    public function createUser(): View
    {
        return view('admin.users-create');
    }

    /**
     * Simpan user baru
     */
    public function storeUser(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role'     => ['required', 'in:admin,streamer'],
        ]);

        $user = User::create([
            'name'               => $validated['name'],
            'email'              => $validated['email'],
            'password'           => Hash::make($validated['password']),
            'role'               => $validated['role'],
            'is_active'          => true,
            'email_verified_at'  => now(),
        ]);

        ActivityLog::log(
            action: 'admin.user.created',
            description: "User {$user->email} [{$user->role}] dibuat oleh admin",
            userId: Auth::id(),
        );

        return redirect()->route('admin.users')
            ->with('success', "User {$user->email} berhasil dibuat.");
    }

    /**
     * Toggle aktif/nonaktif user
     */
    public function toggleUser(User $user): RedirectResponse
    {
        // Jangan nonaktifkan diri sendiri
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Tidak bisa menonaktifkan akun sendiri.');
        }

        // Protect other admins from being toggled
        if ($user->isAdmin()) {
            return back()->with('error', 'Tidak bisa mengubah status admin lain.');
        }

        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';

        ActivityLog::log(
            action: 'admin.user.toggled',
            description: "User {$user->email} {$status} oleh admin",
            userId: Auth::id(),
        );

        return back()->with('success', "User {$user->email} berhasil {$status}.");
    }

    /**
     * Reset password user
     */
    public function resetPassword(Request $request, User $user): RedirectResponse
    {
        // Protect other admins from password reset
        if ($user->isAdmin() && $user->id !== Auth::id()) {
            return back()->with('error', 'Tidak bisa mereset password admin lain.');
        }

        $validated = $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user->update(['password' => Hash::make($validated['password'])]);

        ActivityLog::log(
            action: 'admin.user.password_reset',
            description: "Password user {$user->email} direset oleh admin",
            userId: Auth::id(),
        );

        return back()->with('success', "Password user {$user->email} berhasil direset.");
    }

    /**
     * Semua donasi (semua streamer)
     */
    public function donations(Request $request): View
    {
        $query = Donation::with('streamer')->orderBy('created_at', 'desc');

        if ($search = $request->input('search')) {
            // Escape LIKE wildcards to prevent slow query attacks
            $escapedSearch = $this->escapeLikeWildcards($search);
            $query->where(function ($q) use ($escapedSearch) {
                $q->where('name', 'like', "%{$escapedSearch}%")
                  ->orWhere('message', 'like', "%{$escapedSearch}%");
            });
        }

        if ($streamerId = $request->input('streamer_id')) {
            $query->where('streamer_id', $streamerId);
        }

        $donations = $query->paginate(30)->withQueryString();
        $streamers = Streamer::orderBy('display_name')->get(['id', 'display_name', 'slug']);

        return view('admin.donations', compact('donations', 'streamers'));
    }

    /**
     * Activity logs
     */
    public function logs(Request $request): View
    {
        $query = ActivityLog::with(['user', 'streamer'])->orderBy('created_at', 'desc');

        if ($action = $request->input('action')) {
            // Escape LIKE wildcards to prevent slow query attacks
            $escapedAction = $this->escapeLikeWildcards($action);
            $query->where('action', 'like', "%{$escapedAction}%");
        }

        $logs = $query->paginate(30)->withQueryString();

        return view('admin.logs', compact('logs'));
    }

    /**
     * Impersonate streamer (login as)
     * Requires password confirmation for security
     */
    public function impersonate(Request $request, User $user): RedirectResponse
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Tidak bisa impersonate diri sendiri.');
        }

        // Prevent impersonating other admins
        if ($user->isAdmin()) {
            return back()->with('error', 'Tidak bisa impersonate admin lain.');
        }

        // Require password confirmation for sensitive action
        $request->validate([
            'password' => ['required', 'string'],
        ], [
            'password.required' => 'Password wajib diisi untuk konfirmasi.',
        ]);

        // Verify admin's password
        if (!Hash::check($request->password, Auth::user()->password)) {
            return back()->with('error', 'Password salah. Impersonasi dibatalkan.');
        }

        // Simpan admin ID di session
        session(['impersonating_admin_id' => Auth::id()]);

        Auth::login($user);

        ActivityLog::log(
            action: 'admin.impersonate.start',
            description: "Admin mulai impersonate user {$user->email}",
            userId: $user->id,
        );

        return redirect()->route('streamer.dashboard')
            ->with('info', "Kamu sedang login sebagai {$user->name}. Klik 'Stop Impersonate' untuk kembali.");
    }

    /**
     * Stop impersonate — kembali ke akun admin
     */
    public function stopImpersonate(): RedirectResponse
    {
        $adminId = session('impersonating_admin_id');

        // Bukan sesi impersonasi — redirect ke dashboard sesuai role
        if (!$adminId) {
            return redirect()->route('dashboard');
        }

        $admin = User::find($adminId);

        if (!$admin || !$admin->isAdmin()) {
            session()->forget('impersonating_admin_id');
            return redirect()->route('login');
        }

        session()->forget('impersonating_admin_id');
        Auth::login($admin);

        ActivityLog::log(
            action: 'admin.impersonate.stop',
            description: "Admin {$admin->email} berhenti impersonate",
            userId: $admin->id,
        );

        return redirect()->route('admin.dashboard')
            ->with('success', 'Kembali ke akun admin.');
    }

    /**
     * Hapus donasi
     */
    public function deleteDonation(Donation $donation): RedirectResponse
    {
        $info = "{$donation->name} / Rp " . number_format($donation->amount, 0, ',', '.');
        $donation->delete();

        ActivityLog::log(
            action: 'admin.donation.deleted',
            description: "Donasi dari {$info} dihapus oleh admin",
            userId: Auth::id(),
        );

        return back()->with('success', "Donasi berhasil dihapus.");
    }
}
