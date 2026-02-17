<?php

namespace App\Controllers;

class Dashboard extends BaseController
{
    public function admin()
    {
        $session = session();
        if (! $session->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        if ($session->get('role') !== 'admin') {
            return redirect()->to('/login')->with('error', 'Unauthorized');
        }

        return view('dashboard/admin');
    }

    public function restaurant()
    {
        $session = session();
        if (! $session->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        if ($session->get('role') !== 'restaurant') {
            return redirect()->to('/login')->with('error', 'Unauthorized');
        }

        return view('dashboard/restaurant');
    }
}
