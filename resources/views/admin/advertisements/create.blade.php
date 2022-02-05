@extends('layouts.admin')


@section('content')

    <div class="row">

        <div class="col-lg-12 margin-tb">

            <div class="pull-left">

                <h2>Add New Advertisement</h2>

            </div>

            <div class="pull-right">

                <a class="btn btn-primary" href="{{ route('advertisements.index') }}"> Back</a>

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


    {!! Form::open(array('route' => 'advertisements.store','method'=>'POST','enctype'=>'multipart/form-data')) !!}


         <div class="row">

		    <div class="col-xs-12 col-sm-12 col-md-12">

		        <div class="form-group">

		            <strong>Name:</strong>

		            <input type="text" name="name" class="form-control" placeholder="Name">

		        </div>

		    </div>

            <div class="col-xs-12 col-sm-12 col-md-3">
       
                <div class="form-group">
                    <input id="image" type="file" class="form-control" name="image">
                   
                </div>

            </div>



		    <div class="col-xs-12 col-sm-12 col-md-12 text-center">

		            <button type="submit" class="btn btn-primary">Submit</button>

		    </div>

		</div>


   {!! Form::close() !!}



@endsection