<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Validator;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @desc model验证，如果如果验证未通过返回一个ResourceError对象
     * @param $attributes
     * @param $rules
     * @return mixed
     */
    public function validate($attributes,$rules){
        $validator = Validator::make($attributes,$rules);
        if($validator->passes()){
            return true;
        }else{
            /**
             * 获取第一条错误信息
             */
            $message = $validator->messages();
            echo json_encode(["status"=>false,"data"=>$message->first()]);
            exit;
        }
    }
}
