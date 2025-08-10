<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\ModelResource;
use App\Models\Brand;
use App\Models\CarModel;
use App\Models\Type;
use Illuminate\Http\Request;

class HomePageController extends Controller
{
    public function index(Request $request)
    { 
        $query = CarModel::with([
                'modelName.type.brand',
                'cars.images'
            ])
            ->latest(); // يجيب الأحدث أولاً

        // فلترة اختيارية
        if ($request->filled('brand')) {
            $query->whereHas('modelName.type.brand', fn($q) => 
                $q->where('name', 'like', '%' . $request->brand . '%'));
        }

        if ($request->filled('type')) {
            $query->whereHas('modelName.type', fn($q) => 
                $q->where('name', 'like', '%' . $request->type . '%'));
        }

        if ($request->filled('model')) {
            $query->whereHas('modelName', fn($q) => 
                $q->where('name', 'like', '%' . $request->model . '%'));
        }

        if ($request->has('year')) {
            $query->where('year', $request->year);
        }

        if (is_numeric($request->min_price)) {
            $query->where('price', '>=', $request->min_price);
        }

        if (is_numeric($request->max_price)) {
            $query->where('price', '<=', $request->max_price);
        }

        // جلب البيانات بعد التعديلات
        $models = $query->paginate(10);
        $models->each->refresh();

        return ModelResource::collection($models);
    }

    public function show($id)
    {
        $model = CarModel::with([
                'modelName.type.brand',
                'cars.images'
            ])
            ->find($id);

        if (!$model) {
            return response()->json(['message' => __('messages.model_not_found')], 404);
        }

        $model->refresh(); // تحديث البيانات

        return new ModelResource($model);
    }

    public function filterInfo()
    {
        $brands = Brand::get(['id', 'name', 'logo'])
            ->map(fn($brand) => [
                'id' => (string) $brand->id,
                'attributes' => [
                    'name' => $brand->name,
                    'logo' => $brand->logo ? asset($brand->logo) : null,
                ],
            ]);

        $types = Type::pluck('name')
            ->map(fn($type) => strtolower($type))
            ->unique()
            ->values()
            ->map(fn($type) => ['name' => $type]);

        $maxPrice = CarModel::max('price');
        $minPrice = CarModel::min('price');

        return response()->json([
            'brands' => $brands,
            'types' => $types,
            'max_price' => $maxPrice,
            'min_price' => $minPrice,
        ]);
    }
}
