<?php

namespace App\Console\Commands;

use App\Models\AutoAssignment;
use App\Services\AutoAssignmentService;
use Illuminate\Console\Command;
use Carbon\Carbon;

class AutoCancelAutoAssign extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:auto_assignment_cancel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make available pre-assigned order to driver';

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


        //
        $secondsFromNowAgo = Carbon::now()->subSeconds(setting('alertDuration', 60) * 2)->toTimeString();
        $autoAssignments = AutoAssignment::where('status','pending')->whereTime('updated_at', '<', $secondsFromNowAgo)->limit(10)->get();
        //loop through and delte the records while also sending notification to the driver
        foreach ($autoAssignments as $autoAssignment) {
            //driver 
            $driver = $autoAssignment->driver;
            //send the new order failture to driver via push notification
            $autoAssignmentSerivce = new AutoAssignmentService();
            $autoAssignmentSerivce->sendFailedNewOrderNotification($driver, $autoAssignment);

            try {
                //reject the auto-assignment
                $autoAssignment->status = "rejected";
                $autoAssignment->save();
            } catch (\Exception $ex) {
                logger("error while rejecting order", [$ex]);
            }
        }



        //allow a resend of rejected auto-assignments
        $clearRejectedAutoAssignment = ((int)setting('clearRejectedAutoAssignment', 0)) ?? 0;

        if ($clearRejectedAutoAssignment > 0) {
            $minutesFromNowAgo = Carbon::now()->subMinutes($clearRejectedAutoAssignment)->toTimeString();
            $deletedAutoAssignments = AutoAssignment::whereTime('updated_at', '<', $minutesFromNowAgo)->limit(20)->delete();
        }
        return 0;
    }
}
