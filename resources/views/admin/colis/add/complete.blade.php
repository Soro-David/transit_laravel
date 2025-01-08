@extends('admin.layouts.admin')

@section('content-header')
@endsection

@section('content')
    @csrf
    <section>
        <div class="container">
            <h1>QR Code</h1>
            <img src="{{ asset($filePath) }}" alt="QR Code" style="max-width: 300px;">
        </div>
    </section>
    <style>
        section {
            background-color: #fff !important;
        }

        table.dataTable {
            width: 100% !important;
        }

        table.dataTable th,
        table.dataTable td {
            white-space: nowrap;
        }
    </style>
@endsection
