<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check() && in_array(Auth::user()->role, ['organizer', 'system_admin'], true)) {
            return to_route(Auth::user()->role === 'organizer' ? 'organizer.dashboard' : 'system.dashboard');
        }

        return view('admin.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'role' => ['required', Rule::in(['system_admin', 'organizer'])],
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $role = $credentials['role'];
        $user = User::where('email', $credentials['email'])->first();

        if ($user && $user->role !== $role) {
            $expectedRoleName = $user->role === 'system_admin' ? 'Sistem Yöneticisi' : ($user->role === 'organizer' ? 'Organizatör' : 'Müşteri');
            return back()->withErrors([
                'email' => "Bu hesap {$expectedRoleName} rolüne sahiptir. Seçtiğiniz panel türüyle giriş yapamazsınız.",
            ])->onlyInput('email', 'role');
        }

        $authenticated = Auth::attempt([
            'email' => $credentials['email'],
            'password' => $credentials['password'],
            'role' => $role,
        ], $request->boolean('remember'));

        if (! $authenticated) {
            return back()->withErrors(['email' => 'Seçtiğiniz yönetici türü ve giriş bilgileriniz eşleşmiyor.'])->onlyInput('email', 'role');
        }

        if ($role === 'organizer') {
            $isPaid = DB::table('organizer_fees')
                ->where('organizer_id', Auth::id())
                ->where('year', now()->year)
                ->where('status', 'paid')
                ->exists();

            if (Auth::user()->organizer_status !== 'active' || ! $isPaid) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Organizatör hesabınız henüz etkinleştirilmedi veya yıllık platform aidatınız tamamlanmadı. Lütfen sistem yöneticisiyle iletişime geçin.',
                ])->onlyInput('email', 'role');
            }
        }

        $request->session()->regenerate();

        return to_route($role === 'organizer' ? 'organizer.dashboard' : 'system.dashboard');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return to_route('admin.login');
    }

    public function organizerDashboard(): View
    {
        $organizerId = Auth::id();
        $period = request('period', 'month');
        [$start, $end] = $this->periodDates($period);

        $reservationQuery = DB::table('reservations')->where('organizer_id', $organizerId);
        $reservations = (clone $reservationQuery)->leftJoin('users', 'users.id', '=', 'reservations.user_id')
            ->select('reservations.*', 'users.name as customer_name', 'users.email as customer_email', 'users.phone as customer_phone')
            ->latest('reservations.created_at')->limit(50)->get();

        $messages = DB::table('user_messages')->where('organizer_id', $organizerId)->latest()->limit(50)->get();

        $fee = DB::table('organizer_fees')->where('organizer_id', $organizerId)->where('year', now()->year)->first();
        $installments = $fee
            ? DB::table('organizer_fee_installments')->where('organizer_fee_id', $fee->id)->orderBy('installment_no')->get()
            : collect();

        $revenue = (float) DB::table('payments')
            ->where('status', 'paid')
            ->whereIn('reservation_id', (clone $reservationQuery)->select('id'))
            ->whereBetween('created_at', [$start, $end])
            ->sum('amount');

        $expenses = (float) DB::table('organizer_expenses')
            ->where('organizer_id', $organizerId)
            ->whereBetween('expense_date', [$start->toDateString(), $end->toDateString()])
            ->sum('amount');

        $reservationCount = (clone $reservationQuery)->whereBetween('created_at', [$start, $end])->count();
        $ticketsSold = (int) (clone $reservationQuery)->whereBetween('created_at', [$start, $end])->sum('guests');

        $listings = DB::table('listings')->where('organizer_id', $organizerId)->latest()->get()->map(function ($listing) {
            $listingRevenue = DB::table('payments')
                ->join('reservations', 'reservations.id', '=', 'payments.reservation_id')
                ->where('reservations.listing_id', $listing->id)
                ->where('payments.status', 'paid')
                ->sum('payments.amount');
            $listing->total_revenue = (float) $listingRevenue;
            return $listing;
        });

        return view('admin.organizer', [
            'listings' => $listings,
            'reservations' => $reservations,
            'messages' => $messages,
            'refunds' => DB::table('reservation_refunds')->whereIn('reservation_id', (clone $reservationQuery)->select('id'))->latest()->get(),
            'installments' => $installments,
            'fee' => $fee,
            'period' => $period,
            'revenue' => $revenue,
            'expenses' => $expenses,
            'net' => $revenue - $expenses,
            'reservationCount' => $reservationCount,
            'ticketsSold' => $ticketsSold,
            'recentExpenses' => DB::table('organizer_expenses')->where('organizer_id', $organizerId)->latest('expense_date')->limit(30)->get(),
        ]);
    }

    public function systemDashboard(): View
    {
        $period = request('period', 'month');
        [$start, $end] = $this->periodDates($period);
        $revenue = (float) DB::table('payments')->where('status', 'paid')->whereBetween('created_at', [$start, $end])->sum('amount');
        $expenses = (float) DB::table('organizer_expenses')->whereBetween('expense_date', [$start->toDateString(), $end->toDateString()])->sum('amount');
        $feesPaid = (float) DB::table('organizer_fees')->where('status', 'paid')->whereBetween('paid_at', [$start->toDateString(), $end->toDateString()])->sum('annual_amount');

        // Detailed Per-Organizer Financial & Membership Breakdown
        $organizers = User::where('role', 'organizer')->orderBy('name')->get()->map(function ($org) use ($start, $end) {
            $sales = (float) DB::table('payments')
                ->join('reservations', 'reservations.id', '=', 'payments.reservation_id')
                ->where('reservations.organizer_id', $org->id)
                ->where('payments.status', 'paid')
                ->whereBetween('payments.created_at', [$start, $end])
                ->sum('payments.amount');

            $orgExpenses = (float) DB::table('organizer_expenses')
                ->where('organizer_id', $org->id)
                ->whereBetween('expense_date', [$start->toDateString(), $end->toDateString()])
                ->sum('amount');

            $ticketCount = (int) DB::table('reservations')
                ->where('organizer_id', $org->id)
                ->whereBetween('created_at', [$start, $end])
                ->sum('guests');

            $fee = DB::table('organizer_fees')->where('organizer_id', $org->id)->where('year', now()->year)->first();
            $installments = $fee
                ? DB::table('organizer_fee_installments')->where('organizer_fee_id', $fee->id)->orderBy('installment_no')->get()
                : collect();

            $paidInstallmentsCount = $installments->where('status', 'paid')->count();
            $paidFeeAmount = (float) $installments->where('status', 'paid')->sum('amount');
            $remainingFeeAmount = $fee ? max(0, (float) $fee->annual_amount - $paidFeeAmount) : 0;

            $org->sales = $sales;
            $org->expenses = $orgExpenses;
            $org->net = $sales - $orgExpenses;
            $org->ticket_count = $ticketCount;
            $org->fee = $fee;
            $org->annual_amount = $fee ? $fee->annual_amount : null;
            $org->fee_status = $fee ? $fee->status : null;
            $org->fee_id = $fee ? $fee->id : null;
            $org->installments = $installments;
            $org->paid_installments_count = $paidInstallmentsCount;
            $org->total_installments_count = $installments->count();
            $org->remaining_fee_amount = $remainingFeeAmount;

            return $org;
        });

        return view('admin.system', [
            'period' => $period,
            'revenue' => $revenue,
            'expenses' => $expenses,
            'feesPaid' => $feesPaid,
            'net' => $revenue + $feesPaid - $expenses,
            'paidReservations' => DB::table('payments')->where('status', 'paid')->whereBetween('created_at', [$start, $end])->count(),
            'pendingPayments' => DB::table('payments')->join('reservations', 'reservations.id', '=', 'payments.reservation_id')
                ->join('users', 'users.id', '=', 'payments.user_id')->where('payments.status', 'pending')
                ->select('payments.*', 'reservations.item', 'users.name as customer_name', 'users.email as customer_email', 'users.phone as customer_phone')
                ->latest('payments.created_at')->limit(50)->get(),
            'organizers' => $organizers,
            'installments' => DB::table('organizer_fee_installments')->join('organizer_fees', 'organizer_fees.id', '=', 'organizer_fee_installments.organizer_fee_id')
                ->join('users', 'users.id', '=', 'organizer_fees.organizer_id')
                ->where('organizer_fees.year', now()->year)->where('organizer_fee_installments.status', 'unpaid')
                ->select('organizer_fee_installments.*', 'users.name as organizer_name', 'organizer_fees.year')->orderBy('due_date')->get(),
            'refunds' => DB::table('reservation_refunds')->join('reservations', 'reservations.id', '=', 'reservation_refunds.reservation_id')
                ->join('users', 'users.id', '=', 'reservation_refunds.requested_by')
                ->where('reservation_refunds.status', 'requested')->select('reservation_refunds.*', 'reservations.item', 'users.name as customer_name', 'users.email as customer_email')->latest('reservation_refunds.created_at')->get(),
            'messages' => DB::table('user_messages')->latest()->limit(30)->get(),
            'coupons' => DB::table('coupons')->latest()->limit(50)->get(),
            'draftListings' => DB::table('listings')->join('users', 'users.id', '=', 'listings.organizer_id')->where('listings.is_published', false)->select('listings.*', 'users.name as organizer_name')->latest('listings.created_at')->limit(30)->get(),
            'organizerCount' => User::where('role', 'organizer')->count(),
        ]);
    }

    public function createOrganizer(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'email' => ['required', 'email', 'max:190', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:30'],
            'password' => ['required', 'string', 'min:12'],
            'annual_amount' => ['required', 'numeric', 'min:1', 'max:10000000'],
            'installment_count' => ['required', 'integer', 'between:1,12'],
        ]);

        DB::transaction(function () use ($validated) {
            $organizer = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'password' => $validated['password'],
                'role' => 'organizer',
                'organizer_status' => 'pending',
            ]);
            $feeId = DB::table('organizer_fees')->insertGetId([
                'organizer_id' => $organizer->id,
                'year' => now()->year,
                'annual_amount' => $validated['annual_amount'],
                'status' => 'unpaid',
                'installment_count' => $validated['installment_count'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $amountPerInstallment = round($validated['annual_amount'] / $validated['installment_count'], 2);
            $remaining = (float) $validated['annual_amount'];

            for ($number = 1; $number <= $validated['installment_count']; $number++) {
                $amount = $number === (int) $validated['installment_count'] ? $remaining : $amountPerInstallment;
                $remaining -= $amount;
                DB::table('organizer_fee_installments')->insert([
                    'organizer_fee_id' => $feeId,
                    'installment_no' => $number,
                    'amount' => $amount,
                    'due_date' => now()->addMonths($number - 1)->toDateString(),
                    'status' => 'unpaid',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        });

        return to_route('system.dashboard')->with('status', 'Organizatör hesabı ve yıllık ücret planı oluşturuldu.');
    }

    public function toggleOrganizerStatus(int $organizer): RedirectResponse
    {
        $user = User::where('role', 'organizer')->findOrFail($organizer);
        $newStatus = $user->organizer_status === 'active' ? 'suspended' : 'active';
        $user->update(['organizer_status' => $newStatus, 'updated_at' => now()]);

        return back()->with('status', 'Organizatör durumu "' . ($newStatus === 'active' ? 'Aktif' : 'Askıya Alındı') . '" olarak güncellendi.');
    }

    public function markInstallmentPaid(int $installment): RedirectResponse
    {
        $row = DB::table('organizer_fee_installments')->join('organizer_fees', 'organizer_fees.id', '=', 'organizer_fee_installments.organizer_fee_id')
            ->where('organizer_fee_installments.id', $installment)->select('organizer_fee_installments.*', 'organizer_fees.organizer_id', 'organizer_fees.id as fee_id')->first();
        abort_unless($row !== null, 404);

        DB::transaction(function () use ($row) {
            DB::table('organizer_fee_installments')->where('id', $row->id)->update(['status' => 'paid', 'paid_at' => now()->toDateString(), 'updated_at' => now()]);
            $unpaid = DB::table('organizer_fee_installments')->where('organizer_fee_id', $row->fee_id)->where('status', 'unpaid')->count();
            if ($unpaid === 0) {
                DB::table('organizer_fees')->where('id', $row->fee_id)->update(['status' => 'paid', 'paid_at' => now()->toDateString(), 'updated_at' => now()]);
                DB::table('users')->where('id', $row->organizer_id)->update(['organizer_status' => 'active', 'updated_at' => now()]);
            }
        });

        return to_route('system.dashboard')->with('status', 'Taksit ödendi olarak kaydedildi.');
    }

    public function approveTransferPayment(int $payment): RedirectResponse
    {
        $row = DB::table('payments')->where('id', $payment)->first();
        abort_unless($row !== null, 404);
        abort_unless($row->method === 'bank_transfer' && $row->status === 'pending', 422, 'Yalnızca bekleyen havale/EFT kayıtları bu ekrandan onaylanabilir.');

        DB::transaction(function () use ($row) {
            DB::table('payments')->where('id', $row->id)->update(['status' => 'paid', 'updated_at' => now()]);
            DB::table('reservations')->where('id', $row->reservation_id)->update(['payment_status' => 'paid', 'status' => 'confirmed', 'updated_at' => now()]);
        });

        return to_route('system.dashboard')->with('status', 'Havale/EFT ödemesi onaylandı ve rezervasyon kesinleştirildi.');
    }

    public function createRenewalPlan(Request $request, int $organizer): RedirectResponse
    {
        $validated = $request->validate([
            'annual_amount' => ['required', 'numeric', 'min:1', 'max:10000000'],
            'installment_count' => ['required', 'integer', 'between:1,12'],
        ]);
        $user = User::where('role', 'organizer')->findOrFail($organizer);
        abort_if(DB::table('organizer_fees')->where('organizer_id', $user->id)->where('year', now()->year)->exists(), 422, 'Bu yıl için ücret planı zaten var.');

        DB::transaction(function () use ($validated, $user) {
            $feeId = DB::table('organizer_fees')->insertGetId([
                'organizer_id' => $user->id,
                'year' => now()->year,
                'annual_amount' => $validated['annual_amount'],
                'status' => 'unpaid',
                'installment_count' => $validated['installment_count'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $part = round($validated['annual_amount'] / $validated['installment_count'], 2);
            $remaining = (float) $validated['annual_amount'];
            for ($number = 1; $number <= $validated['installment_count']; $number++) {
                $amount = $number === (int) $validated['installment_count'] ? $remaining : $part;
                $remaining -= $amount;
                DB::table('organizer_fee_installments')->insert([
                    'organizer_fee_id' => $feeId,
                    'installment_no' => $number,
                    'amount' => $amount,
                    'due_date' => now()->addMonths($number - 1)->toDateString(),
                    'status' => 'unpaid',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            DB::table('users')->where('id', $user->id)->update(['organizer_status' => 'pending', 'updated_at' => now()]);
        });

        return to_route('system.dashboard')->with('status', 'Organizatörün bu yılki yenileme planı açıldı.');
    }

    public function storeListing(Request $request): RedirectResponse
    {
        abort_unless(Auth::user()->organizer_status === 'active', 403);
        $validated = $request->validate([
            'category' => ['required', 'string', 'max:80'],
            'custom_category' => ['nullable', 'string', 'max:80'],
            'title' => ['required', 'string', 'max:180'],
            'location' => ['required', 'string', 'max:180'],
            'description' => ['required', 'string', 'max:5000'],
            'image_url' => ['nullable', 'url', 'max:500'],
            'price' => ['required', 'numeric', 'min:0.01', 'max:10000000'],
            'capacity' => ['required', 'integer', 'between:1,1000000'],
        ]);

        $category = $validated['category'];
        if ($category === 'custom' && ! empty($validated['custom_category'])) {
            $category = Str::slug($validated['custom_category']);
        }
        unset($validated['custom_category']);

        $validated['category'] = $category;
        $validated['organizer_id'] = Auth::id();
        $validated['slug'] = Str::slug($validated['title']).'-'.Str::lower(Str::random(6));
        $validated['is_published'] = false;
        $validated['created_at'] = now();
        $validated['updated_at'] = now();
        DB::table('listings')->insert($validated);

        return to_route('organizer.dashboard')->with('status', 'İlanınız ve branşınız taslak olarak kaydedildi; yayına almak için sistem yöneticisi onayı gerekir.');
    }

    public function storeExpense(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'category' => ['required', 'string', 'max:80'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:10000000'],
            'expense_date' => ['required', 'date', 'before_or_equal:today'],
            'note' => ['nullable', 'string', 'max:2000'],
        ]);
        DB::table('organizer_expenses')->insert([...$validated, 'organizer_id' => Auth::id(), 'created_at' => now(), 'updated_at' => now()]);

        return to_route('organizer.dashboard')->with('status', 'Gider kaydedildi.');
    }

    public function updateListing(Request $request, int $listing): RedirectResponse
    {
        $validated = $request->validate([
            'price' => ['required', 'numeric', 'min:0.01', 'max:10000000'],
            'capacity' => ['required', 'integer', 'min:1', 'max:1000000'],
        ]);
        $ownedListing = DB::table('listings')->where('id', $listing)->where('organizer_id', Auth::id())->first();
        abort_unless($ownedListing !== null, 404);
        abort_if($validated['capacity'] < $ownedListing->reserved_count, 422, 'Kontenjan, satılmış katılımcı sayısından az olamaz.');

        DB::table('listings')->where('id', $listing)->update([
            'price' => $validated['price'],
            'capacity' => $validated['capacity'],
            'updated_at' => now(),
        ]);

        return to_route('organizer.dashboard')->with('status', 'İlan fiyatı ve kontenjanı güncellendi.');
    }

    public function reviewRefund(Request $request, int $refund): RedirectResponse
    {
        $validated = $request->validate(['decision' => ['required', Rule::in(['approved', 'rejected'])]]);
        $row = DB::table('reservation_refunds')->join('reservations', 'reservations.id', '=', 'reservation_refunds.reservation_id')
            ->where('reservation_refunds.id', $refund)->select('reservation_refunds.*', 'reservations.organizer_id')->first();
        abort_unless($row !== null, 404);
        if (Auth::user()->role === 'organizer') abort_unless((int) $row->organizer_id === Auth::id(), 403);

        DB::transaction(function () use ($row, $validated) {
            DB::table('reservation_refunds')->where('id', $row->id)->update([
                'status' => $validated['decision'],
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
                'updated_at' => now(),
            ]);
            if ($validated['decision'] === 'approved') {
                DB::table('reservations')->where('id', $row->reservation_id)->update(['status' => 'refunded', 'payment_status' => 'refunded', 'updated_at' => now()]);
                DB::table('payments')->where('reservation_id', $row->reservation_id)->update(['status' => 'refunded', 'updated_at' => now()]);
            }
        });

        return back()->with('status', $validated['decision'] === 'approved' ? 'İade talebi onaylandı.' : 'İade talebi reddedildi.');
    }

    public function updateMessageStatus(Request $request, int $message): RedirectResponse
    {
        $row = DB::table('user_messages')->where('id', $message)->first();
        abort_unless($row !== null, 404);
        if (Auth::user()->role === 'organizer') {
            abort_unless((int) $row->organizer_id === Auth::id(), 403);
        }
        $status = $request->input('status', 'read');
        DB::table('user_messages')->where('id', $message)->update(['status' => $status, 'updated_at' => now()]);

        return back()->with('status', 'Mesaj durumu güncellendi.');
    }

    public function storeCoupon(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'alpha_dash', 'max:60', 'unique:coupons,code'],
            'discount_type' => ['required', Rule::in(['percent', 'fixed'])],
            'discount_value' => ['required', 'numeric', 'gt:0', 'max:1000000'],
            'expires_at' => ['nullable', 'date', 'after:now'],
            'max_uses' => ['nullable', 'integer', 'min:1', 'max:1000000'],
        ]);
        if ($validated['discount_type'] === 'percent') $validated['discount_value'] = min((float) $validated['discount_value'], 100);
        $validated['code'] = Str::upper($validated['code']);
        $validated['is_active'] = true;
        $validated['uses_count'] = 0;
        $validated['created_at'] = now();
        $validated['updated_at'] = now();
        DB::table('coupons')->insert($validated);

        return to_route('system.dashboard')->with('status', 'Kupon oluşturuldu.');
    }

    public function publishListing(int $listing): RedirectResponse
    {
        $updated = DB::table('listings')->where('id', $listing)->update(['is_published' => true, 'updated_at' => now()]);
        abort_unless($updated, 404);

        return to_route('system.dashboard')->with('status', 'İlan yayına alındı.');
    }

    private function periodDates(string $period): array
    {
        $start = match ($period) {
            'day' => now()->startOfDay(),
            'year' => now()->startOfYear(),
            default => now()->startOfMonth(),
        };

        return [$start, now()->endOfDay()];
    }
}
