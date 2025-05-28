<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Items",
 *     description="APIs for managing items"
 * )
 */
class ItemController extends Controller
{
/**
 * @OA\Get(
 *     path="/api/items",
 *     summary="جلب جميع العناصر",
 *     description="تُعيد قائمة بجميع العناصر المتوفرة.",
 *     tags={"Items"},
 *     security={{"sanctum":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="قائمة العناصر",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="Item Name"),
 *                 @OA\Property(property="price", type="number", format="float", example=99.99),
 *                 @OA\Property(property="image_url", type="string", example="http://example.com/images/item.jpg"),
 *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-05-28T14:24:56Z"),
 *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-05-28T14:24:56Z")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="غير مصرح"
 *     )
 * )
 */
    public function index()
    {
        $items = Item::all();
        $items = $items->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'price' => $item->price,
                'image_url' => $item->image_url,
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at,
            ];
        });
        return response()->json($items , 201);
    }

    /**
     * Store a newly created resource in storage.
     */
        /**
 * @OA\Post(
 *     path="/api/items",
 *     summary="إضافة عنصر جديد",
 *     description="تُستخدم لإضافة عنصر جديد مع صورة.",
 *     tags={"Items"},
 *     security={{"sanctum":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 required={"name", "price", "image"},
 *                 @OA\Property(property="name", type="string", example="New Item"),
 *                 @OA\Property(property="price", type="integer", example=100),
 *                 @OA\Property(property="image", type="file")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="تم إضافة العنصر بنجاح",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="item add successfully")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="خطأ في التحقق من البيانات"
 *     )
 * )
 */
    public function store(Request $request)
    {
        $data = $request->validate([
            "name" => 'required|string',
            "price" => 'required|integer',
            "image" => 'required|image',
        ]);
        $item = new Item;
        $item->name = $data['name'];
        $item->price = $data['price'];
        if ($data["image"]) {
            $image = $data["image"];
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images'), $imageName);
            $item->image = $imageName;
        }
        $item->save();
        return response()->json(["message" => "item add successfully"], 201);
    }

    /**
     * Display the specified resource.
     */
/**
 * @OA\Get(
 *     path="/api/items/{id}",
 *     summary="جلب عنصر معين",
 *     description="يعرض تفاصيل عنصر معين باستخدام ID.",
 *     tags={"Items"},
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID الخاص بالعنصر",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="تم جلب العنصر",
 *         @OA\JsonContent(
 *             @OA\Property(property="id", type="integer", example=1),
 *             @OA\Property(property="name", type="string", example="Item Name"),
 *             @OA\Property(property="price", type="integer", example=150),
 *             @OA\Property(property="image_url", type="string", example="http://example.com/images/item.jpg"),
 *             @OA\Property(property="created_at", type="string", format="date-time"),
 *             @OA\Property(property="updated_at", type="string", format="date-time")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="العنصر غير موجود"
 *     )
 * )
 */
    public function show($id)
    {
        $item = Item::where("id" , $id)->first();
        if ($item) {
            $transformedCategory = [
                'id' => $item->id,
                'name' => $item->name,
                'price' => $item->price,
                'image_url' => $item->image_url,
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at,
            ];
            return response()->json($transformedCategory , 201);
        } else {
            return response()->json(["message" => "item didn't exist"], 401);
        }
    }

/**
 * @OA\Post(
 *     path="/api/items/{id}",
 *     summary="تحديث عنصر",
 *     description="تحديث بيانات العنصر وإمكانية رفع صورة جديدة.",
 *     tags={"Items"},
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(
 *         name="id", in="path", required=true,
 *         description="معرّف العنصر",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 type="object",
 *                 required={"name","price"},
 *                 @OA\Property(property="name", type="string", example="Updated Item"),
 *                 @OA\Property(property="price", type="integer", example=120),
 *                 @OA\Property(
 *                     property="image",
 *                     type="string",
 *                     format="binary",
 *                     description="اختر ملف صورة (اختياري)"
 *                 ),
 *                 @OA\Property(property="_method", type="string", example="PUT")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="تم التحديث بنجاح",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="item update successfully")
 *         )
 *     ),
 *     @OA\Response(response=401, description="غير مصرح"),
 *     @OA\Response(response=404, description="العنصر غير موجود")
 * )
 */

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            "name" => 'required|string',
            "price" => 'required|integer',
            "image" => 'sometimes|nullable|image',
        ]);
        $item = Item::where("id" , $id)->first();
        if ($item) {
            if (isset($data["image"])) {
                $image = $data["image"];
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('images'), $imageName);
                $image = $imageName;
            } else {
                $image = $item->image;
            }
            $item->update([
                'name' => $data['name'],
                'price' => $data['price'],
                'image' => $image,
            ]);
            return response()->json(["message" => "item update successfully"], 201);
        }
        return response()->json(["message" => "item didn't exist"], 401);
    }

    /**
     * Remove the specified resource from storage.
     */
/**
 * @OA\Delete(
 *     path="/api/items/{id}",
 *     summary="حذف عنصر",
 *     description="حذف عنصر معين باستخدام ID.",
 *     tags={"Items"},
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID الخاص بالعنصر",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="تم الحذف بنجاح",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="item was deleted successfully")
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="العنصر غير موجود"
 *     )
 * )
 */
    public function destroy($id)
    {
        $item = Item::where("id" , $id)->first();
        if ($item) {
            $item->delete();
            return response()->json(["message" => "item was deleted successfully"], 201);
        } else {
            return response()->json(["message" => "item didn't exist"], 401);
        }
    }
}
