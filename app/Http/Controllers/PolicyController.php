<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

/**
 * Controller for displaying legal and policy pages.
 */
class PolicyController extends Controller
{
    /**
     * Display the policy index page with links to all policies.
     *
     * @return \Illuminate\View\View
     */
    public function index(): View
    {
        return view('policies.index');
    }

    /**
     * Display a specific policy page.
     *
     * @param string $slug The policy slug identifier
     * @return \Illuminate\View\View
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException If policy not found
     */
    public function show(string $slug): View
    {
        $policies = [
            'terms-of-service' => ['title' => 'Ketentuan Layanan'],
            'privacy-policy' => ['title' => 'Kebijakan Privasi'],
            'security-policy' => ['title' => 'Kebijakan Keamanan Data'],
            'cookie-policy' => ['title' => 'Kebijakan Cookie'],
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
