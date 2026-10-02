<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function reservations(): View
    {
        $reservations = DB::table('reservations')
            ->leftJoin('payments', 'payments.reservation_id', '=', 'reservations.id')
            ->leftJoin('users as organizers', 'organizers.id', '=', 'reservations.organizer_id')
            ->where('reservations.user_id', Auth::id())
            ->select(
                'reservations.*',
                'payments.method as payment_method',
                'payments.status as payment_record_status',
                'payments.provider_reference',
                'payments.coupon_code',
                'organizers.name as organizer_name'
            )
            ->latest('reservations.created_at')
            ->get()
            ->map(function ($res) {
                $refund = DB::table('reservation_refunds')
                    ->where('reservation_id', $res->id)
                    ->latest()
                    ->first();
                $res->refund = $refund;
                return $res;
            });

        return view('customer.reservations', compact('reservations'));
    }

    public function requestRefund(Request $request, int $reservation): RedirectResponse
    {
        $validated = $request->validate(['reason' => ['required', 'string', 'min:5', 'max:500']]);
        $booking = DB::table('reservations')->where('id', $reservation)->where('user_id', Auth::id())->first();
        abort_unless($booking !== null, 404);
        abort_if(in_array($booking->status, ['cancelled', 'refunded'], true), 422, 'Bu rezervasyon için iade talebi alınamıyor.');
        abort_if(DB::table('reservation_refunds')->where('reservation_id', $booking->id)->whereIn('status', ['requested', 'approved'])->exists(), 422, 'Bu rezervasyon için açık bir iade talebi zaten var.');

        DB::table('reservation_refunds')->insert([
            'reservation_id' => $booking->id,
            'requested_by' => Auth::id(),
            'amount' => $booking->total_amount,
            'reason' => $validated['reason'],
            'status' => 'requested',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return to_route('customer.reservations')->with('status', 'İade talebiniz yöneticiye iletildi.');
    }
}
