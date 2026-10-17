<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Salon;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', User::class);

        $stats = [
            'users' => User::where('role', '!=', 'admin')->count(),
            'active_users' => User::where('status', 'active')
                ->where('role', '!=', 'admin')
                ->count(),
            'pending_salons' => Salon::where('status', 'pending')->count(),
            'approved_salons' => Salon::where('status', 'approved')->count(),
            'suspended_salons' => Salon::where('status', 'suspended')->count(),
            'reviews' => Review::count(),
            'hidden_reviews' => Review::whereIn('status', [
                'hidden_by_admin',
                'hidden_by_suspension',
            ])->count(),
        ];

        $pendingSalons = Salon::with('owner')
            ->where('status', 'pending')
            ->latest()
            ->get();

        $salons = Salon::with('owner', 'approver')
            ->latest()
            ->paginate(8, ['*'], 'salons_page');

        $users = User::where('role', '!=', 'admin')
            ->latest()
            ->paginate(8, ['*'], 'users_page');

        $reviews = Review::with('user', 'salon')
            ->whereIn('status', [
                'hidden_by_admin',
                'hidden_by_suspension',
            ])
            ->latest()
            ->paginate(8, ['*'], 'reviews_page');

        return view('admin.dashboard', compact(
            'stats',
            'pendingSalons',
            'salons',
            'users',
            'reviews',
        ));
    }
}