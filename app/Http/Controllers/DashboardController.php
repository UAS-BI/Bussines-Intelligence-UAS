<?php

namespace App\Http\Controllers;

use App\Models\FactHousing;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProperties = FactHousing::count();

        $averagePrice = FactHousing::avg('Price_EUR');

        $averageSize = FactHousing::avg('Size_sqm');

        $averageRooms = FactHousing::avg('Rooms');

        $avgPriceByArrondissement = DB::table('fact_housing')
            ->join('dim_location', 'fact_housing.location_id', '=', 'dim_location.location_id')
            ->select(
                'dim_location.arrondissement',
                DB::raw('AVG(fact_housing.Price_EUR) as avg_price')
            )
            ->groupBy('dim_location.arrondissement')
            ->orderByDesc('avg_price')
            ->limit(10)
            ->get();

        $propertyTypeDistribution = DB::table('fact_housing')
            ->join(
                'dim_property_type',
                'fact_housing.property_type_id',
                '=',
                'dim_property_type.property_type_id'
            )
            ->select(
                'dim_property_type.property_type',
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('dim_property_type.property_type')
            ->get();
        
        $conditionDistribution = DB::table('fact_housing')
            ->join(
                'dim_condition',
                'fact_housing.condition_id',
                '=',
                'dim_condition.condition_id'
            )
            ->select(
                'dim_condition.property_condition',
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('dim_condition.property_condition')
            ->orderByDesc('total')
            ->get();

            $priceCategoryDistribution = DB::table('fact_housing')
                ->select(
                    'Price_Category',
                    DB::raw('COUNT(*) as total'),
                    DB::raw('AVG(Price_EUR) as avg_price')
                )
                ->groupBy('Price_Category')
                ->orderByDesc('avg_price')
                ->get();

        return view('dashboard', compact(
            'totalProperties',
            'averagePrice',
            'averageSize',
            'averageRooms',
            'avgPriceByArrondissement',
            'propertyTypeDistribution',
            'conditionDistribution',
            'priceCategoryDistribution',
        ));
    }
    public function locationInsights()
    {
        $locationStats = DB::table('fact_housing')
            ->join('dim_location', 'fact_housing.location_id', '=', 'dim_location.location_id')
            ->select(
                'dim_location.arrondissement',
                DB::raw('COUNT(*) as total_properties'),
                DB::raw('AVG(fact_housing.Price_EUR) as avg_price'),
                DB::raw('AVG(fact_housing.Size_sqm) as avg_size'),
                DB::raw('AVG(fact_housing.Rooms) as avg_rooms'),
                DB::raw('MAX(fact_housing.Price_EUR) as highest_price')
            )
            ->groupBy('dim_location.arrondissement')
            ->orderByDesc('avg_price')
            ->limit(8)
            ->get();

        $mostExpensiveArrondissement = DB::table('fact_housing')
            ->join('dim_location', 'fact_housing.location_id', '=', 'dim_location.location_id')
            ->select(
                'dim_location.arrondissement',
                DB::raw('AVG(fact_housing.Price_EUR) as avg_price')
            )
            ->groupBy('dim_location.arrondissement')
            ->orderByDesc('avg_price')
            ->first();

        $mostAvailableArrondissement = DB::table('fact_housing')
            ->join('dim_location', 'fact_housing.location_id', '=', 'dim_location.location_id')
            ->select(
                'dim_location.arrondissement',
                DB::raw('COUNT(*) as total_properties')
            )
            ->groupBy('dim_location.arrondissement')
            ->orderByDesc('total_properties')
            ->first();

        $marketAveragePrice = DB::table('fact_housing')
            ->avg('Price_EUR');

        $topSupplyArrondissements = DB::table('fact_housing')
            ->join('dim_location', 'fact_housing.location_id', '=', 'dim_location.location_id')
            ->select(
                'dim_location.arrondissement',
                DB::raw('COUNT(*) as total_properties')
            )
            ->groupBy('dim_location.arrondissement')
            ->orderByDesc('total_properties')
            ->limit(8)
            ->get();

        return view('location-insights', compact(
            'locationStats',
            'mostExpensiveArrondissement',
            'mostAvailableArrondissement',
            'marketAveragePrice',
            'topSupplyArrondissements'
        ));
    }
    public function propertyTypes()
    {
        $propertyTypeStats = DB::table('fact_housing')
            ->join(
                'dim_property_type',
                'fact_housing.property_type_id',
                '=',
                'dim_property_type.property_type_id'
            )
            ->select(
                'dim_property_type.property_type',
                DB::raw('COUNT(*) as total_properties'),
                DB::raw('AVG(fact_housing.Price_EUR) as avg_price'),
                DB::raw('AVG(fact_housing.Size_sqm) as avg_size'),
                DB::raw('AVG(fact_housing.Rooms) as avg_rooms'),
                DB::raw('MAX(fact_housing.Price_EUR) as highest_price')
            )
            ->groupBy('dim_property_type.property_type')
            ->orderByDesc('total_properties')
            ->get();

        $dominantPropertyType = DB::table('fact_housing')
            ->join(
                'dim_property_type',
                'fact_housing.property_type_id',
                '=',
                'dim_property_type.property_type_id'
            )
            ->select(
                'dim_property_type.property_type',
                DB::raw('COUNT(*) as total_properties')
            )
            ->groupBy('dim_property_type.property_type')
            ->orderByDesc('total_properties')
            ->first();

        $premiumPropertyType = DB::table('fact_housing')
            ->join(
                'dim_property_type',
                'fact_housing.property_type_id',
                '=',
                'dim_property_type.property_type_id'
            )
            ->select(
                'dim_property_type.property_type',
                DB::raw('AVG(fact_housing.Price_EUR) as avg_price')
            )
            ->groupBy('dim_property_type.property_type')
            ->orderByDesc('avg_price')
            ->first();

        $largestPropertyType = DB::table('fact_housing')
            ->join(
                'dim_property_type',
                'fact_housing.property_type_id',
                '=',
                'dim_property_type.property_type_id'
            )
            ->select(
                'dim_property_type.property_type',
                DB::raw('AVG(fact_housing.Size_sqm) as avg_size')
            )
            ->groupBy('dim_property_type.property_type')
            ->orderByDesc('avg_size')
            ->first();

        $totalPropertyTypes = DB::table('dim_property_type')->count();

        return view('property-types', compact(
            'propertyTypeStats',
            'dominantPropertyType',
            'premiumPropertyType',
            'largestPropertyType',
            'totalPropertyTypes'
        ));
    }
    public function priceAnalysis()
    {
        $averagePrice = FactHousing::avg('Price_EUR');

        $highestPrice = FactHousing::max('Price_EUR');

        $luxuryProperties = DB::table('fact_housing')
            ->where('Price_Category', 'Luxury')
            ->count();

        $avgPricePerSqm = DB::table('fact_housing')
            ->selectRaw('AVG(Price_EUR / Size_sqm) as avg_price_per_sqm')
            ->value('avg_price_per_sqm');

        $priceCategoryStats = DB::table('fact_housing')
            ->select(
                'Price_Category',
                DB::raw('COUNT(*) as total_properties'),
                DB::raw('AVG(Price_EUR) as avg_price'),
                DB::raw('MIN(Price_EUR) as min_price'),
                DB::raw('MAX(Price_EUR) as max_price')
            )
            ->groupBy('Price_Category')
            ->orderByDesc('avg_price')
            ->get();

        $avgPriceByPropertyType = DB::table('fact_housing')
            ->join(
                'dim_property_type',
                'fact_housing.property_type_id',
                '=',
                'dim_property_type.property_type_id'
            )
            ->select(
                'dim_property_type.property_type',
                DB::raw('AVG(fact_housing.Price_EUR) as avg_price')
            )
            ->groupBy('dim_property_type.property_type')
            ->orderByDesc('avg_price')
            ->get();

        $topPriceDistricts = DB::table('fact_housing')
            ->join('dim_location', 'fact_housing.location_id', '=', 'dim_location.location_id')
            ->select(
                'dim_location.arrondissement',
                DB::raw('AVG(fact_housing.Price_EUR) as avg_price')
            )
            ->groupBy('dim_location.arrondissement')
            ->orderByDesc('avg_price')
            ->limit(8)
            ->get();

        return view('price-analysis', compact(
            'averagePrice',
            'highestPrice',
            'luxuryProperties',
            'avgPricePerSqm',
            'priceCategoryStats',
            'avgPriceByPropertyType',
            'topPriceDistricts'
        ));
    }
    public function propertyCondition()
    {
        $conditionStats = DB::table('fact_housing')
            ->join(
                'dim_condition',
                'fact_housing.condition_id',
                '=',
                'dim_condition.condition_id'
            )
            ->select(
                'dim_condition.property_condition',
                DB::raw('COUNT(*) as total_properties'),
                DB::raw('AVG(fact_housing.Price_EUR) as avg_price'),
                DB::raw('AVG(fact_housing.Size_sqm) as avg_size'),
                DB::raw('AVG(fact_housing.Rooms) as avg_rooms'),
                DB::raw('MAX(fact_housing.Price_EUR) as highest_price')
            )
            ->groupBy('dim_condition.property_condition')
            ->orderByDesc('total_properties')
            ->get();

        $mostCommonCondition = DB::table('fact_housing')
            ->join(
                'dim_condition',
                'fact_housing.condition_id',
                '=',
                'dim_condition.condition_id'
            )
            ->select(
                'dim_condition.property_condition',
                DB::raw('COUNT(*) as total_properties')
            )
            ->groupBy('dim_condition.property_condition')
            ->orderByDesc('total_properties')
            ->first();

        $premiumCondition = DB::table('fact_housing')
            ->join(
                'dim_condition',
                'fact_housing.condition_id',
                '=',
                'dim_condition.condition_id'
            )
            ->select(
                'dim_condition.property_condition',
                DB::raw('AVG(fact_housing.Price_EUR) as avg_price')
            )
            ->groupBy('dim_condition.property_condition')
            ->orderByDesc('avg_price')
            ->first();

        $needsAttention = DB::table('fact_housing')
            ->join(
                'dim_condition',
                'fact_housing.condition_id',
                '=',
                'dim_condition.condition_id'
            )
            ->where('dim_condition.property_condition', 'Needs Renovation')
            ->count();

        $totalConditionTypes = DB::table('dim_condition')->count();

        return view('property-condition', compact(
            'conditionStats',
            'mostCommonCondition',
            'premiumCondition',
            'needsAttention',
            'totalConditionTypes'
        ));
    }
    public function propertyData()
    {
        $properties = DB::table('fact_housing')
            ->join(
                'dim_location',
                'fact_housing.location_id',
                '=',
                'dim_location.location_id'
            )
            ->join(
                'dim_property_type',
                'fact_housing.property_type_id',
                '=',
                'dim_property_type.property_type_id'
            )
            ->join(
                'dim_condition',
                'fact_housing.condition_id',
                '=',
                'dim_condition.condition_id'
            )
            ->select(
                'fact_housing.property_id',
                'fact_housing.Price_EUR',
                'fact_housing.Size_sqm',
                'fact_housing.Rooms',
                'fact_housing.Price_Category',
                'fact_housing.location_id',
                'fact_housing.property_type_id',
                'fact_housing.condition_id',
                'dim_location.arrondissement',
                'dim_property_type.property_type',
                'dim_condition.property_condition'
            )
            ->latest('fact_housing.property_id')
            ->paginate(10);

        $locations = DB::table('dim_location')->get();

        $propertyTypes = DB::table('dim_property_type')->get();

        $conditions = DB::table('dim_condition')->get();

        return view('property-data', compact(
            'properties',
            'locations',
            'propertyTypes',
            'conditions'
        ));
    }

    public function storeProperty(Request $request)
    {
        $request->validate([
            'Price_EUR' => 'required|numeric|min:1',
            'Size_sqm' => 'required|numeric|min:1',
            'Rooms' => 'required|integer|min:1|max:10',
            'Price_Category' => 'required|in:Medium,High,Luxury',
            'location_id' => 'required|exists:dim_location,location_id',
            'property_type_id' => 'required|exists:dim_property_type,property_type_id',
            'condition_id' => 'required|exists:dim_condition,condition_id',
        ]);

        $lastProperty = DB::table('fact_housing')
            ->orderByDesc('fact_id')
            ->first();

        $nextNumber = $lastProperty
            ? ((int) substr($lastProperty->property_id, 1)) + 1
            : 10000;

        $newPropertyId = 'P' . $nextNumber;

        DB::table('fact_housing')->insert([
            'property_id' => $newPropertyId,
            'Price_EUR' => $request->Price_EUR,
            'Size_sqm' => $request->Size_sqm,
            'Rooms' => $request->Rooms,
            'Price_Category' => $request->Price_Category,
            'location_id' => $request->location_id,
            'property_type_id' => $request->property_type_id,
            'condition_id' => $request->condition_id,
        ]);

        return redirect()
            ->route('property.data')
            ->with('success', 'Property added successfully.');
    }

    public function updateProperty(Request $request, $id)
    {
        $request->validate([
            'Price_EUR' => 'required|numeric|min:1',
            'Size_sqm' => 'required|numeric|min:1',
            'Rooms' => 'required|integer|min:1|max:10',
            'Price_Category' => 'required|in:Medium,High,Luxury',
            'location_id' => 'required|exists:dim_location,location_id',
            'property_type_id' => 'required|exists:dim_property_type,property_type_id',
            'condition_id' => 'required|exists:dim_condition,condition_id',
        ]);

        DB::table('fact_housing')
            ->where('property_id', $id)
            ->update([
                'Price_EUR' => $request->Price_EUR,
                'Size_sqm' => $request->Size_sqm,
                'Rooms' => $request->Rooms,
                'Price_Category' => $request->Price_Category,
                'location_id' => $request->location_id,
                'property_type_id' => $request->property_type_id,
                'condition_id' => $request->condition_id,
            ]);

        return redirect()
            ->route('property.data')
            ->with('success', 'Property updated successfully.');
    }

    public function deleteProperty($id)
    {
        DB::table('fact_housing')
            ->where('property_id', $id)
            ->delete();

        return redirect()
            ->route('property.data')
            ->with('success', 'Property deleted successfully.');
    }
}