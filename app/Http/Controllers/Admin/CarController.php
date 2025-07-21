<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Car;
use App\Models\CarModel;
use App\Models\Image;
use App\Http\Resources\ModelResource;
use App\Models\ModelName;
use App\Models\Type;

class CarController extends Controller
{
    //_______________________________________________________________________________________________
    public function index(string $brandId, string $typeId, string $modelNameId, string $modelId)
    {
        $cars = Car::where('carmodel_id', $modelId)
        ->whereHas('carModel.modelName', function ($query) use ($modelNameId) {
            $query->where('id', $modelNameId);
        })
        ->get();
        if ($cars->isEmpty()) {
            return response()->json(['message' => __('messages.no_cars')], 404);
        }
        return response()->json([
            'message' => __('messages.cars_retrieved_successfully'),
            'data' => $cars
        ], 200);
    }

    //_____________________________________________________________________________________________
    public function store(Request $request,string $brandId, string $typeId,string $modelNameId , string $modelId)
    {
        $model = CarModel::with('modelName.type.brand')
            ->where('id', $modelId)
            ->whereHas('modelName', function ($query) use ($modelNameId) {
                $query->where('id', $modelNameId);
            })
            ->first();
        if (!$model) {
                return response()->json(['message' => __('messages.model_not_found')], 404);
        }

        $request->validate([
            'plate_number' => 'required|string|unique:cars',
            'status' => 'nullable|string',
            'color' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
            'images' => 'nullable|array',
            'images.*' => 'image|max:2048', // صور متعددة
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');        
            $imagePath = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('cars'), $imagePath);        
        }


        $car = Car::create([
            'carmodel_id' => $modelId,
            'plate_number' => $request->plate_number,
            'status' => $request->status,
            'color' => $request->color,
            'image' => $imagePath ? 'cars/' . $imagePath : null,
        ]);


        $carsCount = Car::where('carmodel_id', $modelId)->count();
        $model->count = $carsCount;
        $model->save();


        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('cars', 'public');

                Image::create([
                    'car_id' => $car->id,
                    'path' => $path,
                ]);
            }
        }

        return response()->json([
            'message' => __('messages.car_created'),
            'data' => $car->load('images'),
        ], 201);
    }

        // _________________________________________________________________________________________

    public function show(string $brandId, string $typeId, string $modelNameId, string $modelId, string $id)
    {
        $car = Car::with(['images', 'carModel.modelName.type.brand'])->find($id);
        if (!$car) {
            return response()->json(['message' => __('messages.car_not_found')], 404);
        }

        return response()->json([
            'message' => __('messages.car_retrieved_successfully'),
            'data' => [
                'id' => $car->id,
                'plate_number' => $car->plate_number,
                'status' => $car->status,
                'color' => $car->color,
                'main_image' => $car->image ? asset('storage/' . $car->image) : null,
                'images' => $car->images->map(fn($img) => asset('storage/' . $img->path)),
                'car_model' => $car->carModel ? new ModelResource($car->carModel) : null,
            ]
        ]);
    }
    //________________________________________________________________________________________________________
    public function related()
    {
        $cars = CarModel::inRandomOrder()->take(7)->get();
        return response()->json($cars);
    }
    // __________________________________________________________________________________________
    public function update(Request $request, string $brandId, string $typeId, string $modelNameId, string $modelId, string $id)
    {
        $model = CarModel::find($modelId);
        $car = Car::with(['images', 'carModel.modelName.type.brand'])->find($id);
        if (!$car) {
            return response()->json(['message' => __('messages.car_not_found')], 404);
        }    
   
        if ($car->carmodel_id != $modelId) {
            return response()->json(['message' => __('messages.car_not_belong_to_model')], 404);
        }

        $request->validate([
            'plate_number' => 'sometimes|string|unique:cars,plate_number,' . $car->id,
            'status' => 'sometimes|string',
            'color' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);

        
        if ($request->hasFile('image')) {
            // حذف الصورة القديمة لو موجودة
            if ($car->image && file_exists(public_path($car->image))) {
                unlink(public_path($car->image));
            }

            $request->validate([
                'plate_number' => 'required|string|unique:cars',
                'status' => 'nullable|string',
                'color' => 'nullable|string',
                'image' => 'nullable|image|max:2048',
                'images' => 'nullable|array',
                'images.*' => 'image|max:2048',
            ]);

            $imagePath = null;
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $imagePath = time() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('cars'), $imagePath);
            }

            $car = Car::create([
                'carmodel_id' => $modelId,
                'plate_number' => $request->plate_number,
                'status' => $request->status,
                'color' => $request->color,
                'image' => $imagePath ? 'cars/' . $imagePath : null,
            ]);

            $model->count = Car::where('carmodel_id', $modelId)->count();
            $model->save();

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    $path = $file->store('cars', 'public');
                    Image::create([
                        'car_id' => $car->id,
                        'path' => $path,
                    ]);
                }
            }

            return response()->json([
                'message' => __('messages.car_updated'),
                'data' => $car->load('images'),
            ], 201);
        }
    }
}


