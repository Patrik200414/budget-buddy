<?php

namespace App\Console\Commands;

use App\Models\TransactionCategory;
use App\Models\TransactionSubcategory;
use App\Models\TransactionType;
use Exception;
use Illuminate\Console\Command;
use Storage;

class IntegrateTransactionCategories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:integrate-transaction-categories';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'With this command you can integrate the categories from /storage/data. The categories are in JSON files.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try{

            \DB::transaction(function(){
                $jsonFile = file_get_contents(storage_path() . '/data/transaction_categories.json');
                $json = json_decode($jsonFile);

                foreach($json as $transactions=>$transaction){
                    /**
                     * Save transaction types to transaction_types table
                     */
                    $createdTransactionType = TransactionType::create([
                        'transaction_type'=>$transactions
                    ]);
                    foreach($transaction as $transactionCategories=>$transactionCategory){
                        /**
                         * Save transaction category to transaction_categories table
                         */
                        $createdTransactionCategory = TransactionCategory::create([
                            'transaction_category_name'=>$transactionCategories,
                            'transaction_type_id'=> $createdTransactionType->id
                        ]);
                        foreach($transactionCategory as $transactionSubcategory){
                            /**
                             * Save transaction subcategory to transaction_subcategory
                             */
                            TransactionSubcategory::create([
                                'transaction_subcategory_name'=>$transactionSubcategory,
                                'transaction_category_id'=>$createdTransactionCategory->id
                            ]);
                        }
                    }
                }
            });
            
            return $this->info('Transaction category migration was successfull!');
        } catch(Exception $e){
            $this->error($e->getMessage());
        }
    }
}
