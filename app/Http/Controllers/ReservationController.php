<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Apartment;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function reserveApartment(Request $request, $apartmentId)
    {
        // التحقق من صحة البيانات
        $request->validate([
            'start_date' => 'required|date|after_or_equal:today',
            'end_date'   => 'required|date|after:start_date',
        ]);

        // التأكد أن الشقة موجودة
        $apartment = Apartment::findOrFail($apartmentId);

        // التحقق من وجود حجز متداخل لنفس الشقة
        $conflict = Reservation::where('apartment_id', $apartmentId)
            ->where(function($q) use ($request) {
                $q->where('start_date', '<=', $request->end_date)
                  ->where('end_date', '>=', $request->start_date);
            })->exists();

        if ($conflict) {
            return response()->json(['message' => 'Apartment already reserved in this period'], 400);
        }

        // إنشاء الحجز وربطه بالمستخدم الحالي
        $reservation = Reservation::create([
            'apartment_id' => $apartmentId,
            'user_id'      => auth()->id(), // المستأجر الحالي بعد تسجيل الدخول
            'start_date'   => $request->start_date,
            'end_date'     => $request->end_date,
            'status'       => 'pending' // بانتظار موافقة المؤجر أو الأدمن
        ]);

        return response()->json([
            'message'     => 'Reservation created successfully',
            'reservation' => $reservation
        ], 201);
    }



    public function updateReservation(Request $request, $reservationId)
{
    // جلب الحجز والتأكد أنه موجود
    $reservation = Reservation::findOrFail($reservationId);

    // التحقق أن المستخدم الحالي هو صاحب الحجز
    if ($reservation->user_id !== auth()->id()) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    // التحقق من صحة البيانات
    $request->validate([
        'start_date' => 'required|date|after_or_equal:today',
        'end_date'   => 'required|date|after:start_date',
        'status'     => 'in:pending,approved,cancelled' // إذا بدك تسمح بتغيير الحالة
    ]);

    // تحديث الحجز
    $reservation->update([
        'start_date' => $request->start_date,
        'end_date'   => $request->end_date,
        'status'     => $request->status ?? $reservation->status,
    ]);

    return response()->json([
        'message'     => 'Reservation updated successfully','reservation' => $reservation], 200);
}

}

