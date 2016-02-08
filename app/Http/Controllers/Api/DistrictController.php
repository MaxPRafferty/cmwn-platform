<?php

namespace app\Http\Controllers\Api;

use app\Transformer\DistrictTransformer;
use app\District;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;

class DistrictController extends ApiController
{
    public function index()
    {
        $districts = District::limitToUser($this->currentUser)->get();

        return $this->respondWithCollection($districts, new DistrictTransformer());
    }

    public function show($uuid)
    {
        $district = District::findByUuid($uuid);

        if (!$district) {
            return $this->errorNotFound('District not found');
        }

        // make sure that the user is authorized to view this district.
        if (!$district->isUser($this->currentUser)) {
            return $this->errorUnauthorized();
        }

        return $this->respondWithItem($district, new DistrictTransformer());
    }

    public function create()
    {
        if ($this->currentUser->isSiteAdmin()) {
            $validator = Validator::make(Input::all(), District::$createRules);

            if (!$validator->passes()) {
                return $this->errorWrongArgs($validator->errors()->all());
            }

            $district = new District();

            try {
                $district->updateDistrict(Input::all());
            } catch (QueryException $e) {
                return $this->errorInternalError('Could not create the district. Possible integrity constraint violation.');
            }

            return $this->respondWithItem($district, new DistrictTransformer());
        } else {
            return $this->errorInternalError('You are not authorized to create users.');
        }
    }

    public function update($uuid)
    {
        $district = District::findByUuid($uuid);

        if (!$district) {
            return $this->errorNotFound('District not found');
        }

        // make sure that the user is authorized to update this district.
        if (!$district->canUpdate($this->currentUser)) {
            return $this->errorUnauthorized();
        }

        $validator = Validator::make(Input::all(), District::$updateRules);

        if ($validator->passes()) {
            $district->updateDistrict(Input::all());

            return $this->respondWithArray(array('message' => 'The district has been updated successfully.'));
        } else {
            $messages = print_r($validator->errors()->getMessages(), true);

            return $this->errorInternalError('Input validation error: '. $messages);
        }
    }

    public function updateImage($uuid)
    {
        $district = District::findByUuid($uuid);

        if (!$district->canUpdate($this->currentUser)) {
            return $this->errorInternalError('You are not authorized.');
        }

        $validator = Validator::make(Input::all(), Image::$imageUpdateRules);

        if ($validator->fails()) {
            return $this->errorWrongArgs($validator->errors()->all());
        }

        if ($district->updateImage(Input::all())) {
            return $this->respondWithArray(array('message' => 'The image has been updated sucessfully.'));
        }
    }
}
