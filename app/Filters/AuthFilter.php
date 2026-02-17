<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthFilter implements FilterInterface
{
    // Session timeout in seconds (30 minutes)
    protected $timeout = 1800;

    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        if (! $session->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Please login to continue');
        }

        $last = $session->get('last_activity') ?? 0;

        if (time() - $last > $this->timeout) {
            $session->destroy();
            return redirect()->to('/login')->with('error', 'Session timed out. Please login again.');
        }

        // Refresh last activity
        $session->set('last_activity', time());
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // no-op
    }
}
