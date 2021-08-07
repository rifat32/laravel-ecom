<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DbInsertController extends Controller
{
    public function insertProducts(Request $request)
    {
        $products = $request->products;
        foreach ($products as $product) {
            $slug = strtolower($product["name"]);
            $slug = str_replace(" ", "-", $slug);
            DB::table('products')
                ->insert([
                    'active' => $product["active"],
                    'description' => $product["description"],
                    'image_url' => $product["image_url"],
                    'name' => $product["name"],
                    'slug' =>  $slug,
                    'sku' => $product["sku"],
                    'unit_price' => $product["unit_price"],
                    'units_in_stock' => $product["units_in_stock"],
                    'category_slug' => $product["category_slug"],
                    'created_at' => $product["created_at"]
                ]);
        }
        return response()->json(["message" => "product inserted"], 201);
    }
    public function  insertcategories(Request $request)
    {
        $categories = $request->categories;
        foreach ($categories as $category) {
            DB::table('categories')
                ->insert([
                    'name' => $category["name"],
                    'slug' => $category["slug"]
                ]);
        }
        return response()->json(["message" => "category inserted"], 201);
    }
}
