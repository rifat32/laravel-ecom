<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class UserController extends Controller
{
    public function getCarts(Request $request)
    {
        $user = $request->user();
        $carts =  DB::table('carts')
            ->where([
                "user_id" => $user->id,
                "order_id" => null
            ])
            ->join('products', 'carts.product_id', '=', 'products.id')
            ->select('carts.*', 'products.name', 'products.unit_price', 'products.image_url')
            ->orderBy("id")
            ->get();
        return response()->json(["carts" => $carts], 200);
    }
    public function createCarts(Request $request)
    {
        $user = $request->user();
        $productId = (int)$request->productId;
        $cartQuery =  DB::table('carts')
            ->where([
                "user_id" => $user->id,
                "product_id" => $productId,
                "order_id" => null
            ]);
        $cart = $cartQuery->first();
        if (count((array)$cart)) {
            $cartQuery->update([
                "quantity" => $cart->quantity + 1,
                "updated_at" => \Carbon\Carbon::now(),
            ]);
            return response()->json([
                "message" => "cart updated successfully"
            ], 200);
        } else {
            DB::table('carts')
                ->insert([
                    "user_id" => $user->id,
                    "product_id" => $productId,
                    "quantity" => 1,
                    "created_at" =>  \Carbon\Carbon::now(),
                    "updated_at" => \Carbon\Carbon::now(),
                ]);
            return response()->json([
                "message" => "cart created successfully"
            ], 201);
        }
    }
    public function updateCarts(Request $request, $id)
    {
        $user = $request->user();
        $incrementDecrement = $request->incrementDecrement;

        $cartQuery = DB::table('carts')
            ->where([
                'id' => $id,
                "user_id" => $user->id
            ]);
        $cart = $cartQuery->first();

        if ($incrementDecrement == "increment") {
            $cartQuery->update([
                "quantity" => $cart->quantity + 1
            ]);
        } else {
            if ($cart->quantity > 1) {
                $cartQuery->update([
                    "quantity" => $cart->quantity  - 1
                ]);
            }
        }
        return response()->json([
            "message" => "cart updated successfully"
        ], 201);
    }
    public function deleteSingleCart(Request $request, $id)
    {
        $user = $request->user();
        DB::table('carts')
            ->where([
                'id' => $id,
                "user_id" => $user->id
            ])
            ->delete();
        return response()->json([
            "message" => "cart deleted successfully"
        ], 204);
    }
    public function deleteAllCarts(Request $request)
    {
        $user = $request->user();
        DB::table('carts')
            ->where([
                "user_id" => $user->id
            ])
            ->delete();
        return response()->json([
            "message" => "carts are deleted successfully"
        ], 204);
    }
    public function  getOrders(Request $request)
    {
        $user = $request->user();
        $orders =  DB::table('orders')
            ->where([
                "user_id" => $user->id
            ])
            ->orderBy("id")
            ->get();
        return response()->json(["orders" => $orders], 200);
    }
    public function  createOrders(Request $request)
    {
        $customOrderIdFound = false;
        $random = null;
        while (!$customOrderIdFound) {
            $random = Str::random(10);
            $order =  DB::table('orders')
                ->where([
                    "custom_order_id" => $random
                ])
                ->first();
            if (count((array)$order)) {
                $customOrderIdFound = false;
            } else {
                $customOrderIdFound = true;
            }
        }
        $street = $request->street;
        $city = $request->city;
        $zipcode = $request->zipcode;
        $user = $request->user();
        $newOrderId =  DB::table('orders')
            ->insertGetId([
                "custom_order_id" => $random,
                "user_id" => $user->id,
                "street" => $street,
                "city" => $city,
                "zipcode" => $zipcode
            ]);

        DB::table('carts')
            ->where([
                "user_id" => $user->id
            ])
            ->update([
                "order_id" =>  $newOrderId
            ]);
        return response()->json([
            "message" => "order created successfully"
        ], 201);
    }
}
