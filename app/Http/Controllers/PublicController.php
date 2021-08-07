<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PublicController extends Controller
{
    public function getProducts($paginate)
    {

        $products  =  DB::table("products")
            ->inRandomOrder()
            ->paginate($paginate);

        return response()
            ->json([
                "products" => $products
            ], 200);
    }
    public function getProductsByCategory($category, $paginate)
    {

        $products  =  DB::table("products")
            ->where([
                "category_slug" => $category
            ])
            ->orderByDesc("id")
            ->paginate($paginate);
        return response()
            ->json([
                "products" => $products
            ], 200);
    }
    public function getProductsBySearch($key, $paginate)
    {

        $products  =  DB::table("products")
            ->where('name', 'like', '%' . $key . '%')
            ->orderByDesc("id")
            ->paginate($paginate);
        return response()
            ->json([
                "products" => $products
            ], 200);
    }

    public function getCategories()
    {
        $categories  =  DB::table("categories")
            ->orderByDesc("name")
            ->get();
        return response()
            ->json([
                "categories" => $categories
            ], 200);
    }
}
