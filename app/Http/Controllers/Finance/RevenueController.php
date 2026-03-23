<?php

namespace App\Http\Controllers\Finance;

use App\Application\Services\RevenueService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RevenueController extends Controller
{
    public function __construct(
        private RevenueService $revenueService
    ) {}

    /**
     * Revenue dashboard / reports
     */
    public function index(Request $request)
    {
        try {
            $month = $request->get('month', now()->format('Y-m'));
            $summary = $this->revenueService->getSummary();
            $dailyReport = $this->revenueService->getDailyReport($month);
            $monthlyReport = $this->revenueService->getMonthlyReport(12);
            $unpaid = $this->revenueService->getUnpaidInspections();
            $payments = $this->revenueService->getPaidInspections($month);
            $lang = app()->getLocale();

            return view('finance.index', compact(
                'summary', 'dailyReport', 'monthlyReport', 'unpaid', 'payments', 'month', 'lang'
            ));
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error',
                app()->getLocale() === 'ar' ? 'حدث خطأ في تحميل التقارير.' : 'Error loading reports.');
        }
    }

    /**
     * Mark inspection as paid
     */
    public function markPaid(Request $request, string $id)
    {
        $request->validate([
            'discount' => 'nullable|numeric|min:0',
            'payment_note' => 'nullable|string|max:500',
        ]);

        try {
            $discount = (float) $request->input('discount', 0);
            $note = $request->input('payment_note');
            $this->revenueService->markAsPaid($id, $discount, $note);

            $lang = app()->getLocale();
            return back()->with('success', $lang === 'ar' ? 'تم تسجيل الدفع بنجاح.' : 'Payment recorded.');
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error',
                app()->getLocale() === 'ar' ? 'حدث خطأ.' : 'Error recording payment.');
        }
    }

    /**
     * Mark inspection as unpaid
     */
    public function markUnpaid(string $id)
    {
        try {
            $this->revenueService->markAsUnpaid($id);

            $lang = app()->getLocale();
            return back()->with('success', $lang === 'ar' ? 'تم إلغاء الدفع.' : 'Payment reversed.');
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error',
                app()->getLocale() === 'ar' ? 'حدث خطأ.' : 'Error.');
        }
    }
}