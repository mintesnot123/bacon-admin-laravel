@extends('layouts.admin')


@section('content')

    <div class="row">

        <div class="col-lg-12 margin-tb">

            <div class="pull-left">

                <h2>Edit Permission</h2>

            </div>

            <div class="pull-right">

                <a class="btn btn-primary" href="{{ route('role-permissions.index') }}"> Back</a>

            </div>

        </div>

    </div>


    @if ($errors->any())

        <div class="alert alert-danger">

            <strong>Whoops!</strong> There were some problems with your input.<br><br>

            <ul>

                @foreach ($errors->all() as $error)

                    <li>{{ $error }}</li>

                @endforeach

            </ul>

        </div>

    @endif


    <form action="{{ route('role-permissions.update',$permission->id) }}" method="POST">

    	@csrf

        @method('PUT')


         <div class="row">

		    <div class="col-xs-12 col-sm-12 col-md-12">

		        <div class="form-group">

		            <strong>Name:</strong>

		            <input type="text" name="name" value="{{ $permission->name }}" class="form-control" placeholder="Name">

                     <input type="hidden" name="guard_name" value="{{ $permission->guard_name }}">

		        </div>

		    </div>

		   <!--  <div class="col-xs-12 col-sm-12 col-md-12">

		        <div class="form-group">

		            <strong>Guard Name:</strong>

		            <textarea class="form-control" style="height:150px" name="guard_name" placeholder="Detail">{{ $permission->guard_name }}</textarea>

		        </div>

		    </div> -->

		    <div class="col-xs-12 col-sm-12 col-md-12 text-center">

		      <button type="submit" class="btn btn-primary">Submit</button>

		    </div>

		</div>


    </form>




@endsection