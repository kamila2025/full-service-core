<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Helpers\TaiwanCities;
use App\Helpers\TaiwanZipcode;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TaiwanCitiesController extends Controller
{
  /**
   * 取得所有城市列表
   */
  public function getCities(Request $request): JsonResponse
  {
    $cities = TaiwanCities::getCities();
    $searchTerm = $request->get('q', '');

    $formattedCities = [];
    foreach ($cities as $key => $city) {
      // 如果沒有搜尋詞或城市名稱包含搜尋詞，則加入結果
      if (empty($searchTerm) || strpos($city, $searchTerm) !== false) {
        $formattedCities[] = [
          'id'    => $city,
          'text'  => $city
        ];
      }
    }

    return response()->json([
      'results' => $formattedCities
    ]);
  }

  /**
   * 根據城市取得地區列表
   */
  public function getDistricts(Request $request, string $city): JsonResponse
  {
    $districts = TaiwanCities::getDistricts($city);
    $searchTerm = $request->get('q', '');

    // 轉換為 Select2 格式
    $formattedDistricts = [];
    foreach ($districts as $district) {
      // 如果沒有搜尋詞或地區名稱包含搜尋詞，則加入結果
      if (empty($searchTerm) || strpos($district, $searchTerm) !== false) {
        $formattedDistricts[] = [
          'id'    => $district,
          'text'  => $district
        ];
      }
    }

    return response()->json([
      'results' => $formattedDistricts
    ]);
  }

  /**
   * 根據城市和地區取得郵遞區號
   */
  public function getZipcode(string $city, string $district): JsonResponse
  {
    $zipcode = TaiwanZipcode::getZipcode($city, $district);

    return response()->json([
      'zipcode' => $zipcode
    ]);
  }
}
