<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Banner;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    public function index()
    {
        $banners = Banner::orderBy('sort_order')->paginate(20);
        return view('admin.banners.index', compact('banners'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:150',
            'subtitle' => 'nullable|string|max:255',
            'badge_text' => 'nullable|string|max:50',
            'cta_text' => 'nullable|string|max:50',
            'cta_link' => 'nullable|string|max:255',
            'desktop_image' => 'required|string|max:255',
            'mobile_image' => 'nullable|string|max:255',
            'position' => 'required|in:hero,promo_strip,featured_grid,footer_banner',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = (bool) $request->boolean('is_active');
        $banner = Banner::create($data);

        AuditLog::log('created', 'Banner', $banner->id);

        return back()->with('success', "Banner '{$banner->title}' created successfully.");
    }

    public function destroy(Banner $banner)
    {
        $title = $banner->title;
        $banner->delete();
        AuditLog::log('deleted', 'Banner', $banner->id);

        return back()->with('success', "Banner '{$title}' removed.");
    }
}
