<?php

namespace App\Http\Controllers\Services;

use App\Enums\ModelsEnum;
use App\Http\Requests\SetLocationRequest;

class Location
{
    public function setLocation(ModelsEnum $model, SetLocationRequest $request)
    {
        $entity = $model->value::where('mobile', $request->input('mobile'))->first();

        if(!$entity)
        {
            return response()->json(['message' => __('messages.mobile_not_registered')]);
        }

        $entity->update(['latitude' => $request->input('latitude'), 'longitude' => $request->input('longitude')]);

        return response()->json(['message' => __('messages.location_located')]);
    }
}
