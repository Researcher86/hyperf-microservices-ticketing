<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\BusinessException;
use App\Middleware\AuthMiddleware;
use App\Model\User;
use App\Request\SigninRequest;
use App\Request\SignupRequest;
use App\Service\JwtService;
use Hyperf\Contract\TranslatorInterface;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Swoole\Http\Status;

#[Controller(prefix: "/api/users")]
class UserController extends AbstractController
{
    private TranslatorInterface $translator;
    private JwtService $jwtService;

    public function __construct(TranslatorInterface $translator, JwtService $jwtService)
    {
        $this->translator = $translator;
        $this->jwtService = $jwtService;
    }

    #[RequestMapping(path:"currentuser", methods: "get")]
    #[Middleware(AuthMiddleware::class)]
    public function currentUser()
    {
        return $this->response->json([
            'currentUser' => $this->request->getAttribute('currentUser', null),
        ]);
    }

    #[RequestMapping(path:"signup", methods: "post")]
    public function signup(SignupRequest $request)
    {
        ['email' => $email, 'password' => $password] =  $request->validated();

        $user = User::query()->where(['email' => $email])->first();

        if ($user) {
            throw new BusinessException(Status::BAD_REQUEST, $this->translator->trans('app.email.exists'));
        }

        $user = User::create([
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT)
        ]);

        $token = $this->jwtService->encode(['id' => $user->id, 'email' => $user->email]);

        return $this->response
            ->json($user)
            ->withHeader('Token', $token)
            ->withStatus(Status::CREATED);
    }

    #[RequestMapping(path:"signin", methods: "post")]
    public function signin(SigninRequest $request)
    {
        ['email' => $email, 'password' => $password] =  $request->validated();

        $user = User::query()->where(['email' => $email])->first();
        if (!$user || !password_verify($password, $user->password)) {
            throw new BusinessException(
                Status::UNAUTHORIZED,
                $this->translator->trans('app.login.unauthorized')
            );
        }

        $token = $this->jwtService->encode(['id' => $user->id, 'email' => $user->email]);

        return $this->response
            ->json($user)
            ->withHeader('Token', $token)
            ->withStatus(Status::OK);
    }

    #[RequestMapping(path:"signout", methods: "post")]
    public function signout()
    {
        return $this->response
            ->json([])
            ->withHeader('Token', '')
            ->withStatus(Status::OK);
    }
}
