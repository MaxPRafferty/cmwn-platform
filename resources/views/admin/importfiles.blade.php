@extends('master')
@section('content')
	<script>
	window.onload = function(){
		$('[name=districts]').change(function(){
			var id = $(this).val();
			if (id=='' || id==0){
				return false;
			}
		    document.location = "/admin/importfiles?district="+id;
		});
	};
</script>

	<div class="panel panel-info">
		<div class="panel-body" style="padding-top:30px">
			{!! Form::open(array('url' => 'admin/importfiles', 'files'=>true, 'class' => 'form-horizontal', 'role' => 'form', 'id' =>
				'uploadcsvform'))	!!}
			<table id="example" class="table table-striped table-bordered" cellspacing="0" width="100%">
				<tr class="tr_head">
					<td colspan="2"><h4>Import Files</h4></td>
				</tr>
				{{--*/  $org = array() /*--}}
				@foreach($organizations as $organization)
					@for($i=0; $i<$organization->organizations->count(); $i++)
					{{--*/  $org[$organization->organizations[$i]->uuid] = $organization->organizations[$i]->title /*--}}
					@endfor
				@endforeach



				{{--*/  $dist = array() /*--}}
				{{--*/  $dist[0] = 'Select District' /*--}}
				@foreach($districts as $district)
					{{--*/  $dist[$district->uuid] = $district->title /*--}}
				@endforeach
				<tr>
					<td>Select the district:  </td>
					<td>
						<div class="input-group" style="margin-bottom: 25px">
							{!! Form::select('districts', $dist, $selected_district[0]['id'])!!}
						</div>
					</td>
				</tr>
<?php
					var_dump($org);
				?>

				@if($org)
				<tr>
					<td>Select the orgnization:  </td>
					<td>
						<div class="input-group" style="margin-bottom: 25px">
							{{--*/  $org = array('one-a1c8-11e5-99e2-08002777c33d') /*--}}
							{!! Form::select('organizations', array(
							'one-a1c8-11e5-99e2-08002777c33d' => 'one-a1c8-11e5-99e2-08002777c33d'
							))!!}
						</div>
					</td>
				</tr>


				<tr>
					<td>Select your file: </td>
					<td>
						<div class="input-group" style="margin-bottom: 25px">
							{!! Form::file('yourcsv') !!}
						</div>
					</td>
				</tr>

				<tr>
					<td colspan="2">
						<div class="form-group" style="margin-top:10px">
							<div class="col-sm-12 controls" style="text-align: center">
								{!! Form::reset('Reset', array('class' => 'btn btn-default', 'id' => 'btn-reset')) !!}
								{!! Form::submit('Upload', array('class' => 'btn btn-success', 'id' => 'btn-upload')) !!}
							</div>
						</div>
					</td>
				</tr>
				@endif
			</table>
			{!! Form::close() !!}
		</div>
	</div>
@stop
