<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\TypesResource;
use App\Models\Brand;
use App\Models\Type;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;

class AdminTypesController extends Controller
{
    use HttpResponses;

    public function index(Brand $brand)
    {
        $types = $brand->types()->get();
        return TypesResource::collection($types);
    }

    public function store(Request $request, Brand $brand)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        $type = Type::create([
            'name' => $request->name,
            'description' => $request->description,
            'brand_id' => $brand->id,
        ]);

        $type = new TypesResource($type);

        if (!$type) {
            return response()->json([
                'status' => trans('messages.error_occurred'),
                'message' => trans('messages.type_create_failed'),
                'data' => ''
            ], 500);
        }

        return $this->success($type, trans('messages.type_created_successfully'), 201);
    }

    public function update(Request $request, Brand $brand, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        $type = Type::findOrFail($id);

        $type->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        if (!$type) {
            return response()->json([
                'status' => trans('messages.error_occurred'),
                'message' => trans('messages.type_update_failed'),
                'data' => ''
            ], 500);
        }

        $type = new TypesResource($type);

        return $this->success($type, trans('messages.type_updated_successfully'));
    }

    public function destroy(Brand $brand, $typeId)
    {
        $type = Type::findOrFail($typeId);

        $brand->types()->detach($typeId);

        if (!$type) {
            return response()->json([
                'status' => trans('messages.error_occurred'),
                'message' => trans('messages.type_not_found'),
                'data' => ''
            ], 500);
        }

        return $this->success('', trans('messages.type_deleted_successfully'));
    }

    public function show(Brand $brand, $id)
    {
        $type = Type::findOrFail($id);

        if (!$type) {
            return response()->json([
                'status' => trans('messages.error_occurred'),
                'message' => trans('messages.type_not_found'),
                'data' => ''
            ], 500);
        }

        return new TypesResource($type);
    }
}
