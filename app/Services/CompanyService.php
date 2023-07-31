<?php

namespace App\Services;

use App\Models\Company;
use App\Models\CompanyUser;
use App\Models\User;
use Exception;

class CompanyService
{
    public function getCompanies(User $user)
    {
        return CompanyUser::query()
            ->select([
                'company' => 'company.title',
                'phone' => 'company.phone',
                'description' => 'company.description',
            ])
            ->rightJoin('company', 'company_user.company_id', '=', 'company.id')
            ->where(['company_user.user_id' => $user->id])
            ->get();
    }

    public function addCompany(User $user, array $companiesData)
    {
        foreach ($companiesData as $companyData) {
            app('db')->transaction(function () use ($companyData, $user) {
                $company = new Company($companyData);
                if (!$company->save()) {
                    throw new Exception('Something went wrong');
                }
                $companyUser = new CompanyUser([
                    'user_id' => $user->id,
                    'company_id' => $company->id
                ]);
                if (!$companyUser->save()) {
                    throw new Exception('Something went wrong');
                }
            });
        }

        return true;
    }
}
