<?php

namespace app\Transformer;

use app\Role;
use League\Fractal\TransformerAbstract;

class RolesTransformer extends TransformerAbstract
{
    protected $availableEmbeds = [];

    /**
     * Turn this item object into a generic array.
     *
     * @return array
     */
    public function transform(Array $roles)
    {
        $data = array();

        for($k=0; $k<count($roles['districts']); $k++){
            $data['districts'][$k]['uuid'] = $roles['districts'][$k]['uuid'];
            $data['districts'][$k]['role_id'] = $roles['districts'][$k]['pivot']['role_id'];
        }

        for($k=0; $k<count($roles['organizations']); $k++){
            $data['organizations'][$k]['uuid'] = $roles['organizations'][$k]['uuid'];
            $data['organizations'][$k]['role_id'] = $roles['organizations'][$k]['pivot']['role_id'];
        }

        for($k=0; $k<count($roles['groups']); $k++){
            $data['groups'][$k]['uuid'] = $roles['groups'][$k]['uuid'];
            $data['groups'][$k]['role_id'] = $roles['groups'][$k]['pivot']['role_id'];
        }

        return $data;

    }
}
