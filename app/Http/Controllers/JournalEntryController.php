<?php

namespace App\Http\Controllers;

use App\Models\JournalEntry;
use Illuminate\Http\Request;

class JournalEntryController extends Controller
{
    public function index(Request $request)
    {
        $entries = JournalEntry::query()
            ->when($request->search, function ($query, $search) {
                $query->where('entry_number', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(20);

        return view('journal-entries.index', compact('entries'));
    }
}
