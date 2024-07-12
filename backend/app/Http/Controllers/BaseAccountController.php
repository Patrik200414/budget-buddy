<?php

namespace App\Http\Controllers;

use App\AccountType;
use App\AccountValidationTrait;
use App\Exceptions\ForbiddenAccountModification;
use App\Exceptions\NonExistingAccount;
use App\Exceptions\NonExistingUserException;
use App\Models\BaseAccount;
use App\UserFromBearerToken;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class BaseAccountController extends Controller
{
    use UserFromBearerToken, AccountValidationTrait;
    public function blockAccount(Request $request, string $accountId){
        try{
            $user = $this->getUserFromBearerToken($request);
            $account = BaseAccount::where(['id'=>$accountId])->first();

            $this->validateIfAccountExists($account);
            $this->validateIfUserHasPermissionForAccount($account, $user);

            $account->is_account_blocked = true;
            $account->save();

            return response()->json(['status'=>'Account is successfully blocked!'], 202);
        } catch(NonExistingUserException | NonExistingAccount | ForbiddenAccountModification $e){
            return response()->json(['error'=>$e->getMessage()], $e->getCode());
        }
    }

    public function getAccountSummary(Request $request, string $accountId){
        try{
            $user = $this->getUserFromBearerToken($request);

            $account = BaseAccount::where(['id'=>$accountId, 'is_account_blocked'=>false])->first();
            $this->validateIfAccountExists($account);
            $this->validateIfUserHasPermissionForAccount($account, $user);

            $mappedAccountSummary = $this->convertAccountModelToSummary($account);
            return response()->json($mappedAccountSummary);
        } catch(NonExistingAccount | ForbiddenAccountModification $e){
            return response()->json(['error'=>$e->getMessage()], $e->getCode());
        }
    }

    public function getAccountSummariesByUser(Request $request){
        $user = $this->getUserFromBearerToken($request);

        $accounts = BaseAccount::all()->where('user_id', '=', $user->id);
        
        $mappedAccounts = $this->mapAccountsToAccountSummaries($accounts);

        return response()->json(['accounts'=>$mappedAccounts]);
    }

    public function getTransferToAccounts(Request $request, string $transferFromAccountId){
        $user = $this->getUserFromBearerToken($request);

        $accounts = BaseAccount::all()
            ->where('user_id', '=', $user->id)
            ->where('account_type', '!=', AccountType::HOLDINGS_ACCOUNT->value)
            ->where('id', '!=', $transferFromAccountId);

        $mappedAccounts = $this->mapAccountsToAccountSummaries($accounts);

        return response()->json(['test'=>$mappedAccounts]);
    }

    private function convertAccountModelToSummary(BaseAccount $account){
        return [
            'id'=>$account->id,
            'accountName'=>$account->account_name,
            'balance'=>$account->balance,
            'accountNumber'=>$account->account_number,
            'accountType'=>$account->account_type
        ];
    }

    private function mapAccountsToAccountSummaries(Collection $accounts){
        return $accounts->map(function($account){
            return $this->convertAccountModelToSummary($account);
        });
    }
}
