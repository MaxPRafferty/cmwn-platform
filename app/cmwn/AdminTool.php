<?php

namespace app;

use Illuminate\Database\Eloquent\Model;

class AdminTool extends Model
{
    public static $uploadCsvRules = array(
        'xlsx'=>'required', // TODO: add file type validation
        'teacherAccessCode' => 'required|alpha_num',
        'studentAccessCode' => 'required|alpha_num',
    );

    public static $uploadImageRules = array(
        'yourfile' => 'required|image',
    );
}
