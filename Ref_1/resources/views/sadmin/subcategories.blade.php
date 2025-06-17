@extends('layouts.sadmin')

@section('content')
    <div class="row align-items-center">
        <div class="col-md-6">
            <h2>Sub Categories</h2>
        </div>
        <div class="col-md-6 text-end">
            <button type="button" class="btn btn-sm btn-info"
                    title="Add SubCategory" data-bs-toggle="modal" data-bs-target="#addsubcat">
                Add New SubCategory
            </button>
        </div>
    </div>
@endsection
