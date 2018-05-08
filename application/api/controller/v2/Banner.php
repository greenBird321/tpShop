<?php

namespace app\api\controller\v2;

use think\Controller;

class Banner extends Controller
{
    public function getBanner($id)
    {
        echo 'This is V2 Verison api';
    }
}
