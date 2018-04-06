<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class RedirectingController extends Controller
{
    /**
     * @Route("/", name="home_page")
     */
    public function index()
    {
        return $this->redirectToRoute('web_list');
    }


    /**
     * @Route("/{url}", name="remove_trailing_slash",
     *     requirements={"url" = ".*\/$"})
     */
    public function removeTrailingSlash(Request $request)
    {
        $pathInfo = $request->getPathInfo();
        $requestUri = $request->getRequestUri();

        $url = str_replace($pathInfo, rtrim($pathInfo, ' /'), $requestUri);

        // 308 (Permanent Redirect) is similar to 301 (Moved Permanently) except
        // that it does not allow changing the request method (e.g. from POST to GET)
        return $this->redirect($url, 308);
    }
}
