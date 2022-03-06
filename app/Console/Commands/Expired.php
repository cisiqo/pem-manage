<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class Expired extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check if the pem is expired';

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
        $expireds = DB::select('select `group`,`env`,`email`,`discription` from pem where date <= CURDATE() + interval 15 day');
        // 发送邮件
        foreach ($expireds as $row) {
            $results = DB::select('select name from `groups` where id = :id', ['id' => $row->group]);
            $group = $results[0]->name;

            $data = [
                'group' => $group,
                'env' => $row->env,
                'discription' => $row->discription
            ];

            Log::info("开始发送邮件");

            Mail::send('emails.expired', $data, function ($m) use ($row) {
                $m->to($row->email)->subject('Pem Expired');
                $m->from(env('MAIL_USERNAME'));
            });

            // 打印发送日志
            Log::info('给' . $row->email . '发送了一封邮件');
        }

    }
}
