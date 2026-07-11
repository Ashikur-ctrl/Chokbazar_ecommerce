<?php

namespace App\Services\Seller;

use App\Models\Seller;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    /**
     * Register a new seller account.
     * Creates User + Seller in a single transaction.
     */
    public function register(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $seller = Seller::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'company_name' => $data['company_name'],
                'description' => $data['description'] ?? null,
                'business_type' => $data['business_type'] ?? null,
                'year_established' => $data['year_established'] ?? null,
                'website_url' => $data['website_url'] ?? null,
                'whatsapp_number' => $data['whatsapp_number'] ?? null,
                'address' => $data['address'] ?? null,
                'city' => $data['city'] ?? null,
                'state' => $data['state'] ?? null,
                'postal_code' => $data['postal_code'] ?? null,
                'country' => $data['country'] ?? null,
                'verification_status' => 'pending',
                'is_active' => true,
                'fulfillment_method' => 'email',
            ]);

            $seller->generateApiKey();

            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => 'seller',
                'seller_id' => $seller->id,
            ]);

            return $user;
        });
    }
}
