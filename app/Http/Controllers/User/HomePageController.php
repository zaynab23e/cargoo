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
        $query = CarModel::with(['modelName.type.brand', 'cars.images'])
            ->whereHas('cars') // نتأكد إن الموديل له سيارات
            ->select(
                'id',
                'year',
                'price',
                'engine_type',
                'transmission_type',
                'seat_type',
                'seats_count',
                'acceleration',
                'image',
                'model_name_id'
            )
            ->latest();

        if ($request->filled('brand')) {
            $query->whereHas('modelName.type.brand', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->brand . '%');
            });
        }

        if ($request->filled('type')) {
            $query->whereHas('modelName.type', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->type . '%');
            });
        }

        if ($request->filled('model')) {
            $query->whereHas('modelName', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->model . '%');
            });
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

        // حافظ على query params في روابط الباجينيشن
        $models = $query->paginate(10)->appends($request->query());

        return ModelResource::collection($models);
    }

    public function show($id)
    {
        $model = CarModel::with(['modelName.type.brand', 'cars.images'])
            ->select(
                'id',
                'year',
                'price',
                'engine_type',
                'transmission_type',
                'seat_type',
                'seats_count',
                'acceleration',
                'image',
                'model_name_id'
            )
            ->find($id);

        if (! $model) {
            return response()->json(['message' => __('messages.model_not_found')], 404);
        }

        if (! $model->modelName || ! $model->modelName->type || ! $model->modelName->type->brand) {
            return response()->json(['message' => __('messages.model_not_belonging')], 403);
        }

        return new ModelResource($model);
    }

    public function filterInfo()
    {
        $brands = Brand::get(['id', 'name', 'logo'])
            ->map(function ($brand) {
                return [
                    'id' => (string) $brand->id,
                    'attributes' => [
                        'name' => $brand->name,
                        'logo' => $brand->logo ? asset($brand->logo) : null,
                    ],
                ];
            });

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
