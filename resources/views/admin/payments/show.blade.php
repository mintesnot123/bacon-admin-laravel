@extends('layouts.admin')


@section('content')

    <div class="row">

        <div class="col-lg-12 margin-tb">

            <div class="pull-left">

                <h2> Show Payment</h2>

            </div>

            <div class="pull-right">

                <a class="btn btn-primary" href="{{ route('payments.index') }}"> Back</a>

            </div>

        </div>

    </div>


    <div class="row">

        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Created Date:</strong>

                {{ $payment->created_at }}

            </div>

        </div>

        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Invoice #:</strong>

                {{ $payment->invoice_no }}

            </div>

        </div>

        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Appointment #:</strong>
               <a target="_blank" href="{{ route('appointments.show',$payment->appointment->id) }}">{{$payment->appointment->appoint_no}}</a>

            </div>
        </div>

        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">
                <strong>Payment Status :</strong>
                @if($payment->payment_type==1) {!!'Payment Pending'!!}@endif
                @if($payment->payment_type==2) {!!'Payment Completed'!!}@endif
                @if($payment->payment_type==3) {!!'Payment Cancelled'!!}@endif

            </div>

        </div>

        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Payment Amount (BDT):</strong>

                {{ $payment->amount }}

            </div>

        </div>

        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>payeer #:</strong>

                {{ $payment->payeer_id }}

            </div>

        </div>

        <div class="col-xs-12 col-sm-12 col-md-12">

            <div class="form-group">

                <strong>Payments logs:</strong>

                {{ $payment->logs }}

            </div>

        </div>

    </div>

@endsection

