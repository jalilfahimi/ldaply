<?php

namespace App\Console\Commands;

use CORE;
use App\Models\User;
use CFG;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class SystemInstall extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:install {firstname} {lastname} {email} {password} ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Installs the root admin and the default configurations';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $defaults = CORE::defaults();
            $passwordminlength = $defaults['passwordminlength'];
            $passwordregex = $defaults['passwordregex'];

            $firstname = $this->argument('firstname');
            $lastname = $this->argument('lastname');
            $email = $this->argument('email');
            $password = $this->argument('password');

            $inputs = ['password' =>  $password];
            $rules = [
                'password' => [
                    'required',
                    'string',
                    'min:' . $passwordminlength . '',       // must be at least $minlength characters in length
                    'regex:/' . $passwordregex . '/',       // must pass regex
                ],
            ];

            $validation = Validator::make($inputs, $rules);

            if ($validation->fails()) {
                print_r($validation->errors()->all());
                return Command::FAILURE;
            }

            $this->call('db:wipe');
            $this->call('migrate');

            User::create([
                'firstname' => $firstname,
                'lastname' => $lastname,
                'email' => $email,
                'password' => bcrypt($password),
                'auth' => 'manual',
                'language' => $defaults['language'],
            ]);

            foreach ($defaults as $k => $v) {
                CFG::set($k, $v);
            }

            $this->call('scheduledtasks:update');

            CFG::set('installed', '1');
            CFG::set('maintenancemode', '0');
            CFG::set('cronisstarted', '0');

            return Command::SUCCESS;
        } catch (Exception $e) {
            CORE::logcli($e);
            return Command::FAILURE;
        }
    }
}
