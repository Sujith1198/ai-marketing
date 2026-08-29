<?php

namespace App\Http\Controllers;

use App\Models\ScheduledPost;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        $posts = ScheduledPost::with(['campaign.product', 'content', 'socialAccount'])
            ->orderBy('scheduled_at', 'asc')
            ->get();

        return view('calendar.index', compact('posts'));
    }
}
