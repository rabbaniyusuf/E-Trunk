@extends('layouts.main')
@section('title', 'Preview Laporan')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between mb-3">
        <h4>Preview Laporan</h4>
        <div>
            <button onclick="window.print()" class="btn btn-outline-primary">
                <i class="bi bi-printer"></i> Print
            </button>
            <a href="{{ request()->fullUrlWithQuery(['format' => 'pdf']) }}"
               class="btn btn-primary">
                <i class="bi bi-download"></i> Download PDF
            </a>
        </div>
    </div>

    <!-- Include the same content as PDF template but with web styling -->
    @include('admin.reports.pdf-template')
</div>
@endsection
