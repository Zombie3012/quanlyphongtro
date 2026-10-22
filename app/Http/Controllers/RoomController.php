<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index(Request $request, Property $property)
    {
        $query = $property->rooms();

        if ($request->filled('search')) {
            $query->where(
                'room_number',
                'like',
                '%' . $request->search . '%'
            );
        }

        $rooms = $query->orderBy('room_number')->paginate(10)->withQueryString();

        return view('rooms.index', compact('rooms', 'property'));
    }

    public function create(Property $property)
    {
        return view('rooms.create', compact('property'));
    }

    public function store(Request $request, Property $property)
    {
        $request->validate([
            'room_number' => 'required|string|max:50|unique:rooms,room_number',
            'price' => 'required|numeric|min:0',
            'area' => 'required|numeric|min:0',
            'status' => 'required|in:Trống,Đang thuê,Đang sửa',
        ]);

        $property->rooms()->create([
            'room_number' => $request->room_number,
            'price' => $request->price,
            'area' => $request->area,
            'status' => $request->status,
        ]);

        return redirect()
            ->route('rooms.index', $property)
            ->with('success', 'Thêm phòng thành công!');
    }

    public function edit(Property $property, Room $room)
    {
        abort_unless(
            (int) $room->property_id === (int) $property->id,
            404
        );

        return view('rooms.edit', compact('room', 'property'));
    }

    public function update(
        Request $request,
        Property $property,
        Room $room
    ) {
        abort_unless(
            (int) $room->property_id === (int) $property->id,
            404
        );

        $request->validate([
            'room_number' =>
                'required|string|max:50|unique:rooms,room_number,' . $room->id,

            'price' => 'required|numeric|min:0',

            'area' => 'required|numeric|min:0',

            'status' =>
                'required|in:Trống,Đang thuê,Đang sửa',
        ]);

        $room->update([
            'room_number' => $request->room_number,
            'price' => $request->price,
            'area' => $request->area,
            'status' => $request->status,
        ]);

        return redirect()
            ->route('rooms.index', $property)
            ->with('success', 'Cập nhật phòng thành công!');
    }

    public function destroy(Property $property, Room $room)
    {
        abort_unless(
            (int) $room->property_id === (int) $property->id,
            404
        );

        $room->delete();

        return redirect()
            ->route('rooms.index', $property)
            ->with('success', 'Xóa phòng thành công!');
    }
}
