<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Mail\OutofStockMail;
use App\Models\InventoryItem;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ProductOutOfStockNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'out-of-stock:notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'send mail to store officers when product is out of stock';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $products = DB::table('inventory_items')->where('quantity','<','1')->get();
        // foreach ($products as $product) {
        //     // if($product->quantity < 1){
        //         $product = [
        //             'id' => $product->id,
        //             'description' => $product->description,
                
        //         ];
             
        // }
        $users = DB::table('users')->where('role_id','=','5')->get();
            // $users = User::where('role_id', '=','5')->get();
            foreach ($users as $key =>  $user) {
                Mail::to($user->email)->send(new OutofStockMail($products));
                
            }
         
            return response()->json(['success'=>'Send email successfully.']);
        }
       
        
    
}
