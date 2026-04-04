<?php

namespace App\Http\Controllers\Settings;

use App\Domain\Models\Setting;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    private array $settingKeys = [
        'company_name_ar',
        'company_name_en',
        'company_address_ar',
        'company_address_en',
        'company_phone',
        'company_email',
        'company_website',
        'company_tax_number',
        'company_logo',
        'company_favicon',
        'pdf_notes_ar',
        'pdf_notes_en',
        'score_excellent',
        'score_good',
        'score_needs_attention',
    ];

    public function index()
    {
        $settings = [];
        foreach ($this->settingKeys as $key) {
            $settings[$key] = Setting::get($key, $this->getDefault($key));
        }

        $lang = app()->getLocale();
        return view('settings.index', compact('settings', 'lang'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'company_name_ar' => 'nullable|string|max:255',
            'company_name_en' => 'nullable|string|max:255',
            'company_address_ar' => 'nullable|string|max:500',
            'company_address_en' => 'nullable|string|max:500',
            'company_phone' => 'nullable|string|max:50',
            'company_email' => 'nullable|email|max:255',
            'company_website' => 'nullable|url|max:255',
            'company_tax_number' => 'nullable|string|max:50',
            'pdf_notes_ar' => 'nullable|string|max:1000',
            'pdf_notes_en' => 'nullable|string|max:1000',
            'score_excellent' => 'required|numeric|min:1|max:100',
            'score_good' => 'required|numeric|min:1|max:100',
            'score_needs_attention' => 'required|numeric|min:1|max:100',
            'company_logo' => 'nullable|image|mimes:png,jpg,jpeg,svg,webp|max:2048',
            'company_favicon' => 'nullable|file|mimes:png,ico,svg|max:512',
        ]);

        try {
            // Handle logo upload
            if ($request->hasFile('company_logo')) {
                $oldLogo = Setting::get('company_logo');
                if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                    Storage::disk('public')->delete($oldLogo);
                }
                $path = $request->file('company_logo')->store('logos', 'public');
                Setting::set('company_logo', $path);
            }

            // Handle logo removal
            if ($request->boolean('remove_logo')) {
                $oldLogo = Setting::get('company_logo');
                if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                    Storage::disk('public')->delete($oldLogo);
                }
                Setting::set('company_logo', null);
            }

            // Handle favicon upload
            if ($request->hasFile('company_favicon')) {
                $oldFav = Setting::get('company_favicon');
                if ($oldFav && Storage::disk('public')->exists($oldFav)) {
                    Storage::disk('public')->delete($oldFav);
                }
                $path = $request->file('company_favicon')->store('favicons', 'public');
                Setting::set('company_favicon', $path);
            }

            // Handle favicon removal
            if ($request->boolean('remove_favicon')) {
                $oldFav = Setting::get('company_favicon');
                if ($oldFav && Storage::disk('public')->exists($oldFav)) {
                    Storage::disk('public')->delete($oldFav);
                }
                Setting::set('company_favicon', null);
            }

            // Save text settings
            $textKeys = [
                'company_name_ar', 'company_name_en',
                'company_address_ar', 'company_address_en',
                'company_phone', 'company_email', 'company_website', 'company_tax_number',
                'pdf_notes_ar', 'pdf_notes_en',
                'score_excellent', 'score_good', 'score_needs_attention',
            ];

            foreach ($textKeys as $key) {
                Setting::set($key, $request->input($key, ''));
            }

            Setting::clearCache();
            Cache::forget('dashboard_stats');
            Cache::forget('dashboard_recent');
            Cache::forget('dashboard_today_count');
            Cache::forget('dashboard_today_completed');
            Cache::forget('dashboard_inspectors');
            // مسح cache العتبات عند تغيير الإعدادات
            Cache::forget('vis.scoring.thresholds');

            $lang = app()->getLocale();
            return redirect()->route('settings.index')
                ->with('success', $lang === 'ar' ? 'تم حفظ الإعدادات بنجاح' : 'Settings saved successfully');
        } catch (\Throwable $e) {
            report($e);
            return back()->withInput()->with('error',
                app()->getLocale() === 'ar' ? 'حدث خطأ أثناء حفظ الإعدادات.' : 'Error saving settings.');
        }
    }

    private function getDefault(string $key)
    {
        $defaults = [
            'company_name_ar' => config('vis.company.name_ar', 'مركز فحص المركبات'),
            'company_name_en' => config('vis.company.name_en', 'Vehicle Inspection Center'),
            'company_address_ar' => config('vis.company.address_ar', ''),
            'company_address_en' => config('vis.company.address_en', ''),
            'company_phone' => config('vis.company.phone', ''),
            'company_email' => config('vis.company.email', ''),
            'company_website' => config('vis.company.website', ''),
            'company_tax_number' => config('vis.company.tax_number', ''),
            'company_logo' => config('vis.company.logo'),
            'company_favicon' => config('vis.company.favicon'),
            'pdf_notes_ar' => config('vis.company.notes_ar', ''),
            'pdf_notes_en' => config('vis.company.notes_en', ''),
            'score_excellent' => config('vis.scoring.excellent', 90),
            'score_good' => config('vis.scoring.good', 75),
            'score_needs_attention' => config('vis.scoring.needs_attention', 50),
        ];

        return $defaults[$key] ?? '';
    }
}