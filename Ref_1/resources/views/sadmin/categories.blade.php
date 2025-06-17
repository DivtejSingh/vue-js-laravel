@extends('layouts.sadmin')

@section('content')
    <div class="row align-items-center">
        <div class="col-md-6">
            <h2>Categories</h2>
        </div>
        <div class="col-md-6 text-end">
            <button type="button" class="btn btn-sm btn-primary"
                    title="Add Category" data-bs-toggle="modal" data-bs-target="#addcat">
                Add New Category
            </button>
            <button type="button" class="btn btn-sm btn-info"
                    title="Add SubCategory" data-bs-toggle="modal" data-bs-target="#addsubcat">
                Add New SubCategory
            </button>
        </div>
    </div>

    <div class="table-group-divider"></div>
    <table class="table table-striped table-sm">
        <thead>
        <tr>
            <th>Id</th>
            <th>Name</th>
            <th>image</th>
            <th>Slug</th>
            <th>Feature</th>
            <th>Sort</th>
            <th>Status</th>
            <th>Created</th>
            <th width="125px">Actions</th>
        </tr>
        </thead>
        <tbody>
        @foreach($categories as $cats)
        <tr>
            <td>{{$cats->id}}</td>
            <td>{{$cats->cat_name}}</td>
            <td>{{$cats->cat_img_url}}</td>
            <td>{{$cats->cat_slug}}</td>
            <td>{{$cats->cat_feature}}</td>
            <td>{{$cats->cat_sort}}</td>
            <td>{{$cats->cat_status}}</td>
            <td>{{$cats->created_at}}</td>
            <td>
                <a href="#" class="btn btn-sm btn-primary">Edit</a>
                <a href="#" class="btn btn-sm btn-danger">Delete</a>
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>

    {{--    Add Category Modal Start--}}
    <div class="modal fade" id="addcat" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addcat" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addcat">Add Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="#">
                        <div class="mb-2">
                            <label for="cat_name" class="form-label">Category Name</label>
                            <input type="text" class="form-control" name="cat_name" placeholder="Category Name" required>
                        </div>
                        <div class="mb-2">
                            <label for="cat_img_url" class="form-label">Category Image</label>
                            <input type="file" class="form-control" name="cat_img_url" placeholder="Image" required>
                        </div>
                        <div class="mb-2 d-none">
                            <label for="cat_slug" class="form-label">Category Slug</label>
                            <input type="text" class="form-control" name="cat_slug" placeholder="Category Slug">
                        </div>
                        <div class="row">
                            <div class="mb-2 col">
                                <label for="cat_feature" class="form-label">Category Feature</label>
                                <select class="form-select" name="cat_feature">
                                    <option value="0" selected>None</option>
                                    <option value="1">Popular</option>
                                    <option value="2">Featured</option>
                                </select>
                            </div>
                            <div class="mb-2 col">
                                <label for="cat_sort" class="form-label">Category Sort</label>
                                <input type="number" class="form-control" name="cat_sort" placeholder="1" required>
                            </div>
                            <div class="mb-2 col">
                                <label for="cat_status" class="form-label">Category Status</label>
                                <select type="text" class="form-select" name="cat_status">
                                    <option value="1" selected>Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="text-center mt-2">
                            <button type="submit" class="btn btn-danger w-75">Add Category</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{--    Add Category Modal End--}}

    {{--    Add SubCategory Modal Start--}}
    <div class="modal fade" id="addsubcat" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addsubcat" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addsubcat">Add SubCategory</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="#">
                        <div class="mb-2">
                            <label for="subcat_name" class="form-label">Subcategory Name</label>
                            <input type="text" class="form-control" name="cat_name" placeholder="Category Name" required>
                        </div>
                        <div class="mb-2 d-none">
                            <label for="subcat_slug" class="form-label">Subcategory Slug</label>
                            <input type="text" class="form-control" name="subcat_slug" placeholder="subcategory Slug">
                        </div>
                        <div class="mb-2">
                            <label for="subcat_status" class="form-label">Subcategory Status</label>
                            <select type="text" class="form-select" name="subcat_status">
                                <option value="1" selected>Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                         <div class="text-center mt-2">
                            <button type="submit" class="btn btn-danger w-75">Add Category</button>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Understood</button>
                </div>
            </div>
        </div>
    </div>
    {{--    Add SubCategory Modal End--}}
@endsection
