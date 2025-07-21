<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ModelNameResource;
use App\Models\Brand;
use App\Models\ModelName;
use App\Models\Type;
use Illuminate\Http\Request;

class AdminModelNameController extends Controller
{
    public function index(string $brandId, string $typeId)
    {
        $brand = Brand::find($brandId);
        if (!$brand) {
            return response()->json(['message' => trans('messages.brand_not_found')], 404);
        }
        $type = $brand->types()->find($typeId);
        if (!$type) {
            return response()->json(['message' => trans('messages.type_not_found_in_brand')], 404);
        }

        $modelNames = $type->modelNames()->with('type.brand')->get(['id', 'name', 'type_id']);
        return ModelNameResource::collection($modelNames);
    }

    public function store(string $brandId, string $typeId, Request $request)
    {
        $request->validate([
            'name' => 'required|string',
        ]);

        $brand = Brand::find($brandId);
        if (!$brand) {
            return response()->json(['message' => trans('messages.brand_not_found')], 404);
        }
        $type = $brand->types()->find($typeId);
        if (!$type) {
            return response()->json(['message' => trans('messages.type_not_found_in_brand')], 404);
        }

        $modelName = ModelName::create([
            'name' => $request->name,
            'type_id' => $type->id,
        ]);

        return response()->json([
            'message' => trans('messages.model_created_successfully'),
            'data' => $modelName
        ]);
    }

    public function update(string $brandId, string $typeId, Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string',
        ]);

        $brand = Brand::find($brandId);
        if (!$brand) {
            return response()->json(['message' => trans('messages.brand_not_found')], 404);
        }
        $type = $brand->types()->find($typeId);
        if (!$type) {
            return response()->json(['message' => trans('messages.type_not_found_in_brand')], 404);
        }

        $modelName = $type->modelNames()->find($id);
        if (!$modelName) {
            return response()->json(['message' => trans('messages.model_not_found_in_brand')], 404);
        }

        $modelName->name = $request->name;
        if (!$modelName->save()) {
            return response()->json([
                'status' => trans('messages.error_occurred'),
                'message' => trans('messages.model_update_failed'),
                'data' => null
            ], 500);
        }

        return response()->json([
            'message' => trans('messages.model_updated_successfully'),
            'data' => $modelName
        ]);
    }

    public function show(string $brandId, string $typeId, $id)
    {
        $type = Type::where('id', $typeId)->first();

        if (!$type) {
            return response()->json(['message' => trans('messages.type_not_in_brand')], 404);
        }

        $modelName = ModelName::find($id);
        if (!$modelName) {
            return response()->json(['message' => trans('messages.model_not_found')], 404);
        }

        if (!$modelName->type->brand) {
            return response()->json(['message' => trans('messages.model_not_belong_to_brand_or_type')], 403);
        }

        return new ModelNameResource($modelName);
    }

    public function destroy(string $brandId, string $typeId, $id)
    {
        $brand = Brand::find($brandId);
        if (!$brand) {
            return response()->json(['message' => trans('messages.brand_not_found')], 404);
        }

        if (!$brand->types()->find($typeId)) {
            return response()->json(['message' => trans('messages.type_not_found_in_brand')], 404);
        }

        $modelName = ModelName::find($id);
        if (!$modelName) {
            return response()->json(['message' => trans('messages.model_not_found')], 404);
        }

        if (!$modelName->type->brand) {
            return response()->json(['message' => trans('messages.model_not_belong_to_brand')], 403);
        }

        $modelName->delete();

        return response()->json([
            'message' => trans('messages.model_deleted_successfully')
        ]);
    }
}
