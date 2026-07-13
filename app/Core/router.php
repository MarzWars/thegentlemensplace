<?php
// app/Core/Router.php
namespace App\Core;

class Router
{
    private array $routes = [];

    public function __construct()
    {
        $this->registerRoutes();
    }

    private function registerRoutes(): void
    {
        // Format: [method, uri-pattern, Controller::method, middleware[]]
        $this->routes = [
            // Public
            ['GET',  '/',                   'HomeController@index',               []],
            ['GET',  'sitemap.xml',         'SitemapController@index',            []],
            ['GET',  'sitemap-main.xml',     'SitemapController@main',             []],
            ['GET',  'performers',          'PerformerController@index',          ['age_gate']],
            ['GET',  'performers/category/{category}', 'PerformerController@index', ['age_gate']],
            ['GET',  'performer/{slug}',    'PerformerController@show',           ['age_gate']],

            // Auth
            ['GET',  'register',            'AuthController@showRegister',        ['guest']],
            ['POST', 'register',            'AuthController@register',            ['guest']],
            ['GET',  'login',               'AuthController@showLogin',           ['guest']],
            ['POST', 'login',               'AuthController@login',               ['guest']],
            ['GET',  'logout',              'AuthController@logout',              ['auth']],
            ['GET',  'verify-email/{token}','AuthController@verifyEmail',         []],
            ['GET',  'forgot-password',     'AuthController@showForgot',          ['guest']],
            ['POST', 'forgot-password',     'AuthController@sendReset',           ['guest']],
            ['GET',  'reset-password/{t}',  'AuthController@showReset',           ['guest']],
            ['POST', 'reset-password',      'AuthController@resetPassword',       ['guest']],

            // Credits
            ['GET',  'credits',             'CreditController@packages',          ['auth']],
            ['POST', 'credits/purchase',    'CreditController@initiatePurchase',  ['auth']],
            ['GET',  'credits/history',     'CreditController@history',           ['auth']],

            // PayFast
            ['GET',  'payment/success',     'PaymentController@success',          []],
            ['GET',  'payment/cancel',      'PaymentController@cancel',           []],
            ['POST', 'payment/notify',      'PaymentController@notify',           []],  // ITN webhook
            ['POST', 'payment/checkout',    'PaymentController@checkout',         ['auth']],
            ['POST', 'payment/simulate',    'PaymentController@simulate',         ['auth']], // POC sandbox

            // Calls
            ['POST', 'call/request',        'CallController@request',             ['auth','age_verified']],
            ['GET',  'call/connect/{token}','CallController@connect',             ['auth']],
            ['GET',  'call/history',        'CallController@history',             ['auth']],
            ['GET',  'call/ringing/{uuid}',  'CallController@ringing',             ['auth']],
            ['GET',  'call/status/{uuid}',   'CallController@status',              []],
            ['POST', 'call/billing-tick',    'CallController@tickBilling',         ['auth']],
            ['POST', 'call/accept/{uuid}',   'CallController@accept',              ['performer']],
            ['POST', 'call/decline/{uuid}',   'CallController@decline',             ['performer']],
            ['POST', 'call/end/{uuid}',        'CallController@end',                 ['auth']],
            ['GET',  'call/room/{uuid}',       'CallController@room',                ['auth']],
            ['GET',  'call/incoming-check',    'CallController@checkIncoming',       ['performer']],

            // Live Streaming
            ['GET',  'performer-dash/broadcast', 'StreamController@broadcast',    ['performer']],
            ['POST', 'stream/start',             'StreamController@start',        ['performer']],
            ['POST', 'stream/end',               'StreamController@end',          ['performer']],
            ['GET',  'stream/{slug}',            'StreamController@watch',        ['auth', 'age_verified']],
            ['POST', 'stream/billing-tick',      'StreamController@tickBilling',  ['auth']],
            ['POST', 'stream/tip',               'StreamController@tip',          ['auth']],

            // Telephony webhooks
            ['POST', 'webhook/call-status', 'BillingController@callStatus',       []],
            ['POST', 'webhook/call-billing','BillingController@callBilling',       []],

            // Account
            ['GET',  'account',             'ProfileController@index',            ['auth']],
            ['POST', 'account/update',      'ProfileController@update',           ['auth']],

            // Reviews
            ['POST', 'review/submit',       'ReviewController@submit',            ['auth']],

            // Admin (separate middleware checks role)
            ['GET',  'admin/login',          'AdminController@showLogin',          []],
            ['POST', 'admin/login',          'AdminController@login',              []],
            ['GET',  'admin/logout',         'AdminController@logout',             []],
            ['GET',  'admin',                'AdminController@dashboard',          ['admin']],
            ['GET',  'admin/users',          'AdminController@users',              ['admin']],
            ['POST', 'admin/users/approve/{id}', 'AdminController@approveUser',        ['admin']],
            ['POST', 'admin/users/suspend/{id}', 'AdminController@suspendUser',        ['admin']],
            ['POST', 'admin/users/ban/{id}',     'AdminController@banUser',            ['admin']],
            ['POST', 'admin/users/activate/{id}','AdminController@activateUser',       ['admin']],
            ['POST', 'admin/users/make-admin/{id}','AdminController@promoteToAdmin',   ['admin']],
            ['GET',  'admin/performers',     'AdminController@performers',         ['admin']],
            ['POST', 'admin/performers/add',     'AdminController@addPerformer',       ['admin']],
            ['POST', 'admin/performer/approve/{id}', 'AdminController@approvePerformer', ['admin']],
            ['POST', 'admin/performer/suspend/{id}', 'AdminController@suspendPerformer', ['admin']],
            ['POST', 'admin/performer/edit/{id}',   'AdminController@editPerformer',      ['admin']],
            ['POST', 'admin/performer/delete-media/{id}', 'AdminController@deletePerformerMedia', ['admin']],
            ['GET',  'admin/calls',          'AdminController@calls',              ['admin']],
            ['GET',  'admin/transactions',   'AdminController@transactions',       ['admin']],
            ['GET',  'admin/payouts',        'AdminController@payouts',            ['admin']],
            ['POST', 'admin/payout/process', 'AdminController@processPayout',     ['admin']],
            ['GET',  'admin/settings',       'AdminController@settings',           ['admin']],
            ['POST', 'admin/settings/save',  'AdminController@saveSetting',        ['admin']],
            ['POST', 'admin/settings/sync-rates', 'AdminController@syncRates',         ['admin']],
            ['GET',  'admin/admins',             'AdminController@adminsList',         ['admin']],
            ['POST', 'admin/admins/update-role/{id}','AdminController@updateAdminRole',['admin']],
            ['POST', 'admin/admins/toggle-active/{id}','AdminController@toggleAdminActive',['admin']],

            // Admin Proxy Mode (answer calls as any performer)
            ['GET',  'admin/call',                   'AdminController@adminCallPage',   ['admin']],
            ['GET',  'admin/call/pending',            'AdminController@pendingCalls',    ['admin']],
            ['POST', 'admin/call/answer/{uuid}',      'AdminController@answerCall',      ['admin']],
            ['GET',  'admin/call/room/{uuid}',        'AdminController@callRoom',        ['admin']],
            ['POST', 'admin/proxy-mode/toggle',       'AdminController@toggleProxyMode', ['admin']],


            // Performer Dashboard
            ['GET',  'performer-dash',      'PerformerDashController@dashboard',  ['performer']],
            ['POST', 'performer-dash/status','PerformerDashController@toggleStatus',['performer']],
            ['GET',  'performer-dash/earnings','PerformerDashController@earnings', ['performer']],
            ['POST', 'performer-dash/upload-photo', 'PerformerDashController@uploadPhoto', ['performer']],
            ['POST', 'performer-dash/settings', 'PerformerDashController@updateSettings', ['performer']],


            // Legal
            ['GET',  'terms',               'HomeController@terms',               []],
            ['GET',  'privacy',             'HomeController@privacy',             []],
            ['GET',  '2257',                'HomeController@usc2257',             []],
        ];
    }

    public function dispatch(string $uri): void
    {
        $uri    = trim($uri, '/');
        $method = $_SERVER['REQUEST_METHOD'];

        foreach ($this->routes as [$routeMethod, $pattern, $handler, $middleware]) {
            if ($routeMethod !== $method && !($routeMethod === 'GET' && $method === 'HEAD')) continue;

            // Normalise the stored pattern the same way we normalise the URI
            $normalPattern = trim($pattern, '/');
            $regex  = preg_replace('/\{[a-z_]+\}/', '([^/]+)', $normalPattern);
            if (!preg_match("#^{$regex}$#", $uri, $matches)) continue;

            // Run middleware
            $mw = new Middleware();
            if (!$mw->handle($middleware)) return;

            // Dispatch to controller
            array_shift($matches);
            [$controllerName, $actionName] = explode('@', $handler);
            $controllerClass = "\\App\\Controllers\\{$controllerName}";
            $controller = new $controllerClass();
            $controller->$actionName(...$matches);
            return;
        }

        // 404
        http_response_code(404);
        (new \App\Core\View())->render('errors/404', ['layout' => false]);
    }
}