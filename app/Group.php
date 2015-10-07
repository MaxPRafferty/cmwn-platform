<?php

namespace cmwn;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Request;

class Group extends Model
{
	use SoftDeletes;
	protected $dates = ['deleted_at'];
	protected $table = 'groups';

	public static $groupUpdateRules = array(
		'title[]'=>'string',
		//'role[]'=>'required',
		//'role[]'=>'required|regex:/^[0-9]?$/',
	);

	public function users()
	{
	    return $this->belongsToMany('cmwn\User');
	}


	public static function updateGroups(Request $request){
		$titles = $request::get('title');
		$ids = $request::get('id');

		$deleteId = $request::get('delete');
		$newtitle = $request::get('newtitle');

		$i=0;
		if ($ids) {
			foreach ($ids as $id) {
				$group = Group::find($id);
				$group->title = $titles[ $i ];

				if (isset($deleteId[ $i ]) && $deleteId[ $i ] == $id) {
					$group->delete();
				} else {

					$group->save();
				}
				$i++;
			}
		}

		if ($newtitle){
			$group = new Group();
			$group->title = $newtitle;
			$group->save();
		}

		return true;
	}
}
