<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBrandsRequest;
use App\Http\Resources\BrandsResource;
use App\Models\Brand;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;

class AdminBrandsController extends Controller
{
    use HttpResponses;

    public function updateImage(string $brandId, Request $request)
    {
        $brand = Brand::find($brandId);
        $request->validate([
            'logo' => 'required|image|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('brands'), $filename);
            $brand->logo = 'brands/' . $filename;
        }

        if (!$brand->save()) {
            return response()->json([
                'status' => __('messages.error_occurred'),
                'message' => __('messages.brand_update_failed'),
                'data' => null
            ], 500);
        }

        return response()->json([
            'message' => __('messages.image_updated_successfully'),
            'data' => $brand
        ]);
    }

    public function index()
    {
        $brands = Brand::with('types')->get();
        return BrandsResource::collection($brands);
    }

    public function store(Request $request)
    {
        $filename = null;

        $request->validate([
            'name' => 'required|string|max:255',
            "logo" => 'required|image|max:2048'
        ]);

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('brands'), $filename);

            $brand = Brand::create([
                'name' => $request->name,
                'logo' => $filename ? 'brands/' . $filename : null,
            ]);
        }

        if (!$brand) {
            return response()->json([
                'status' => __('messages.error_occurred'),
                'message' => __('messages.brand_create_failed'),
                'data' => ''
            ], 500);
        }

        $brand = new BrandsResource($brand);
        return $this->success($brand, __('messages.brand_created_successfully'), 201);
    }

    public function update(Request $request, $id)
    {
        $brand = Brand::find($id);
        if (!$brand) {
            return response()->json([
                'status' => __('messages.error_occurred'),
                'message' => __('messages.brand_not_found'),
                'data' => ''
            ], 500);
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:brands,name,' . $brand->id,
            "logo" => 'required|image|max:2048'
        ]);

        if ($request->hasFile('logo')) {
            if ($brand->logo && file_exists(public_path($brand->logo))) {
                unlink(public_path($brand->logo));
            }

            $file = $request->file('logo');
            $filename = time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('brands'), $filename);
            $brand->logo = 'brands/' . $filename;
        }

        $brand->name = $request->name;

        if (!$brand->save()) {
            return response()->json([
                'status' => __('messages.error_occurred'),
                'message' => __('messages.brand_update_failed'),
                'data' => ''
            ], 500);
        }

        return response()->json([
            'message' => __('messages.brand_updated_successfully'),
            'data' => $brand
        ]);
    }

    public function destroy($id)
    {
        $brand = Brand::find($id);
        if (!$brand) {
            return response()->json([
                'status' => __('messages.error_occurred'),
                'message' => __('messages.brand_not_found'),
                'data' => ''
            ], 500);
        }

        $brand->delete();

        return $this->success('', __('messages.brand_deleted_successfully'));
    }

    public function show($id)
    {
        $brand = Brand::find($id);
        if (!$brand) {
            return response()->json([
                'status' => __('messages.error_occurred'),
                'message' => __('messages.brand_not_found'),
                'data' => ''
            ], 500);
        }

        return new BrandsResource($brand);
    }
}
