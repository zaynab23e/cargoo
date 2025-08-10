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

        $models = $query->paginate(10);

        foreach ($models as $model) {
            $model->refresh();
        }

        // تعديل شكل الريسبونس هنا فقط
        $data = $models->map(function ($model) {
            return [
                'id' => (string) $model->id,
                'year' => $model->year,
                'price' => $model->price,
                'engine_type' => $model->engine_type,
                'transmission_type' => $model->transmission_type,
                'seat_type' => $model->seat_type,
                'seats_count' => $model->seats_count,
                'acceleration' => $model->acceleration,
                'image' => $model->image ? asset($model->image) : null,

                'model_name' => [
                    'id' => (string) $model->modelName->id,
                    'name' => $model->modelName->name,
                    'type' => [
                        'id' => (string) $model->modelName->type->id,
                        'name' => $model->modelName->type->name,
                        'brand' => [
                            'id' => (string) $model->modelName->type->brand->id,
                            'name' => $model->modelName->type->brand->name,
                            'logo' => $model->modelName->type->brand->logo ? asset($model->modelName->type->brand->logo) : null,
                        ],
                    ],
                ],

                'cars' => $model->cars->map(function ($car) {
                    return [
                        'id' => (string) $car->id,
                        'license_plate' => $car->license_plate,
                        'images' => $car->images->map(fn($img) => asset($img->filename))->toArray(),
                    ];
                }),
            ];
        });

        // return response()->json([
        //     'data' => $data,
        //     'links' => [
        //         'first' => $models->url(1),
        //         'last' => $models->url($models->lastPage()),
        //         'prev' => $models->previousPageUrl(),
        //         'next' => $models->nextPageUrl(),
        //     ],
        //     'meta' => [
        //         'current_page' => $models->currentPage(),
        //         'from' => $models->firstItem(),
        //         'last_page' => $models->lastPage(),
        //         'links' => $models->linkCollection()->toArray(),
        //         'path' => $models->path(),
        //         'per_page' => $models->perPage(),
        //         'to' => $models->lastItem(),
        //         'total' => $models->total(),
        //     ],
        // ]);
               $models = $query->paginate(10);

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

        $model->refresh();

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
