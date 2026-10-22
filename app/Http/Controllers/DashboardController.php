<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Property;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\User;

class DashboardController extends Controller
{
    public function admin()
    {
        return view('admin.index', [
            'totalProperties' => Property::count(),
            'totalUsers' => User::count(),
            'totalRooms' => Room::count(),
            'totalTenants' => Tenant::count(),
        ]);
    }

    /**
     * Dashboard tổng.
     *
     * Admin: xem toàn bộ hệ thống.
     * Manager: tự động chuyển về khu mình quản lý.
     */
    public function index()
    {
        $user = auth()->user();

        // Manager không được xem Dashboard tổng.
        if ($user->role === 'manager') {
            if (! $user->property_id) {
                abort(403, 'Tài khoản manager chưa được phân công khu nhà trọ.');
            }

            return redirect()->route(
                'properties.dashboard',
                $user->property_id
            );
        }

        // Admin xem toàn bộ hệ thống.
        $totalProperties = Property::count();
        $totalRooms = Room::count();
        $emptyRooms = Room::where('status', 'Trống')->count();
        $rentedRooms = Room::where('status', 'Đang thuê')->count();
        $totalTenants = Tenant::count();

        $revenue = Invoice::where('status', 'Đã thanh toán')
            ->sum('total');

        $unpaidInvoices = Invoice::where('status', 'Chưa thanh toán')
            ->count();

        // Bổ sung lấy danh sách khu trọ kèm số lượng phòng, phòng đang thuê và doanh thu của từng khu
        $properties = Property::withCount([
            'rooms as rooms_count',

            'rooms as rented_rooms_count' => function ($query) {
                $query->where('status', 'Đang thuê');
            },
        ])
            ->with([
                'contracts.invoices' => function ($query) {
                    $query->where('status', 'Đã thanh toán');
                },
            ])
            ->get();

        $properties->each(function ($property) {
            $property->revenue_total = $property->contracts->sum(
                fn ($contract) => $contract->invoices->sum('total')
            );
        });

        return view('dashboard', compact(
            'totalProperties',
            'totalRooms',
            'emptyRooms',
            'rentedRooms',
            'totalTenants',
            'revenue',
            'unpaidInvoices',
            'properties' // <-- Thêm biến này vào để view nhận được
        ));
    }

    /**
     * Dashboard của một khu nhà trọ.
     */
    public function property(Property $property)
    {
        $totalRooms = $property->rooms()->count();

        $emptyRooms = $property->rooms()
            ->where('status', 'Trống')
            ->count();

        $rentedRooms = $property->rooms()
            ->where('status', 'Đang thuê')
            ->count();

        $totalTenants = $property->tenants()->count();

        $revenue = Invoice::whereHas('contract', function ($query) use ($property) {
            $query->where('property_id', $property->id);
        })
            ->where('status', 'Đã thanh toán')
            ->sum('total');

        $unpaidInvoices = Invoice::whereHas('contract', function ($query) use ($property) {
            $query->where('property_id', $property->id);
        })
            ->where('status', 'Chưa thanh toán')
            ->count();

        return view('dashboard.property', compact(
            'property',
            'totalRooms',
            'emptyRooms',
            'rentedRooms',
            'totalTenants',
            'revenue',
            'unpaidInvoices'
        ));
    }
}
