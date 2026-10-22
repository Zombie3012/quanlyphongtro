<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Tenant;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    public function index(Request $request, Property $property)
    {
        $query = $property->tenants();

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', '%' . $search . '%')
                    ->orWhere('phone', 'like', '%' . $search . '%')
                    ->orWhere('cccd', 'like', '%' . $search . '%');
            });
        }

        $tenants = $query->orderBy('full_name')->paginate(10)->withQueryString();

        return view('tenants.index', compact(
            'tenants',
            'property'
        ));
    }

    public function create(Property $property)
    {
        return view('tenants.create', compact('property'));
    }

    public function store(Request $request, Property $property)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'cccd' => 'required|string|max:20|unique:tenants,cccd',
            'address' => 'required|string|max:255',
        ]);

        $property->tenants()->create([
            'full_name' => $request->full_name,
            'phone' => $request->phone,
            'cccd' => $request->cccd,
            'address' => $request->address,
        ]);

        return redirect()
            ->route('tenants.index', $property)
            ->with('success', 'Thêm người thuê thành công!');
    }

    public function edit(Property $property, Tenant $tenant)
    {
        $this->ensureTenantBelongsToProperty($tenant, $property);

        return view('tenants.edit', compact(
            'tenant',
            'property'
        ));
    }

    public function update(
        Request $request,
        Property $property,
        Tenant $tenant
    ) {
        $this->ensureTenantBelongsToProperty($tenant, $property);

        $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'cccd' => 'required|string|max:20|unique:tenants,cccd,' . $tenant->id,
            'address' => 'required|string|max:255',
        ]);

        $tenant->update([
            'full_name' => $request->full_name,
            'phone' => $request->phone,
            'cccd' => $request->cccd,
            'address' => $request->address,
        ]);

        return redirect()
            ->route('tenants.index', $property)
            ->with('success', 'Cập nhật người thuê thành công!');
    }

    public function destroy(Property $property, Tenant $tenant)
    {
        $this->ensureTenantBelongsToProperty($tenant, $property);

        $tenant->delete();

        return redirect()
            ->route('tenants.index', $property)
            ->with('success', 'Xóa người thuê thành công!');
    }

    private function ensureTenantBelongsToProperty(
        Tenant $tenant,
        Property $property
    ): void {
        abort_unless(
            (int) $tenant->property_id === (int) $property->id,
            404
        );
    }
}
