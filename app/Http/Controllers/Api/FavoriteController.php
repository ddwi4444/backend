<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FavoriteKomikModel;
use App\Models\KomikModel;

class FavoriteController extends Controller
{
    public function klikFavorite($uuidKomik, $uuidUser)
    {
        $data = FavoriteKomikModel::where('komik_uuid', $uuidKomik)
            ->where('user_uuid', $uuidUser)
            ->first();
        // If data does not exist, create a new record
        if (empty($data)) {
            // Create a new record in FavoriteKomikModel with 'komik_uuid' set
            FavoriteKomikModel::create([
                'komik_uuid' => $uuidKomik,
                'user_uuid' => $uuidUser,
                // Other fields you may want to set
            ]);

            // You can also do something after creating the record, if needed
        } else {
            // Data already exists, delete the existing record
            $data->delete();

            // You can also do something after deleting the record, if needed
        }
    }

    public function getDataKomikFavorite($user_uuid)
    {
        $dataKomikFavorites = FavoriteKomikModel::where('user_uuid', $user_uuid)->get();

        return response([
            'message' => 'Favorite comics is succesfully show',
            'dataKomikFavorites' => $dataKomikFavorites,
        ], 200);
    }

    public function getDataKomikFavoriteShow($user_uuid)
{
    // Get data from FavoriteKomikModel
    $dataKomikFavorites = FavoriteKomikModel::where('user_uuid', $user_uuid)->get();

    // Extract user_uuid and komik_uuid from FavoriteKomikModel
    $komikUuids = $dataKomikFavorites->pluck('komik_uuid')->toArray();

    // Get data from KomikModel with matching user_uuid or komik_uuid
    $dataKomikFavoritesShow = KomikModel::whereIn('uuid', $komikUuids)
        ->get();

    return response([
        'message' => 'Favorite comics are successfully shown',
        'dataKomikFavorites' => $dataKomikFavorites,
        'dataKomikFavoritesShow' => $dataKomikFavoritesShow,
    ], 200);
}

}
