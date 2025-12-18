<?php

namespace App\Services\Dashboard;

use App\Core\UserManger;
use App\Enums\UserRole;
use App\Models\Article;
use App\Models\BannerAd;
use App\Models\Company;
use App\Models\User;
use App\Services\BaseCrudService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class CompanyService extends BaseCrudService
{

    public function __construct(private UserManger $userManger) {}
    protected function get_model(): Builder
    {
        return Company::with('user');
    }
    protected function postSave(Model $model, array $data)
    {
        /** @var Company $model */
        $email = $data['email'];
        $password = $data['password'];
        $associated_user = $model->user;

        $user = $this->userManger->createOrUpdateUser($associated_user?->id, $email, $password, $model->name, UserRole::COMPANY);

        $model->user()->associate($user);
        $model->save();
    }
}
