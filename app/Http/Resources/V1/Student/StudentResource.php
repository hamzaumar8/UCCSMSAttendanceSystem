<?php

namespace App\Http\Resources\V1\Student;

use App\Http\Resources\V1\Level\LevelResource;
use App\Http\Resources\V1\Module\ModuleCollection;
use App\Http\Resources\V1\Module\ModuleResource;
use App\Http\Resources\V1\AttendanceStudent\AttendanceStudentResource;
use App\Http\Resources\V1\User\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class StudentResource extends JsonResource
{
    // public static $wrap = 'students';
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        return [
            'id' => $this->id,
            'index_number' => $this->index_number,
            'full_name' => $this->full_name(),
            'first_name' => $this->first_name,
            'surname' => $this->surname,
            'other_name' => $this->other_name,
            'gender' => $this->gender,
            'phone' => $this->phone,
            'group_no' => $this->group_no,
            'picture' => $this->picture_url(),
            'created_at' => Carbon::parse($this->created_at),
            // eager loading
            'user' => UserResource::make($this->whenLoaded('user')),
            'level' => LevelResource::make($this->whenLoaded('level')),
            'modules' => ModuleResource::collection(
                $this->whenLoaded('modules')
            ),
            'attendance' => AttendanceStudentResource::collection($this->whenLoaded('attendance')),
            'attendance_stats'=> [
                'total'=> $this->attendance_total(),
                'present'=> $this->attendance_present(),
                'absent'=> $this->attendance_absent(),
                'present_percentage'=> $this->attendance_present_percentage(),
                'absent_percentage'=> $this->attendance_absent_percentage(),
            ]

        ];
    }


    public function with($request)
    {
        return [
            'status' => 'success',
        ];
    }

    public function withResponse($request, $response)
    {
        $response->header('Accept', 'application/json');
        // $response->header('Version', '1.0.0');
    }
}