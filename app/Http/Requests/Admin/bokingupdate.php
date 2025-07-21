<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class bokingupdate extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
                    'car_id' => 'required|exists:cars,id',
        'user_id' => 'required|exists:users,id',
        'driver_id' => 'nullable|exists:drivers,id',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
        'final_price' => 'required|numeric',
        'status' => 'required|in:pending,confirmed,assigned,canceled,completed',
        ];
    }
}
