<?php

namespace App\Http\Controllers;

use App\AccountType;
use App\AccountValidationTrait;
use App\CreateHoldingsAccount;
use App\Exceptions\ForbiddenAccountModification;
use App\Exceptions\NonExistingAccount;
use App\Exceptions\NonExistingUserException;
use App\Models\BaseAccount;
use App\Models\User;
use App\UserFromBearerToken;
use DB;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class BaseAccountController extends Controller
{
    use UserFromBearerToken, AccountValidationTrait, CreateHoldingsAccount;

    const MIN_NUM_FOR_HOLDINGS_ACCOUNT_NUMBER = 100000000;
    const MAX_NUM_FOR_HOLDINGS_ACCOUNT_NUMBER = 999999999999999;

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

        return response()->json(['accounts'=>$mappedAccounts]);
    }

    public function deleteAccount(Request $request, string $accountId){
        try{
            return DB::transaction(function() use($request, $accountId) {
                $user = $this->getUserFromBearerToken($request);
                $deletableBaseAccount = BaseAccount::with('accountable')->where(['id'=>$accountId])->first();
                $this->validateIfAccountExists($deletableBaseAccount);
                $this->validateIfUserHasPermissionForAccount($deletableBaseAccount, $user);
                $deletableBalance = $deletableBaseAccount->balance;
    
                $transferAccountId = $request->transferAccountId;
    
                if($transferAccountId){
                    $this->transferBalanceToOtherAccount($transferAccountId, $user, $deletableBalance);
                } else {
                    $this->saveHoldingsAccount($user, $deletableBalance, self::MIN_NUM_FOR_HOLDINGS_ACCOUNT_NUMBER, self::MAX_NUM_FOR_HOLDINGS_ACCOUNT_NUMBER);
                }
    
                $deletableSavingsAccountId = $deletableBaseAccount->accountable;
                $deletableSavingsAccountId->delete();
                $deletableBaseAccount->delete();

                return response()->json(['message'=>'Deletion was successfull!'], 200);
            });

        } catch(NonExistingAccount | ForbiddenAccountModification $e){
            return response()->json(['error'=>$e->getMessage()], $e->getCode());
        }
    }

    private function transferBalanceToOtherAccount(string $transferAccountId, User $user, float $deletableBalance){
        $transferAccount = BaseAccount::with('accountable')->where(['id'=>$transferAccountId])->first();
        $this->validateIfAccountExists($transferAccount);
        $this->validateIfUserHasPermissionForAccount($transferAccount, $user);

        $transferAccount->balance += $deletableBalance;
        $transferAccount->save();
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
        $mappedAccounts = [];
        foreach($accounts as $account){
            $mappedAccounts[] = $this->convertAccountModelToSummary($account);
        }
        return $mappedAccounts;
    }
}
