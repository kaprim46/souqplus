<?php

namespace App\Services;

use Illuminate\Http\Request;
use Kreait\Firebase;
use Kreait\Firebase\Factory;

class FirebaseService
{
    /**
     * Connect to Firebase
     * @return Firebase\Database
     */
    public static function connect(): Firebase\Database
    {
        $firebase = (new Factory)
            ->withServiceAccount(base_path(env('FIREBASE_CREDENTIALS')))
            ->withDatabaseUri(env("FIREBASE_DATABASE_URL"));

        return $firebase->createDatabase();
    }
}