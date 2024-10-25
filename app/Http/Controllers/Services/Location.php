<?php

namespace App\Http\Controllers\Services;

use App\Enums\ModelsEnum;
use App\Http\Requests\UpdateLocationRequest;

class Location
{
    public function updateLocation(ModelsEnum $model, UpdateLocationRequest $request)
    {
        $entity = $model->value::where('mobile', $request->input('mobile'))->first();

        if(!$entity)
        {
            return response()->json(['message' => 'The mobile number is not registered']);
        }

        $entity->update(['latitude' => $request->input('latitude'), 'longitude' => $request->input('longitude')]);

        return response()->json(['message' => 'Your location is updated successfully']);
    }
}
