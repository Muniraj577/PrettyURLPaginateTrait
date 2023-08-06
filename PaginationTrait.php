<?php

namespace App\Traits;

use Illuminate\Pagination\Paginator;

trait PaginateTrait {
    public static function scopePaginateUri($query, $items, $page)
    {
        $action = app('request')->route()->getActionName();
        $parameters = app('request')->route()->parameters();
        $parameters['page'] = '##'; // ## == %23%23
        $current_url = action($action, $parameters);
        $current_url = preg_replace('/[\?\=]/', '/', $current_url);
        Paginator::currentPageResolver(function () use ($page) {
            return $page;
        });
        $paginate = $query->paginate($items);
        $links = preg_replace('@href="(.*/?page=(\d+))"@U', 'href="' . str_replace('##', '$2', $current_url) . '"', $paginate->render());
        $paginate->linksUri = $links;
        return $paginate;
    }

}

/**
 * usage in  a controller:
 * public function index($page=1) {
 *     $users = User::paginateUri(5, $page);
 *     return view('users', compact('users'));
 * }
 * it also support variable number of parameters,
 * routes must be defined using {page} or {page?} placeholders
 * $links is returned as reference and contain a standard bootstrap 3 navigation
 * Add to model (ex.User):
 * use PaginateTrait;
 *
 * AND in view: {!! $users->linksUri !!}
 **/

