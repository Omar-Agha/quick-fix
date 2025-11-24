<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class BaseCrudService
{
    abstract protected function get_model(): Model;

    public function getAll(): Collection
    {
        return $this->get_model()->all();
    }

    public function getPaginated(int $page = 1, int $perPage = 10): LengthAwarePaginator
    {

        return $this->get_model()->paginate($perPage, ['*'], 'page', $page);
    }

    public function getById(int $id): ?Model
    {
        return $this->get_model()->find($id);
    }

    public function create(array $data): Model
    {
        $new_record = $this->get_model()->create($data);

        return $new_record;
    }

    public function update(int $id, array $data): ?Model
    {
        $old_record = $this->get_model()->find($id);

        if (! $old_record) {
            return null;
        }

        $old_record->update($data);

        return $old_record;
    }

    public function delete(int $id): bool
    {
        $room_template = $this->get_model()->find($id);

        if (! $room_template) {
            return false;
        }

        return $room_template->delete();
    }
}
