@extends('layouts.admin')


@section('content')

    <div class="row">

        <div class="col-lg-12 margin-tb">

            <div class="pull-left">

                <h2> Show Charge</h2>

            </div>

            <div class="pull-right">

                <a class="btn btn-primary" href="{{ route('appointment_charges.index') }}"> Back</a>

            </div>

        </div>

    </div>


    <div class="row">

        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Amount:</strong>

                {{ $charges->amount }}

            </div>

        </div>

        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Doctor Name:</strong>

                {{ $charges->doctor->name }}

            </div>

        </div>


        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Status:</strong>

                {{ $charges->status?'Active':'Inactive' }}

            </div>

        </div>

    </div>

@endsection

