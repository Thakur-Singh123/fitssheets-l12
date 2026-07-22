@extends('layouts.master')
@section('content') 
<div class="content-wrapper">
@if(\Session::has('success'))
		<div class="alert alert-success">
			<h4>{{\Session::get('success')}}</h4>
		</div>
	@endif
            <div class="row">
              <div class="col-md-12 grid-margin stretch-card">
                <div class="card">
                  <div class="card-body">
                    <h4 class="card-title">Add Billed Rate</h4>
                    <form method="POST" action="{{ url('/user/update_billrate') }}" class="forms-sample">
					
					@csrf
					<input id="meta_key"  value="{{ $AdminMeta->meta_key }}" type="hidden" class="form-control" name="meta_key" >
					    <div class="form-group">
                            <label for="password" >Billed Rate($)</label>
                            <input id="meta_value"  value="{{ $AdminMeta->meta_value }}" type="text" class="form-control" name="meta_value" >
                        </div>
						<button type="submit" class="btn btn-success mr-2">Submit</button>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>

@endsection




