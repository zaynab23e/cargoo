<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Car;
use Illuminate\Http\Request;
use App\Models\CarModel;
use App\Models\Type;
use App\Http\Resources\ModelResource;

class ModelController extends Controller
{
    public function index(string $brandId, string $typeId, string $modelNameId)
    {
        $brand = Brand::find($brandId);
        if (!$brand) {
            return response()->json(['message' => __('messages.brand_not_found')], 404);
        }
        $type = $brand->types()->find($typeId);
        if (!$type) {
            return response()->json(['message' => __('messages.type_not_found_in_brand')], 404);
        }
        $modelName = $type->modelNames()->find($modelNameId);
        if (!$modelName) {
            return response()->json(['message' => __('messages.model_name_not_found_in_type')], 404);
        }

        $models = $modelName->carModels()->with('modelName.type.brand')->get([
            'id', 'year', 'price', 'engine_type', 'transmission_type', 'seat_type', 'seats_count', 'acceleration', 'image', 'model_name_id'
        ]);

        return ModelResource::collection($models);
    }

    public function store(string $brandId, string $typeId, string $modelNameId, Request $request)
    {

        $filename = null;
                $filesnames = null;

        $request->validate([
            'year' => 'required|integer',
            'price' => 'required|numeric',
            'image' => 'required|image|max:2048',
            'images.*' => 'image|max:2048',
            'engine_type' => 'required|in:Gasoline,Electric,Hybrid,Plug-in Hybrid',
            'transmission_type' => 'required|in:Manual,Automatic,Hydramatic,CVT,DCT',
            'seat_type' => 'required|in:electric,sport,accessible,leather,fabric',
            'seats_count' => 'required|integer|min:1',
            'acceleration' => 'required|numeric|min:0',
        ]);

        $brand = Brand::find($brandId);
        if (!$brand) {
            return response()->json(['message' => __('messages.brand_not_found')], 404);
        }
        $type = $brand->types()->find($typeId);
        if (!$type) {
            return response()->json(['message' => __('messages.type_not_found_in_brand')], 404);
        }
        $modelName = $type->modelNames()->find($modelNameId);
        if (!$modelName) {
            return response()->json(['message' => __('messages.model_name_not_found_in_type')], 404);
        }

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('models'), $filename);
        }


        $model = CarModel::create([
            'year' => $request->year,
            'price' => $request->price,
            'image' => $filename ? 'models/' . $filename : null,
            'model_name_id' => $modelName->id,
            'engine_type' => $request->engine_type,
            'transmission_type' => $request->transmission_type,
            'seat_type' => $request->seat_type,
            'seats_count' => $request->seats_count,
            'acceleration' => $request->acceleration,
        ]);
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $filename = time() . '_' . $index . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('models'), $filename);

                $model->images()->create([
                    'image' =>$filename? 'models/' . $filename:null,
                ]);
            }
        }

        $model->load('images');        

        return response()->json([
            'message' => __('messages.model_created_successfully'),
            'data' => $model
        ]);
    }

    public function updateImage(string $model, Request $request)
    {
        $model = CarModel::find($model);
        $request->validate([
            'image' => 'required|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('models'), $filename);
            $model->image = 'models/' . $filename;
        }

        if (!$model->save()) {
            return response()->json([
                'status' => 'Error has occurred...',
                'message' => __('messages.model_update_failed'),
                'data' => null
            ], 500);
        }

        return response()->json([
            'message' => __('messages.image_updated_successfully'),
            'data' => $model,
            'images' => $model->images,
        ]);
    }

    public function update(string $brandId, string $typeId, string $modelNameId, Request $request, $id)
    {
        $request->validate([
            'year' => 'required|integer',
            'price' => 'required|numeric',
            'image' => 'required|image|max:2048',
            'engine_type' => 'required|in:Gasoline,Electric,Hybrid,Plug-in Hybrid',
            'transmission_type' => 'required|in:Manual,Automatic,Hydramatic,CVT,DCT',
            'seat_type' => 'required|in:electric,sport,accessible,leather,fabric',
            'seats_count' => 'required|integer|min:1',
            'acceleration' => 'required|numeric|min:0',
        ]);

        $brand = Brand::find($brandId);
        if (!$brand) {
            return response()->json(['message' => __('messages.brand_not_found')], 404);
        }
        $type = $brand->types()->find($typeId);
        if (!$type) {
            return response()->json(['message' => __('messages.type_not_found_in_brand')], 404);
        }
        $modelName = $type->modelNames()->find($modelNameId);
        if (!$modelName) {
            return response()->json(['message' => __('messages.model_name_not_found_in_type')], 404);
        }

        $model = $modelName->carModels()->find($id);
        if (!$model) {
            return response()->json(['message' => __('messages.model_not_found')], 404);
        }

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('models'), $filename);
            $model->image = 'models/' . $filename;
        }
        if ($request->hasFile('images')) {
            $model->images()->delete(); // Clear existing images
            foreach ($request->file('images') as $index => $image) {
                $filename = time() . '_' . $index . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('models'), $filename);

                $model->images()->create([
                    'image' =>$filename? 'models/' . $filename : null,
                ]);
            }
        }

        $model->year = $request->year;
        $model->price = $request->price;
        $model->engine_type = $request->engine_type;
        $model->transmission_type = $request->transmission_type;
        $model->seat_type = $request->seat_type;
        $model->seats_count = $request->seats_count;
        $model->acceleration = $request->acceleration;

        if (!$model->save()) {
            return response()->json([
                'status' => 'Error has occurred...',
                'message' => __('messages.model_update_failed'),
                'data' => null
            ], 500);
        }

        return response()->json([
            'message' => __('messages.model_updated_successfully'),
            'data' => $model
        ]);
    }

    public function show(string $brandId, string $typeId, string $modelNameId, $id)
    {
        $type = Type::with('modelNames')->where('id', $typeId)->first();

        if (!$type) {
            return response()->json(['message' => __('messages.type_not_found_in_brand')], 404);
        }
        $modelName = $type->modelNames()->find($modelNameId);
        if (!$modelName) {
            return response()->json(['message' => __('messages.model_name_not_found_in_type')], 404);
        }

        $model = CarModel::find($id);
        if (!$model) {
            return response()->json(['message' => __('messages.model_not_found')], 404);
        }

        if (!$model->modelName->type->brand) {
            return response()->json(['message' => __('messages.unauthorized_model_relation')], 403);
        }

        return new ModelResource($model);
    }

    public function destroy(string $brandId, string $typeId, string $modelNameId, $id)
    {
        $brand = Brand::find($brandId);
        if (!$brand) {
            return response()->json(['message' => __('messages.brand_not_found')], 404);
        }

        $type = $brand->types()->find($typeId);
        if (!$type) {
            return response()->json(['message' => __('messages.type_not_found_in_brand')], 404);
        }

        $modelName = $type->modelNames()->find($modelNameId);
        if (!$modelName) {
            return response()->json(['message' => __('messages.model_name_not_found_in_type')], 404);
        }

        $model = $modelName->carModels()->find($id);
        if (!$model) {
            return response()->json(['message' => __('messages.model_not_found')], 404);
        }

        $model->delete();

        return response()->json([
            'message' => __('messages.model_deleted_successfully')
        ]);
    }
}
