<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BannedWord;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BannedWordController extends Controller
{
    /**
     * List all banned words (global + all streamer-specific).
     */
    public function index(Request $request): View
    {
        $search = $request->input('q');

        $query = BannedWord::with(['streamer', 'createdBy'])
            ->orderBy('streamer_id')
            ->orderBy('word');

        if ($search) {
            // Escape LIKE wildcards to prevent slow query attacks
            $escapedSearch = $this->escapeLikeWildcards($search);
            $query->where('word', 'like', '%' . $escapedSearch . '%');
        }

        $words = $query->paginate(config('pagination.admin_banned_words', 50))->withQueryString();

        return view('admin.banned-words', compact('words', 'search'));
    }

    /**
     * Store a new global banned word (admin-owned, streamer_id = NULL).
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'word' => ['required', 'string', 'max:100'],
        ]);

        $word = mb_strtolower(trim($request->input('word')));

        // Check for duplicate in global scope
        $exists = BannedWord::whereNull('streamer_id')
            ->where('word', $word)
            ->exists();

        if ($exists) {
            return back()->with('error', "Kata \"$word\" sudah ada di daftar global.");
        }

        BannedWord::create([
            'word'        => $word,
            'streamer_id' => null,
            'created_by'  => Auth::id(),
        ]);

        return back()->with('success', "Kata \"$word\" berhasil ditambahkan ke daftar global.");
    }

    /**
     * Delete any banned word (admin can delete both global and streamer-owned).
     */
    public function destroy(BannedWord $bannedWord): RedirectResponse
    {
        $word = $bannedWord->word;
        $bannedWord->delete();

        return back()->with('success', "Kata \"$word\" berhasil dihapus.");
    }
}
