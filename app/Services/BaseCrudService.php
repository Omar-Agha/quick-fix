<?php

namespace App\Services;

use App\Models\Hotel;
use App\Models\RoomTemplate;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class RoomTemplateService
{
    protected function  base_query(){
        return RoomTemplate::query();
     }
    public function getAll(): Collection
    {
        return $this->base_query()->all();
    }

    public function getPaginated(int $page = 1, int $perPage = 10): LengthAwarePaginator
    {
        
        return $this->base_query()->paginate($perPage, ['*'], 'page', $page);
    }

    public function getById(int $id): ?RoomTemplate
    {
        return $this->base_query()->find($id);
    }

    public function create(array $data): RoomTemplate
    {
        
        $hotel=Hotel::first();
        // $new_record = $this->base_query()->create($data);
        $new_record= new RoomTemplate($data);
        $new_record->hotel()->associate($hotel);
        $new_record->save();
        return $new_record;
    }

    public function update(int $id, array $data): ?RoomTemplate
    {
        $old_record = $this->base_query()->find($id);
        
        if (!$old_record) {
            return null;
        }

        $old_record->update($data);
        return $old_record;
        
    }

    public function delete(int $id): bool
    {
        $room_template = $this->base_query()->find($id);
        
        if (!$room_template) {
            return false;
        }

        return $room_template->delete();
    }

   
}

