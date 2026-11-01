<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Area;
use App\Models\City;
use App\Models\ProductType;
use App\Models\ServiceType;
use App\Models\AuditLog;

class MasterDataController extends Controller
{
    // --- AREAS & CITIES ---
    public function areasIndex()
    {
        $areas = Area::withCount('cities', 'organizations')->latest()->get();
        $cities = City::with('area')->latest()->get();
        return view('masters.areas', compact('areas', 'cities'));
    }

    public function areaStore(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255|unique:areas,name']);
        $area = Area::create(['name' => $request->name, 'is_active' => true]);
        AuditLog::log('CREATE_AREA', 'Master Data', (string)$area->id, "Created area: {$area->name}");

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['status' => 'success', 'data' => $area, 'message' => "Area '{$area->name}' added successfully."]);
        }

        return back()->with('success', "Area '{$area->name}' added successfully.");
    }

    public function areaToggle(Area $area)
    {
        $area->update(['is_active' => !$area->is_active]);
        return back()->with('success', "Area status updated.");
    }

    public function cityStore(Request $request)
    {
        $request->validate([
            'area_id' => 'required|exists:areas,id',
            'name' => 'required|string|max:255',
        ]);
        $city = City::create([
            'area_id' => $request->area_id,
            'name' => $request->name,
            'is_active' => true,
        ]);
        AuditLog::log('CREATE_CITY', 'Master Data', (string)$city->id, "Created city: {$city->name}");

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['status' => 'success', 'data' => $city, 'message' => "City '{$city->name}' added successfully."]);
        }

        return back()->with('success', "City '{$city->name}' added successfully.");
    }

    public function cityToggle(City $city)
    {
        $city->update(['is_active' => !$city->is_active]);
        return back()->with('success', "City status updated.");
    }

    public function citiesByArea(Area $area)
    {
        return response()->json($area->cities()->where('is_active', true)->get());
    }

    // --- PRODUCT TYPES (MACHINERY) ---
    public function productTypesIndex()
    {
        $productTypes = ProductType::withCount('complaints')->latest()->get();
        return view('masters.products', compact('productTypes'));
    }

    public function productTypeStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        $product = ProductType::create([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => true,
        ]);
        AuditLog::log('CREATE_PRODUCT_TYPE', 'Master Data', (string)$product->id, "Created machine product type: {$product->name}");
        return back()->with('success', "Product type '{$product->name}' added.");
    }

    public function productTypeToggle(ProductType $productType)
    {
        $productType->update(['is_active' => !$productType->is_active]);
        return back()->with('success', "Product type status updated.");
    }

    // --- SERVICE TYPES ---
    public function serviceTypesIndex()
    {
        $serviceTypes = ServiceType::withCount('complaints')->latest()->get();
        return view('masters.services', compact('serviceTypes'));
    }

    public function serviceTypeStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        $service = ServiceType::create([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => true,
        ]);
        AuditLog::log('CREATE_SERVICE_TYPE', 'Master Data', (string)$service->id, "Created service type: {$service->name}");
        return back()->with('success', "Service type '{$service->name}' added.");
    }

    public function serviceTypeToggle(ServiceType $serviceType)
    {
        $serviceType->update(['is_active' => !$serviceType->is_active]);
        return back()->with('success', "Service type status updated.");
    }
}
