@extends('layouts.admin')


@section('content')

    <div class="row">

        <div class="col-lg-12 margin-tb">

            <div class="pull-left">

                <h2> Show City</h2>

            </div>

            <div class="pull-right">

                <a class="btn btn-primary" href="{{ route('cities.index') }}"> Back</a>

            </div>

        </div>

    </div>


    <div class="row">

        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Name:</strong>

                {{ $cities->name }}

            </div>

        </div>

       <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Country Name:</strong>

                {{ $cities->country->name }}

            </div>

        </div>

        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Division Name:</strong>

                {{ $cities->division->name }}

            </div>

        </div>


        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Status:</strong>

                {{ $cities->status?'Active':'Inactive' }}

            </div>

        </div>

    </div>

@endsection

