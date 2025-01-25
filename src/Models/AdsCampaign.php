<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdsCampaign extends Model
{
    protected $fillable = [
        'campaign_name',
    ];

    public function addSingle(array $data): self
    {
        return self::updateOrCreate(
            ['campaign_name' => $data['campaign_name']],
            $data
        );
    }

    public function addMultiple(array $data): void
    {
        foreach ($data as $record) {
            $this->addSingle($record);
        }
    }

    public function remove(int $id): bool
    {
        return self::destroy($id) > 0;
    }

    public function updateRecord(int $id, array $data): bool
    {
        return self::where('id', $id)->update($data);
    }
}
