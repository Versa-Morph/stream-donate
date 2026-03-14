<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PolicyController extends Controller
{
    public function index()
    {
        return view('policies.index');
    }

    public function show(string $slug)
    {
        $policies = [
            'terms-of-service' => ['title' => 'Ketentuan Layanan'],
            'privacy-policy' => ['title' => 'Kebijakan Privasi'],
            'donation-policy' => ['title' => 'Kebijakan Donasi'],
            'product-policy' => ['title' => 'Kebijakan Produk Digital'],
            'refund-policy' => ['title' => 'Kebijakan Pengembalian Dana'],
            'payment-policy' => ['title' => 'Kebijakan Pembayaran'],
            'fraud-policy' => ['title' => 'Kebijakan Anti-Fraud'],
            'content-policy' => ['title' => 'Panduan Konten'],
            'ip-policy' => ['title' => 'Kebijakan Hak Kekayaan Intelektual'],
            'tax-policy' => ['title' => 'Kebijakan Pajak'],
            'age-policy' => ['title' => 'Kebijakan Batasan Usia'],
            'fee-policy' => ['title' => 'Transparansi Biaya Platform'],
        ];

        if (!isset($policies[$slug])) {
            abort(404);
        }

        return view('policies.show', [
            'title' => $policies[$slug]['title'],
            'slug' => $slug,
        ]);
    }
}
