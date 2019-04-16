<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use App\Transformers\CategoryTranformer;
use Illuminate\Http\Request;

class CategoriesController extends Controller
{
    //
    public function index()
    {
        return $this->response->collection(Category::all(),new CategoryTranformer());
    }
}
